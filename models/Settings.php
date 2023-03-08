<?php

namespace Cristo\BackendMaps\Models;

use Model;

class Settings extends Model
{

    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'cristo_backendmaps_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
} 