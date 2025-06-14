<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TripAdvisorNewService
{
    protected $baseUrl;
    protected $apiKey;
    protected $apiHost;
    protected $transformer;

    public function __construct(TripAdvisorTransformerService $transformer)
    {
        $this->baseUrl = config('services.tripadvisor.baseUrl');
        $this->apiKey = config('services.tripadvisor.api_key');
        $this->apiHost = config('services.tripadvisor.api_host');
        $this->transformer = $transformer;
    }

    public function autoCompleteLocation(string $query)
    {
        try {
            $response = Http::withHeaders([
                'x-rapidapi-key' => 'da3267b4aamsh14bc08362afe60ep1f3a0cjsnd26bc72eb755',
                'x-rapidapi-host' => 'tripadvisor-com1.p.rapidapi.com',
            ])->get('https://tripadvisor-com1.p.rapidapi.com/auto-complete', [
                        'query' => $query
                    ]);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch auto-complete data: ' . $response->status() . ' - ' . $response->body());
            }

            $results = $response->json();

            $transformedResults = $this->transformer->transformLocationResults($results);

            return $transformedResults;
        } catch (\Exception $e) {
            // More detailed logging for debugging
            \Log::error('TripAdvisor API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function attraction(array $params)
    {
        $requiredParams = ['geoId', 'startDate', 'endDate'];
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                throw new \Exception("Missing required parameter: {$param}");
            }
        }

        $response = Http::withHeaders([
            'X-RapidAPI-Key' => 'da3267b4aamsh14bc08362afe60ep1f3a0cjsnd26bc72eb755',
            'X-RapidAPI-Host' => 'tripadvisor-com1.p.rapidapi.com',
        ])->get('https://tripadvisor-com1.p.rapidapi.com/attractions/search', $params);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch attraction data: ' . $response->body());
        }

        $results = $response->json();

        $transformedResults = $this->transformer->processAttractionData($results);

        return $transformedResults;
    }

    public function searchHotels(array $params)
    {
        $requiredParams = ['geoId', 'checkIn', 'checkOut'];
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                throw new \Exception("Missing required parameter: {$param}");
            }
        }

        // $params['pageNumber'] = $params['pageNumber'] ?? '1';

        $response = Http::withHeaders([
            'X-RapidAPI-Key' => $this->apiKey,
            'X-RapidAPI-Host' => $this->apiHost,
        ])->get('https://tripadvisor16.p.rapidapi.com/api/v1/hotels/searchHotels', $params);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch hotel data: ' . $response->body());
        }

        $results = $response->json();

        return $results;
    }

    public function searchRestaurant(array $params)
    {
        $requiredParams = ['locationId'];
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                throw new \Exception("Missing required parameter: {$param}");
            }
        }

        // $params['page'] = $params['page'] ?? '1';       

        $response = Http::withHeaders([
            'X-RapidAPI-Key' => $this->apiKey,
            'X-RapidAPI-Host' => $this->apiHost,
        ])->get('https://tripadvisor16.p.rapidapi.com/api/v1/restaurant/searchRestaurants', $params);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch restaurant data: ' . $response->body());
        }

        $results = $response->json();

        return $results;
    }

    public function searchVacationRental(array $params)
    {
        $requiredParams = ['geoId', 'arrival', 'departure', 'sortOrder'];
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                throw new \Exception("Missing required parameter: {$param}");
            }
        }

        // $params['page'] = $params['page'] ?? '1';       

        $response = Http::withHeaders([
            'X-RapidAPI-Key' => $this->apiKey,
            'X-RapidAPI-Host' => $this->apiHost,
        ])->get($this->baseUrl . '/rentals/rentalSearch', $params);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch rentals data: ' . $response->body());
        }

        $results = $response->json();

        return $results;
    }

    public function searchAirport(array $params)
    {
        $requiredParams = ['query'];
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                throw new \Exception("Missing required parameter: {$param}");
            }
        }

        // $params['page'] = $params['page'] ?? '1';       

        $response = Http::withHeaders([
            'X-RapidAPI-Key' => $this->apiKey,
            'X-RapidAPI-Host' => $this->apiHost,
        ])->get($this->baseUrl . '/flights/searchAirport', $params);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch flights data: ' . $response->body());
        }

        $results = $response->json();

        return $results;
    }
}