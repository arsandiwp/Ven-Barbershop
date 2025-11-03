<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $totalCustomers = User::where('role_id', 3)->count(); // role_id 3 = customer
        $totalBarbers = User::where('role_id', 2)->count(); // role_id 2 = barber
        $totalServices = Service::count();
        $totalReservations = Reservation::count();

        $today = Carbon::today();
        $todayReservations = Reservation::whereDate('reservation_time', $today)->count();

        $statusCount = Reservation::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'total_customers' => $totalCustomers,
            'total_barbers' => $totalBarbers,
            'total_services' => $totalServices,
            'total_reservations' => $totalReservations,
            'today_reservations' => $todayReservations,
            'reservations_by_status' => $statusCount,
        ]);
    }

    public function reservationsTrend()
    {
        $startDate = Carbon::today()->subDays(6); // 7 hari terakhir
        $endDate = Carbon::today();

        $trend = Reservation::selectRaw('DATE(reservation_time) as date, COUNT(*) as total')
            ->whereBetween('reservation_time', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Pastikan semua hari ada meskipun 0
        $dates = [];
        $totals = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startDate->copy()->addDays($i)->toDateString();
            $dates[] = $day;
            $totals[] = $trend[$day] ?? 0;
        }

        return response()->json([
            'dates' => $dates,
            'totals' => $totals,
        ]);
    }
}
