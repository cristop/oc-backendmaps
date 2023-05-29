<?php namespace Cristo\BackendMaps;

use System\Classes\PluginBase;
use System\Classes\PluginManager;
use System\Classes\SettingsManager;
use Cristo\BackendMaps\Models\Settings;

/**
 * Backend Google Maps Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'cristo.backendmaps::lang.plugin.name',
            'description' => 'cristo.backendmaps::lang.plugin.description',
            'author'      => 'Cristo',
            'icon'        => 'icon-search'
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'cristo.backendmaps::lang.settings.label',
                'description' => 'cristo.backendmaps::lang.settings.description',
                'permissions' => ['cristo.backendmaps.manage'],
                'icon'        => 'icon-map-marker',
                'class'       => 'Cristo\backendmaps\Models\Settings',
                'order'       => 602
            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'cristo.backendmaps.manage' => [
                'label' => 'cristo.backendmaps::lang.permissions.label',
                'tab' => 'cristo.backendmaps::lang.permissions.tab'
            ]
        ];
    }

    public function registerComponents()
    {
        return [
            'Cristo\BackendMaps\Components\gMap' => 'gmap'
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'Cristo\BackendMaps\FormWidgets\BackendMaps' => [
                'label' => 'cristo.backendmaps::lang.widget.name',
                'code'  => 'backendmaps'
            ]
        ];
    }

    public function getApiKey(){
        $settings = Settings::instance();

        return $settings->address_map_key;
    }
}
