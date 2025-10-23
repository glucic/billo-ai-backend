<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GooglePlacesService;

class LocationController extends Controller
{
    private $googlePlaces;

    public function __construct(GooglePlacesService $googlePlaces)
    {
        $this->googlePlaces = $googlePlaces;
    }

    public function suggestions(Request $request)
    {
        $query = $request->input('query');
        $type = $request->input('types', 'address');

        $params = ['input' => $query];

        switch ($type) {
            case 'cities':
                $params['types'] = '(cities)';
                break;
            case 'regions':
                $params['types'] = 'administrative_area_level_1';
                break;
            case 'address':
            default:
                $params['types'] = 'address';
                break;
        }

        $response = $this->googlePlaces->request('autocomplete', $params);
        return $response->json();
    }

    public function details(Request $request)
    {
        $placeId = $request->input('place_id');
        
        $response = $this->googlePlaces->request('details', [
            'place_id' => $placeId,
            'fields' => 'address_component,formatted_address'
        ]);

        $result = $response->json()['result'];
        $components = $result['address_components'];

        $address = [
            'street' => $this->findAddressComponent($components, ['route']),
            'street_number' => $this->findAddressComponent($components, ['street_number']),
            'zip' => $this->findAddressComponent($components, ['postal_code']),
            'city' => $this->findAddressComponent($components, ['locality']),
            'region' => $this->findAddressComponent($components, ['administrative_area_level_1']),
        ];

        // Combine street number and street name
        if ($address['street_number'] && $address['street']) {
            $address['street'] = $address['street_number'] . ' ' . $address['street'];
        }
        unset($address['street_number']);

        return response()->json($address);
    }

    private function findAddressComponent($components, $types)
    {
        foreach ($components as $component) {
            if (count(array_intersect($component['types'], $types)) > 0) {
                return $component['long_name'];
            }
        }
        return '';
    }
}