<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;


class PluginController extends Controller
{

    public function upload(Request $request)
    {
        $request->validate([
            'plugin_zip' => 'nullable|file|mimes:zip',
            'plugin_url' => 'nullable|url',
        ]);

        if (!$request->hasFile('plugin_zip') && !$request->filled('plugin_url')) {
            return back()->with('error', 'Please upload a ZIP file or provide a GitHub URL.');
        }

        $pluginBase = base_path('plugins');
        $tempFolder = storage_path('app/tmp_plugin_' . Str::random(8));
        mkdir($tempFolder, 0777, true);

        // === 1. HANDLE ZIP UPLOAD ===
        if ($request->hasFile('plugin_zip')) {
            $zipFile = $request->file('plugin_zip');
            $zip = new \ZipArchive;
            if ($zip->open($zipFile->getPathname()) === true) {
                $zip->extractTo($tempFolder);
                $zip->close();
            } else {
                File::deleteDirectory($tempFolder);
                return back()->with('error', 'Failed to unzip uploaded file.');
            }
        }

        // === 2. HANDLE GITHUB ZIP LINK ===
        if ($request->filled('plugin_url')) {
            $url = $request->plugin_url;

            if (Str::endsWith($url, '.git')) {
                $url = str_replace('.git', '/archive/refs/heads/main.zip', $url);
            }

            $tempZipPath = storage_path('app/temp_plugin.zip');
            try {
                file_put_contents($tempZipPath, file_get_contents($url));
            } catch (\Exception $e) {
                File::deleteDirectory($tempFolder);
                return back()->with('error', 'Failed to download ZIP from GitHub.');
            }

            $zip = new \ZipArchive;
            if ($zip->open($tempZipPath) === true) {
                $zip->extractTo($tempFolder);
                $zip->close();
                unlink($tempZipPath);
            } else {
                File::deleteDirectory($tempFolder);
                return back()->with('error', 'Failed to unzip downloaded file.');
            }
        }

        // === 3. FIND plugin.json AND PARSE NAME ===
        $pluginFolder = null;
        $pluginJsonPath = null;
        $pluginName = null;

        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempFolder));

        foreach ($rii as $file) {
            if ($file->getFilename() === 'plugin.json') {
                $pluginJsonPath = $file->getPathname();
                $pluginFolder = dirname($pluginJsonPath);
                break;
            }
        }

        if (!$pluginJsonPath || !file_exists($pluginJsonPath)) {
            File::deleteDirectory($tempFolder);
            return back()->with('error', 'plugin.json not found.');
        }

        $pluginData = json_decode(file_get_contents($pluginJsonPath), true);

        if (!isset($pluginData['name']) || empty($pluginData['name'])) {
            File::deleteDirectory($tempFolder);
            return back()->with('error', 'The plugin.json must contain a "name" field.');
        }

        $pluginName = Str::slug($pluginData['name']);
        $pluginPath = "{$pluginBase}/{$pluginName}";

        $validate = $this->validatePluginStructure($pluginJsonPath,$pluginPath);
        if($validate['status']){
            return back()->with('error', $validate['msg']);
        }
        if (file_exists($pluginPath)) {
            File::deleteDirectory($tempFolder);
            return back()->with('error', "Plugin '{$pluginName}' already exists.");
        }

        // === 4. MOVE/COPY TO /plugins/{plugin-name} ===
        try {
            File::copyDirectory($pluginFolder, $pluginPath); // Safer than rename() on Windows
            File::deleteDirectory($tempFolder);
        } catch (\Exception $e) {
            File::deleteDirectory($tempFolder);
            return back()->with('error', 'Failed to install plugin: ' . $e->getMessage());
        }

        return back()->with('success', "Plugin '{$pluginName}' installed successfully.");
    }

    public function delete(Request $request)
    {
        $request->validate([
            'plugin_name' => 'required|string',
        ]);

        $pluginName = $request->input('plugin_name');
        $pluginPath = base_path('plugins/' . $pluginName); // or storage_path(), etc.

        if (File::exists($pluginPath)) {
            File::deleteDirectory($pluginPath);

            return back()->with('success', "Plugin '{$pluginName}' deleted successfully.");
        } else {
            return back()->with('error', "Plugin '{$pluginName}' not found.");
        }
    }
    public function update(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'name' => 'required|string',
        ]);

        $pluginName = Str::slug($request->input('name'));
        $remoteUrl = $request->input('url');

        // Convert to downloadable zip
        if (Str::endsWith($remoteUrl, '.git')) {
            $remoteUrl = str_replace('.git', '/archive/refs/heads/main.zip', $remoteUrl);
        }

        $tempFolder = storage_path('app/tmp_plugin_update_' . Str::random(8));
        $tempZipPath = $tempFolder . '.zip';

        try {
            File::ensureDirectoryExists($tempFolder);
            file_put_contents($tempZipPath, file_get_contents($remoteUrl));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to download update.'], 500);
        }

        // Unzip
        $zip = new \ZipArchive;
        if ($zip->open($tempZipPath) === true) {
            $zip->extractTo($tempFolder);
            $zip->close();
            unlink($tempZipPath);
        } else {
            File::deleteDirectory($tempFolder);
            return response()->json(['error' => 'Failed to unzip update.'], 500);
        }

        // Find plugin.json
        $pluginJsonPath = null;
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempFolder));
        foreach ($rii as $file) {
            if ($file->getFilename() === 'plugin.json') {
                $pluginJsonPath = $file->getPathname();
                break;
            }
        }

        if (!$pluginJsonPath) {
            File::deleteDirectory($tempFolder);
            return response()->json(['error' => 'Invalid plugin structure.'], 500);
        }

        $newPluginFolder = dirname($pluginJsonPath);
        $pluginPath = base_path('plugins/' . $pluginName);

        // Delete old version
        File::deleteDirectory($pluginPath);

        // Copy new one
        try {
            File::copyDirectory($newPluginFolder, $pluginPath);
            File::deleteDirectory($tempFolder);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to install update.'], 500);
        }

        return response()->json(['success' => 'Plugin updated successfully.']);
    }

    public function checkUpdate(Request $request)
    {

        $request->validate([
            'url' => 'required|url',
            'path' => 'required',
        ]);

        $remoteUrl = $request->input('url');
        $pluginPath = $request->input(key: 'path');
        $localPath = base_path($pluginPath);
        \Log::info($localPath);

        if (!File::exists($localPath)) {
            return response()->json(['error' => 'Local plugin not found.']);
        }

        $localData = json_decode(File::get($localPath), true);
        $localVersion = $localData['version'] ?? '0.0.0';

        // Convert GitHub URL to raw file URL (assumes "main" branch)
        $rawUrl = $this->convertGitUrlToRawJson($remoteUrl);
        \Log::info($rawUrl);
        try {
            $remoteResponse = Http::get($rawUrl);

            if ($remoteResponse->failed()) {
                return response()->json(['error' => 'Failed to fetch remote plugin.json'], 500);
            }

            $remoteJson = $remoteResponse->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Request to remote failed.'], 500);
        }

        $remoteVersion = $remoteJson['version'] ?? '0.0.0';
        \Log::info($localVersion);
        \Log::info($remoteVersion);

        return response()->json([
            'current' => $localVersion,
            'latest' => $remoteVersion,
            'update_available' => version_compare($remoteVersion, $localVersion, '>'),
        ]);
    }

    protected function validatePluginStructure($path, $extractPath)
    {
        $jsonPath = $path;

        if (!File::exists($jsonPath)) {
            // throw new \Exception('Invalid plugin: Missing plugin.json');
            
            return  ['status'=>true, 'msg'=>'Invalid plugin: Missing plugin.json'];
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // throw new \Exception('Invalid JSON format in plugin.json');
            
            return  ['status'=>true, 'msg'=>'Invalid JSON format in plugin.json'];
        }

        // Define the validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'name' => 'required|string|alpha_dash|max:255',
            'enabled' => 'required|boolean',
            'version' => 'required|regex:/^\d+\.\d+\.\d+$/',
            'provider' => 'required|string',
            'description' => 'required|string',
            'gitUrl' => 'required|url',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {

            return ['status'=>true, 'msg'=>'Invalid plugin.json: ' . implode(', ', $validator->errors()->all())];
            // throw new \Exception('Invalid plugin.json: ' . implode(', ', $validator->errors()->all()));
        }

        return ['status'=>false, 'msg'=>'valid']; // optionally return it if you want to use it
    }

    private function convertGitUrlToRawJson(string $gitUrl): string
    {
        // Convert GitHub URL to raw content link
        $gitUrl = str_replace('.git', '', $gitUrl);
        $gitUrl = str_replace('https://github.com/', '', $gitUrl);

        // Default to "main" branch, adjust if your plugin uses a different one
        return "https://raw.githubusercontent.com/{$gitUrl}/main/plugin.json";
    }
}
