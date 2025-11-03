<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Mail\ReservationMail;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\User;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mail;

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

    public function customerBookings(Request $request)
    {
        $user = (object) $request->_session;

        $bookings = Reservation::with('barber')
            ->where('user_id', $user->id)
            ->orderBy('reservation_time', 'desc')
            ->get()
            ->map(function ($reservation) {
                $serviceIds = json_decode($reservation->service_id, true);

                if (!is_array($serviceIds)) {
                    $serviceIds = explode(',', str_replace(['[', ']', '"'], '', $reservation->service_id));
                }

                $serviceIds = array_map('intval', $serviceIds);

                $serviceNames = Service::whereIn('id', $serviceIds)->pluck('name')->toArray();

                return [
                    'id' => $reservation->id,
                    'barber_name' => $reservation->barber ? $reservation->barber->name : 'Unknown',
                    'service_name' => implode(", ", $serviceNames),
                    'date' => \Carbon\Carbon::parse($reservation->reservation_time)->format('Y-m-d'),
                    'time' => \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i'),
                    'status' => $reservation->status,
                ];
            });

        return response()->json($bookings);
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'service_id' => 'required|array',
        ]);

        $barberId = $request->barber_id;
        $date = $request->date;

        // ✅ pastikan barber valid
        $barber = User::where('id', $barberId)
            ->where('role_id', 2)
            ->firstOrFail();

        // Hitung total durasi service yang dipilih
        $serviceDurations = Service::whereIn('id', $request->service_id)->pluck('duration');
        $totalDuration = $serviceDurations->sum();

        $startTime = Carbon::parse("$date 09:00:00");
        $endTime = Carbon::parse("$date 18:00:00");

        $interval = 30; // slot dasar
        $allSlots = [];

        while ($startTime < $endTime) {
            $allSlots[] = $startTime->format('H:i');
            $startTime->addMinutes($interval);
        }

        // ✅ Ambil reservasi yang ada
        $reservations = Reservation::where('barber_id', $barberId)
            ->whereDate('reservation_time', $date)
            ->where('status', '!=', 'Cancelled')
            ->get();

        $availableSlots = [];

        foreach ($allSlots as $slot) {
            $slotStart = Carbon::parse("$date $slot");
            $slotEnd = $slotStart->copy()->addMinutes($totalDuration);

            // Cek overlap dengan reservasi
            $hasConflict = false;
            foreach ($reservations as $reservation) {
                $resStart = Carbon::parse($reservation->reservation_time);
                $resEnd = $resStart->copy()->addMinutes($reservation->duration ?? 30);

                if ($slotStart < $resEnd && $slotEnd > $resStart) {
                    $hasConflict = true;
                    break;
                }
            }

            if (!$hasConflict) {
                $availableSlots[] = $slot;
            }
        }

        return response()->json([
            'barber' => $barber->name,
            'date' => $date,
            'duration' => $totalDuration,
            'available_slots' => $availableSlots,
        ]);
    }

    // public function get($id)
    // {
    //     // $data = $this->reservationService->get($id);
    //     // $reservation = Reservation::with(['barber', 'service'])->findOrFail($id);

    //     $reservation = Reservation::findOrFail($id);

    //     $serviceIds = json_decode($reservation->service_id, true) ?? [];
    //     $services = Service::whereIn('id', $serviceIds)->pluck('name');


    //     $reservation->service_names = $services;

    //     return ResponseHelper::get($reservation);
    // }

    public function get($id)
    {
        $reservation = Reservation::with('barber')->findOrFail($id);

        $serviceIds = json_decode($reservation->service_id, true) ?? [];
        $services = Service::whereIn('id', $serviceIds)->pluck('name')->toArray();

        $dateTime = \Carbon\Carbon::parse($reservation->reservation_time);

        // return ResponseHelper::get([
        //     'id' => $reservation->id,
        //     'barber_name' => $reservation->barber->name ?? null,
        //     'service_names' => implode(', ', $services), // << gabung jadi string
        //     'price' => $reservation->price,
        //     'date' => $dateTime->format('Y-m-d'),
        //     'time' => $dateTime->format('H:i'),
        //     'status' => $reservation->status,
        // ]);

        return ResponseHelper::get([
            'id' => $reservation->id,
            'barber_id' => $reservation->barber_id, // tambahkan ini
            'barber_name' => $reservation->barber->name ?? null,
            'service_id' => $serviceIds, // tambahkan ini biar Vue bisa preselect
            'service_names' => implode(', ', $services),
            'price' => $reservation->price,
            'date' => $dateTime->format('Y-m-d'),
            'time' => $dateTime->format('H:i'),
            'status' => $reservation->status,
        ]);

    }




    public function create(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:users,id',
            'service_id' => 'required|array',
            'reservation_time' => 'required|date',
        ]);

        // $user_id = $request->_session['id'];
        $user = (object) $request->_session;

        $barberId = $request->barber_id;
        $startTime = Carbon::parse($request->reservation_time);
        $serviceIds = $request->service_id;

        return DB::transaction(function () use ($barberId, $startTime, $serviceIds, $user) {
            $services = Service::whereIn('id', $serviceIds)->get();
            $totalDuration = $services->sum('duration');
            $totalPrice = $services->sum('price');

            $endTime = $startTime->copy()->addMinutes($totalDuration);

            $conflict = Reservation::where('barber_id', $barberId)
                ->where('status', '!=', 'Cancelled')
                ->whereDate('reservation_time', $startTime->toDateString())
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where('reservation_time', '<', $endTime)
                        ->whereRaw('DATE_ADD(reservation_time, INTERVAL duration MINUTE) > ?', [$startTime]);
                })
                ->exists();

            if ($conflict) {
                throw new CustomException("Barber tidak tersedia pada: " . $startTime->format('H:i'));
            }


            $reservation = Reservation::create([
                'user_id' => $user->id,
                'barber_id' => $barberId,
                'reservation_time' => $startTime,
                'duration' => $totalDuration,
                'price' => $totalPrice,
                'service_id' => json_encode($serviceIds), // ✅ simpan semua service_id
                'status' => 'Confirmed',
            ]);

            $customer = \App\Models\User::find($user->id);
            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(new ReservationMail($reservation, "created"));
            }

            // kirim email ke barber
            $barber = \App\Models\User::find($barberId);
            if ($barber && $barber->email) {
                Mail::to($barber->email)->send(new ReservationMail($reservation, "new booking"));
            }

            return ResponseHelper::create([
                'reservation_id' => $reservation->id,
                'reservation_time' => $reservation->reservation_time,
                'total_duration' => $totalDuration,
                'total_price' => $totalPrice,
                'services' => $services->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->name,
                        'price' => $s->price,
                        'duration' => $s->duration,
                    ];
                }),
            ]);
        });
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Cancelled,Completed',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->status = $request->status;
        $reservation->save();

        $customer = \App\Models\User::find($reservation->user_id);
        if ($customer && $customer->email) {
            Mail::to($customer->email)->send(new ReservationMail($reservation, "updated"));
        }

        return ResponseHelper::put([
            'reservation_id' => $reservation->id,
            'status' => $reservation->status,
        ]);
    }


    public function update($id, Request $request)
    {
        $data = $request->only(Schema::getColumnListing('reservations'));

        return DB::transaction(function () use ($id, $data) {
            $updated = $this->reservationService->update($id, $data);
            return ResponseHelper::put();
        });
    }

    // public function update($id, Request $request)
    // {
    //     $data = $request->only(Schema::getColumnListing('reservations'));

    //     // ✅ Ubah service_id jadi string JSON jika array
    //     if (isset($data['service_id']) && is_array($data['service_id'])) {
    //         $data['service_id'] = json_encode($data['service_id']);

    //         // 🔁 Hitung ulang total_price dan duration
    //         $serviceIds = $request->input('service_id'); // Ambil array asli

    //         $services = \App\Models\Service::whereIn('id', $serviceIds)->get();
    //         $totalDuration = $services->sum('duration');
    //         $totalPrice = $services->sum('price');

    //         $data['duration'] = $totalDuration;
    //         $data['price'] = $totalPrice;
    //     }

    //     return DB::transaction(function () use ($id, $data) {
    //         $updated = $this->reservationService->update($id, $data);
    //         return ResponseHelper::put([
    //             'reservation_id' => $id,
    //             'updated_fields' => $data, // ✅ bisa bantu debugging
    //         ]);
    //     });
    // }

    // public function update($id, Request $request)
    // {
    //     $data = $request->only(Schema::getColumnListing('reservations'));

    //     $serviceIds = $request->input('service_id');

    //     if (isset($serviceIds) && is_array($serviceIds)) {
    //         // langsung assign array, jangan json_encode
    //         $data['service_id'] = $serviceIds;

    //         $services = \App\Models\Service::whereIn('id', $serviceIds)->get();
    //         $totalDuration = $services->sum('duration');
    //         $totalPrice = $services->sum('price');

    //         $data['duration'] = $totalDuration;
    //         $data['price'] = $totalPrice;
    //     }

    //     return DB::transaction(function () use ($id, $data) {
    //         $updated = $this->reservationService->update($id, $data);
    //         return ResponseHelper::put([
    //             'reservation_id' => $id,
    //             'updated_fields' => $data,
    //         ]);
    //     });
    // }

    // public function update($id, Request $request)
    // {
    //     $data = $request->only(Schema::getColumnListing('reservations'));

    //     $serviceIds = $request->input('service_id');

    //     if (isset($serviceIds) && is_array($serviceIds)) {
    //         // ✅ Explicit convert ke string JSON
    //         $data['service_id'] = json_encode($serviceIds);

    //         // 🔍 Debug log
    //         \Log::info('Service ID before save:', [
    //             'original' => $serviceIds,
    //             'encoded' => $data['service_id']
    //         ]);

    //         $services = \App\Models\Service::whereIn('id', $serviceIds)->get();
    //         $totalDuration = $services->sum('duration');
    //         $totalPrice = $services->sum('price');

    //         $data['duration'] = $totalDuration;
    //         $data['price'] = $totalPrice;
    //     }

    //     return DB::transaction(function () use ($id, $data) {
    //         // 🔍 Debug sebelum update
    //         \Log::info('Data to update:', $data);

    //         $updated = $this->reservationService->update($id, $data);

    //         // 🔍 Debug setelah update - cek data di DB
    //         $reservation = \App\Models\Reservation::find($id);
    //         \Log::info('Data after update:', [
    //             'service_id' => $reservation->service_id,
    //             'type' => gettype($reservation->service_id)
    //         ]);

    //         return ResponseHelper::put([
    //             'reservation_id' => $id,
    //             'updated_fields' => $data,
    //         ]);
    //     });
    // }




    public function delete($id)
    {
        $this->reservationService->delete($id);
        return ResponseHelper::delete();
    }
}
