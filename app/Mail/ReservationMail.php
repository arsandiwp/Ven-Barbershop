<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reservation;
    public $action;

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation, $action = "created")
    {
        $this->reservation = $reservation;
        $this->action = $action;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Reservation {$this->action}")
            ->markdown('emails.reservation')
            ->with([
                'reservation' => $this->reservation,
                'action' => $this->action,
            ]);
    }
}
