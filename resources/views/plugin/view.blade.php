<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ 'Plugin' }}
        </h2>

    </x-slot>

    {{-- Flash messages --}}
    @if (session('success'))
        <div id="success-alert"
            class="absolute top-0 right-0 bg-green-600 shadow mt-4 p-4 dark:bg-green-600 hover:bg-green-600">
            <div class="shadow text-green-800 dark:text-gray-200">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="error-alert" class="absolute top-0 right-0 bg-red-600 shadow mt-4 p-4 dark:bg-red-600 hover:bg-red-600">
            <div class="shadow text-red-800 dark:text-gray-200">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const successAlert = document.getElementById('success-alert');
                if (successAlert) successAlert.style.display = 'none';

                const errorAlert = document.getElementById('error-alert');
                if (errorAlert) errorAlert.style.display = 'none';
            }, 13000); // 3000ms = 3 seconds
        });
    </script>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('plugin.upload') }}" method="POST" enctype="multipart/form-data"
                class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="block font-semibold dark:text-gray-200">Upload Plugin ZIP:</label>
                    <input type="file" name="plugin_zip" class="border p-2 rounded w-full dark:text-gray-400">
                </div>

                <div class="text-center font-semibold dark:text-gray-400">OR</div>

                <div>
                    <label class="block font-semibold dark:text-gray-200">Install via GitHub ZIP URL:</label>
                    <input type="text" name="plugin_url"
                        placeholder="https://github.com/user/repo/archive/refs/heads/main.zip"
                        class="border p-2 rounded w-full">
                </div>

                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Install Plugin
                </button>
            </form>

            <div class="mt-4 space-y-2">
                @foreach (\App\PluginHook::getPlugin() as $plugin)
                    <div class="p-4 border rounded shadow bg-white dark:bg-gray-800 dark:border-gray-700">
                        <h3 class="text-lg font-semibold dark:text-gray-300">{{ $plugin['title'] }} <span
                                class="text-sm text-gray-400 dark:text-gray-400">v{{ $plugin['version'] }}</span></h3>
                        <p class="text-gray-500">{{ $plugin['description'] }}</p>

                        <div class="mt-2">
                            <a href="{{ $plugin['gitUrl'] }}" target="_blank"
                                class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                                Info
                            </a>
                            <div id="plugin-update-status" class="inline-block text-sm text-gray-900 dark:text-gray-200 px-4 py-2 rounded ">
                                Checking for updates...
                            </div>

                            <script>
                                const pluginGitUrl = @json($plugin['gitUrl']);
                                const pathPlugin = @json($plugin['name']);

                                fetch('/check-plugin-update', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            url: pluginGitUrl,
                                            path: `plugins/${pathPlugin}/plugin.json` // Correct full path here
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {

                                        const el = document.getElementById('plugin-update-status');
                                        if (data.update_available) {
                                            // Show "Update" button or action
                                            el.innerHTML =
                                                `New version ${data.latest} available! <button onclick="updatePlugin({{ json_encode($plugin['gitUrl']) }}, {{ json_encode($plugin['name']) }})" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>`;
                                        } else {

                                            el.innerHTML = `You're up to date (v${data.current}).`;
                                        }
                                    })
                                    .catch(error => console.error('Error checking plugin update:', error));
                            </script>



                            <form action="{{ route('plugin.delete') }}" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="plugin_name" value="{{ $plugin['name'] }}">
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700">Uninstall</button>
                            </form>
                        </div>
                    </div>
                @endforeach
                
<script>
    function updatePlugin(gitUrl, name) {
        if (!confirm(`Update ${name}?`)) return;

        fetch('/plugin-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                url: gitUrl,
                name: name
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.success);
                location.reload();
            } else {
                alert(data.error || 'Update failed.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Something went wrong.');
        });
    }
</script>   
            </div>

        </div>
    </div>
</x-app-layout>
