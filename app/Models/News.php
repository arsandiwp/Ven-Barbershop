<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $guarded = ['id'];
    protected $appends = ['image', 'readable_date'];

    // public function category()
    // {
    //     return $this->belongsTo(NewsCategory::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(NewsImage::class);
    }

    public function getImageAttribute()
    {
        return $this->images()->first();
    }

    public function getReadableDateAttribute()
    {
        if($this->date){
            $date = date_create($this->date);
            return date_format($date, 'l, F, jS Y');
        }
        return $this->date;
    }
}
