<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Service;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReservationController extends Controller
{
    private $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function getPaginate(Request $request)
    {
        $perPage = $request->per_page ?? 25;
        $keyword = $request->keyword;

        $data = $this->reservationService->getPaginate($perPage, $keyword);
        return $data;
    }

    public function listAll(Request $request)
    {
        return $this->reservationService->getAll();
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $slots = $this->reservationService->getAvailableSlots($request->barber_id, $request->date);

        return response()->json([
            'date' => $request->date,
            'barber_id' => $request->barber_id,
            'available_slots' => $slots,
        ]);
    }


    public function get($id)
    {
        $data = $this->reservationService->get($id);
        return ResponseHelper::get($data);
    }

    public function create(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:users,id',
            'service_id' => 'required', // bisa int atau array
            'reservation_time' => 'required|date',
        ]);

        $barberId = $request->barber_id;
        $startTime = Carbon::parse($request->reservation_time);
        $userId = auth()->id();

        return DB::transaction(function () use ($request, $barberId, $startTime, $userId) {
            try {
                $results = [];
                $serviceIds = is_array($request->service_id) ? $request->service_id : [$request->service_id];
                $currentTime = $startTime;

                foreach ($serviceIds as $serviceId) {
                    $service = Service::findOrFail($serviceId);
                    $endTime = $currentTime->copy()->addMinutes($service->duration);

                    // Cek konflik jadwal barber
                    $conflict = Reservation::where('barber_id', $barberId)
                        ->where('status', '!=', 'Cancelled')
                        ->where(function ($q) use ($currentTime, $endTime) {
                            $q->whereBetween('reservation_time', [$currentTime, $endTime])
                                ->orWhereRaw('? BETWEEN reservation_time AND DATE_ADD(reservation_time, INTERVAL duration MINUTE)', [$currentTime]);
                        })
                        ->exists();

                    if ($conflict) {
                        throw new CustomException("Barber tidak tersedia pada: " . $currentTime->format('H:i'));
                    }

                    $reservation = Reservation::create([
                        'user_id' => $userId,
                        'barber_id' => $barberId,
                        'service_id' => $serviceId,
                        'reservation_time' => $currentTime,
                        'duration' => $service->duration,
                        'price' => $service->price,
                        'status' => 'Confirmed',
                    ]);

                    $results[] = [
                        'service_id' => $serviceId,
                        'reservation_time' => $currentTime->toDateTimeString(),
                    ];

                    $currentTime = $endTime; // lanjut ke waktu berikutnya
                }

                return ResponseHelper::create($results);
            } catch (\Exception $e) {
                throw new CustomException($e->getMessage());
            }
        });
    }


    public function update($id, Request $request)
    {
        $data = $request->only(Schema::getColumnListing('reservations'));

        return DB::transaction(function () use ($id, $data) {
            $updated = $this->reservationService->update($id, $data);
            return ResponseHelper::put($updated);
        });
    }

    public function delete($id)
    {
        $this->reservationService->delete($id);
        return ResponseHelper::delete();
    }
}
