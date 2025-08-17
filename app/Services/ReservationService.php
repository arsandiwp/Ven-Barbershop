<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationService
{
    const PRIMARY_KEY = 'id';
    const DEFAULT_PER_PAGE = 25;

    private $reservation;
    private $service;

    public function __construct(Reservation $reservation, Service $service)
    {
        $this->reservation = $reservation;
        $this->service = $service;
    }

    public function getPaginate($perPage = null, $keyword = null)
    {
        $query = $this->reservation->with(['user', 'barber', 'service']);

        if ($keyword) {
            $query->whereHas('service', function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }

        return $query->paginate($perPage ?? self::DEFAULT_PER_PAGE);
    }

    public function getAll()
    {
        return $this->reservation->with(['user', 'barber', 'service'])->get();
    }

    public function getAvailableSlots($barberId, $date)
    {
        $workStart = Carbon::parse("$date 09:00:00");
        $workEnd = Carbon::parse("$date 18:00:00");

        // Ambil semua reservasi barber di tanggal itu
        $reservations = $this->reservation
            ->where('barber_id', $barberId)
            ->whereDate('reservation_time', $date)
            ->where('status', '!=', 'Cancelled')
            ->with('service')
            ->get();

        // Buat array slot kosong, mulai dari jam kerja sampai habis
        $slots = [];

        $slotStart = $workStart->copy();

        while ($slotStart->lt($workEnd)) {
            // Asumsi slot minimal 15 menit, bisa disesuaikan
            $slotEnd = $slotStart->copy()->addMinutes(15);

            // Cek tumpang tindih dengan reservasi yang ada
            $overlap = $reservations->first(function ($reservation) use ($slotStart, $slotEnd) {
                $resStart = Carbon::parse($reservation->reservation_time);
                $resEnd = $resStart->copy()->addMinutes($reservation->service->duration);
                return $slotStart->lt($resEnd) && $slotEnd->gt($resStart);
            });

            if (!$overlap) {
                $slots[] = $slotStart->format('H:i');
            }

            $slotStart->addMinutes(15);
        }

        return $slots;
    }


    public function get($id)
    {
        return $this->reservation->with(['user', 'barber', 'service'])->where(self::PRIMARY_KEY, $id)->first();
    }

    // public function create($data)
    // {
    //     $userId = Auth::id();
    //     $reservationTime = Carbon::parse($data['reservation_time']);

    //     // Validasi H-3
    //     if ($reservationTime->lt(now()->addHours(3))) {
    //         throw new \Exception('Reservasi minimal dilakukan 3 jam sebelum waktu yang dipilih.');
    //     }

    //     // Ambil durasi service
    //     $service = $this->service->findOrFail($data['service_id']);
    //     $endTime = $reservationTime->copy()->addMinutes($service->duration);

    //     // Cek konflik jadwal dengan barber
    //     $conflict = $this->reservation->where('barber_id', $data['barber_id'])
    //         ->where('status', '!=', 'Cancelled')
    //         ->where(function ($q) use ($reservationTime, $endTime) {
    //             $q->whereBetween('reservation_time', [$reservationTime, $endTime])
    //                 ->orWhereRaw('? BETWEEN reservation_time AND DATE_ADD(reservation_time, INTERVAL duration MINUTE)', [$reservationTime]);
    //         })
    //         ->exists();

    //     if ($conflict) {
    //         throw new \Exception('Barber tidak tersedia pada waktu tersebut.');
    //     }

    //     return $this->reservation->create([
    //         'user_id' => $userId,
    //         'barber_id' => $data['barber_id'],
    //         'service_id' => $data['service_id'],
    //         'reservation_time' => $reservationTime,
    //         'status' => 'Confirmed',
    //     ]);
    // }



    public function create($data)
    {
        $userId = Auth::id();
        $reservationTime = Carbon::parse($data['reservation_time']);

        // Validasi H-3
        if ($reservationTime->lt(now()->addHours(3))) {
            throw new \Exception('Reservasi minimal dilakukan 3 jam sebelum waktu yang dipilih.');
        }

        $service = $this->service->findOrFail($data['service_id']);
        $endTime = $reservationTime->copy()->addMinutes($service->duration);

        // Cek konflik jadwal barber
        $conflict = $this->reservation->where('barber_id', $data['barber_id'])
            ->where('status', '!=', 'Cancelled')
            ->where(function ($q) use ($reservationTime, $endTime) {
                $q->whereBetween('reservation_time', [$reservationTime, $endTime])
                    ->orWhereRaw('? BETWEEN reservation_time AND DATE_ADD(reservation_time, INTERVAL duration MINUTE)', [$reservationTime]);
            })
            ->exists();

        if ($conflict) {
            throw new \Exception('Barber tidak tersedia pada waktu tersebut.');
        }

        return $this->reservation->create([
            'user_id' => $userId,
            'barber_id' => $data['barber_id'],
            'service_id' => $data['service_id'],
            'reservation_time' => $reservationTime,
            'status' => 'Confirmed',
            'price' => $service->price, // Simpan harga saat itu
        ]);
    }


    public function update($id, $data)
    {
        return $this->reservation->where(self::PRIMARY_KEY, $id)->update($data);
    }

    public function delete($id)
    {
        return $this->reservation->where(self::PRIMARY_KEY, $id)->delete();
    }
}
