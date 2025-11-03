<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'service_id' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(User::class, 'barber_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }


    public function getEndTimeAttribute()
    {
        return Carbon::parse($this->reservation_time)
            ->addMinutes($this->service->duration);
    }
}
