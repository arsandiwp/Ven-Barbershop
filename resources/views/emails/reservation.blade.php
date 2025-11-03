@component('mail::message')
# Reservation {{ ucfirst($action) }}

Hello {{ $reservation->user->name ?? 'Customer' }},

Your reservation has been **{{ $action }}**.

- **Barber:** {{ $reservation->barber->name ?? '-' }}
- **Services:** {{ implode(', ', json_decode($reservation->service_id, true) ?? []) }}
- **Date:** {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('Y-m-d') }}
- **Time:** {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
- **Status:** {{ $reservation->status }}

@component('mail::button', ['url' => url('/reservations/'.$reservation->id)])
View Reservation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
