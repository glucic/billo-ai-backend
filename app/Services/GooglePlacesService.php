<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GooglePlacesService
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.places_api_key');
    }

    public function request($endpoint, $params = [])
    {
        $params['key'] = $this->apiKey;

        // For development, disable SSL verification
        return Http::withoutVerifying()
            ->get("https://maps.googleapis.com/maps/api/place/{$endpoint}/json", $params);
    }
}