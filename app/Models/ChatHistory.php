<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'usage' => 'array',
        'expires_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function modelSetting()
    {
        return $this->belongsTo(ModelSetting::class, 'model_id', 'model_id');
    }
}