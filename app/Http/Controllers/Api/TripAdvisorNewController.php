<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TripAdvisorNewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TripAdvisorNewController extends Controller
{
    protected $tripAdvisorNewService;

    public function __construct(TripAdvisorNewService $tripAdvisorNewService)
    {
        $this->tripAdvisorNewService = $tripAdvisorNewService;
    }

    public function autoCompleteLocation(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        try {
            $result = $this->tripAdvisorNewService->autoCompleteLocation($request->input('query'));
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAttractions(Request $request)
    {
        try {
            $request->validate([
                'geoId' => 'required|string',
                'startDate' => 'required|date_format:Y-m-d',
                'endDate' => 'required|date_format:Y-m-d|after:startDate',
                // 'language' => 'sometimes|string',
                // 'currency' => 'sometimes|string',
                // 'page' => 'sometimes|integer|min:1',
                // 'rooms' => 'sometimes|integer|min:1',
                // 'adults' => 'sometimes|integer|min:1',
                // 'children' => 'sometimes|integer|min:0',
                // 'sort' => 'sometimes|string|in:BEST_VALUE,PRICE_LOW_TO_HIGH,PRICE_HIGH_TO_LOW,RATING_HIGH_TO_LOW,POPULARITY,DISTANCE'
            ]);

            $result = $this->tripAdvisorNewService->attraction($request->all());
            return response()->json($result);

            // $result = $this->attraction($params);

            // return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data atraksi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function searchHotels(Request $request)
    {
        $request->validate([
            'geoId' => 'required|string',
            'checkIn' => 'required|date_format:Y-m-d',
            'checkOut' => 'required|date_format:Y-m-d|after:checkIn',
            // 'language' => 'sometimes|string',
            // 'currency' => 'sometimes|string',
            // 'page' => 'sometimes|integer|min:1',
            // 'rooms' => 'sometimes|integer|min:1',
            // 'adults' => 'sometimes|integer|min:1',
            // 'children' => 'sometimes|integer|min:0',
            // 'sort' => 'sometimes|string|in:BEST_VALUE,PRICE_LOW_TO_HIGH,PRICE_HIGH_TO_LOW,RATING_HIGH_TO_LOW,POPULARITY,DISTANCE'
        ]);

        try {
            $result = $this->tripAdvisorNewService->searchHotels($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function searchRestaurant(Request $request)
    {
        $request->validate([
            'locationId' => 'required|string',
            // 'page' => 'sometimes|integer|min:1',
        ]);

        try {
            $result = $this->tripAdvisorNewService->searchRestaurant($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function searchVacationRental(Request $request)
    {
        $request->validate([
            'geoId' => 'required|string',
            'arrival' => 'required|date_format:Y-m-d',
            'departure' => 'required|date_format:Y-m-d|after:checkIn',
            'sortOrder' => 'required|string|in:POPULARITY,PRICELOW,PRICEHIGH,TRAVELERRATINGHIGH,BEDHIGH,BEDLOW,REVIEWSHIGH'
            // 'page' => 'sometimes|integer|min:1',
        ]);

        try {
            $result = $this->tripAdvisorNewService->searchVacationRental($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function searchAirport(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        try {
            $result = $this->tripAdvisorNewService->searchAirport($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}