<?php

namespace App;

class PluginHook
{
    protected static array $pluginList = [];
    protected static array $dashboardButtons = [];
    protected static array $topMenuList = [];
    protected static array $profileMenuList = [];

    public static function registerPlugin(string $title, string $name, string $version, string $description, string $gitUrl)
    {
        self::$pluginList[] = [
            'title'=>$title,
            'name'=>$name,
            'version'=>$version,
            'description' => $description,
            'gitUrl'=> $gitUrl
        ];
    }

    public static function  getPlugin(): array
    {
        return self::$pluginList;
    }
    public static function addDashboardButton(string $html)
    {
        self::$dashboardButtons[] = $html;
    }

    public static function getDashboardButtons(): array
    {
        return self::$dashboardButtons;
    }


    public static function addTopMenu(string $title,string $route)
    {
        self::$topMenuList[] = ['title'=>$title, 'route'=>$route];
    }

    public static function getTopMenu(): array
    {
        return self::$topMenuList;
    }
    public static function addProfileMenu(string $html)
    {
        self::$profileMenuList[] = $html;
    }

    public static function getProfileMenu(): array
    {
        return self::$profileMenuList;
    }
}
