<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use App\Models\Booking;
use App\Models\BookingHistory;
use Illuminate\Support\Facades\DB;

class UpdateFinishedBookings extends Command
{
    protected $signature = 'bookings:check-finished';
    protected $description = 'Check dan update booking yang sudah selesai berdasarkan waktu schedule';

    public function handle()
    {
        $schedules = Schedule::where('end_date', '<=', now())
            ->whereHas('bookings', function ($query) {
                $query->where('status', 'Success');
            })
            ->get();

        foreach ($schedules as $schedule) {
            $bookings = $schedule->bookings()
                ->where('status', 'Success')
                ->get();

            foreach ($bookings as $booking) {
                DB::transaction(function () use ($booking, $schedule) {
                    $booking->update(['status' => 'Finish']);

                    BookingHistory::create([
                        'booking_id' => $booking->id,
                        'user_id' => $booking->user_id,
                        'schedule_id' => $booking->schedule_id,
                        'status' => 'Finish',
                        'information' => 'Schedule telah selesai. Status booking diubah otomatis menjadi Finish.'
                    ]);

                    $this->info("Booking ID {$booking->id} telah diupdate ke status Finish");
                });
            }
        }

        $this->info('Proses update booking selesai');
    }
}
