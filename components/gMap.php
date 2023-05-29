<?php namespace Cristo\BackendMaps\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\CodeBase;

use Cristo\BackendMaps\Models\Settings;

class gMap extends ComponentBase
{
    public $longitude;
    public $latitude;
    public $apiKey;
    public $address_map;
    public $final_add;

    public function componentDetails()
    {
        return [
            'name' => 'cristo.backendmaps::lang.component.name',
            'description' => 'cristo.backendmaps::lang.component.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'address_map' => [
                'title'             => 'cristo.backendmaps::lang.component.width',
                'default'           => '',
                'description'       => 'Latitude and Longitude',
                'type'              => 'string',
            ],
            'width' => [
                'title'             => 'cristo.backendmaps::lang.component.width',
                'default'           => '100%',
                'description'       => 'cristo.backendmaps::lang.component.width_description',
                'type'              => 'string',
            ],
            'height' => [
                'title'             => 'cristo.backendmaps::lang.component.height',
                'default'           => '350px',
                'description'       => 'cristo.backendmaps::lang.component.height_description',
                'type'              => 'string',
            ],
            'mapTypeId' => [
                'title'             => 'cristo.backendmaps::lang.component.mapType',
                'default'           => 'ROADMAP',
                'type'              => 'dropdown',
                'options'           => ['ROADMAP'=>'Roadmap', 'SATELLITE'=>'Satellite', 'HYBRID'=>'Hybrid', 'TERRAIN'=>'Terrain']
            ],
            'zoom' => [
                'title'             => 'cristo.backendmaps::lang.component.zoom',
                'default'           => 12,
                'type'              => 'string',
            ],
            'showMarker' => [
                'title'             => 'cristo.backendmaps::lang.component.showMarker',
                'default'           => 'true',
                'type'              => 'checkbox',
            ],
            'animateMarker' => [
                'title'             => 'cristo.backendmaps::lang.component.animateMarker',
                'default'           => 'true',
                'type'              => 'checkbox',
            ]        
        ];
    }

    public function onRender()
    {
        $settings = Settings::instance();
        
        //address_map
        if($this->property('address_map') == ''){
            // Latitude and longitude
            $lat_log = $settings->default_location;            
            $this->setProperty('zoom', 11);
        }else{
            $lat_log = $this->property('address_map');
        }
        
        $address_map = explode(',', $lat_log);

        $this->latitude = !empty($address_map[0]) ? $address_map[0] : 0;
        $this->longitude = !empty($address_map[1]) ? $address_map[1] : 0;

        // Google Maps API KEY
        $this->apiKey = $settings->address_map_key;
    }

    public function getAddressFromLatLng($lat, $lng) {
        $settings = Settings::instance();
        $apiKey = $settings->address_map_key; // Reemplaza con tu propia clave de API de Google Maps
        
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=$apiKey";
        
        $response = file_get_contents($url);
        
        if ($response !== false) {
          $data = json_decode($response, true);
          
          if ($data['status'] === 'OK') {
            $address = $data['results'][0]['formatted_address'];
            return $address;
          } else {
            return 'No se encontraron resultados';
          }
        } else {
          return 'Error al obtener la dirección';
        }
      }
}