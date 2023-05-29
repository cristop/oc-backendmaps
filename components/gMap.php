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

    /*  FUNCION PARA DEVOLVER INFORMACION DESDE UNA LATITUD Y LONGITUD DADA */
    public static function getAddressFromLatLng($lat, $lng) {
        $settings = Settings::instance();
        $apiKey = $settings->address_map_key; // Reemplaza con tu propia clave de API de Google Maps
    
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=$apiKey";
    
        $response = file_get_contents($url);
    
        if ($response !== false) {
            $data = json_decode($response, true);
    
            if ($data['status'] === 'OK') {
                $formattedAddress = $data['results'][0]['formatted_address'];
                $addressComponents = $data['results'][0]['address_components'];
    
                $address = array(
                    'street_number' => '',
                    'route' => '',
                    'neighborhood' => '',
                    'sublocality' => '',
                    'locality' => '',
                    'administrative_area_level_1' => '',
                    'administrative_area_level_2' => '',
                    'postal_code' => '',
                    'country' => '',
                    'complete' => $formattedAddress
                );
    
                foreach ($addressComponents as $component) {
                    $types = $component['types'];
                    $longName = $component['long_name'];
    
                    if (in_array('street_number', $types)) {
                        $address['street_number'] = $longName;
                    }
                    /* si no encontro number, pone 1 por defecto */
                    if (empty($address['street_number'])) {
                        $address['street_number'] = '1';
                    }
    
                    if (in_array('route', $types)) {
                        $address['route'] = $longName;
                    }
    
                    if (in_array('neighborhood', $types)) {
                        $address['neighborhood'] = $longName;
                    }
    
                    if (in_array('sublocality', $types)) {
                        $address['sublocality'] = $longName;
                    }
    
                    if (in_array('locality', $types)) {
                        $address['locality'] = $longName;
                    }
    
                    if (in_array('administrative_area_level_1', $types)) {
                        $address['administrative_area_level_1'] = $longName;
                    }
    
                    if (in_array('administrative_area_level_2', $types)) {
                        $address['administrative_area_level_2'] = $longName;
                    }
    
                    if (in_array('postal_code', $types)) {
                        $address['postal_code'] = $longName;
                    }
    
                    if (in_array('country', $types)) {
                        $address['country'] = $longName;
                    }
                }
    
                // Comprobar si street_number y route están vacíos
                if (empty($address['route'])) {
                    // Usar el valor de long_name de Trinitat Nova como street si no hay street_number y route
                    foreach ($addressComponents as $component) {
                        if (in_array('establishment', $component['types'])) {
                            $address['route'] = $component['long_name'];
                            break; // Salir del bucle una vez se encuentra la coincidencia
                        }
                    }
                }
    
                return $address;
            } else {
                return array('error' => 'No se encontraron resultados');
            }
        } else {
            return array('error' => 'Error al obtener la dirección');
        }
    }
    /*
    // para llamar getAddressFromLatLng() desde otro plugin:
    use Cristo\BackendMaps\Components\gMap as BackendMaps;
    */
    /*
    $result = BackendMaps::getAddressFromLatLng($ubicacion[0], $ubicacion[1]);

    echo $result['complete'] . " "; // Cadena completa
    echo $result['street_number'] . " "; // Número de calle
    echo $result['route'] . " "; // Calle
    echo $result['neighborhood'] . " "; // Barrio
    echo $result['sublocality'] . " "; // Sublocalidad
    echo $result['locality'] . " "; // Localidad
    echo $result['administrative_area_level_1'] . " "; // Área administrativa de nivel 1 (Estado/Provincia)
    echo $result['administrative_area_level_2'] . " "; // Área administrativa de nivel 2 (Ciudad/Condado)
    echo $result['postal_code'] . " "; // Código postal
    echo $result['country'] . " "; // País
    */
}