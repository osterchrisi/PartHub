<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MouserApiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.mouser.api_key');
    }

    public function searchPartNumber($searchTerm)
    {
        // Construct the URL with the API key as a query parameter
        $url = 'https://api.mouser.com/api/v1/search/partnumber?apiKey=' . $this->apiKey;

        // Send the POST request with the JSON body
        $response = Http::post($url, [
            'SearchByPartRequest' => [
                'mouserPartNumber' => $searchTerm,
                'partSearchOptions' => null //The following values are valid: None | Exact - can use string representations or integer IDs: 1[None] | 2[Exact]
            ]
        ]);

        \Log::info($response->json());

        return $response->json();  // Return the response as a JSON array
    }
}
