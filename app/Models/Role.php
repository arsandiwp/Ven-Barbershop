<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $appends = ['total_user', 'parent_name'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];
    protected $guarded = ["id"];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function privileges()
    {
        return $this->belongsToMany(Privilege::class, 'role_privileges', 'role_id', 'privilege_id');
    }

    public function privileges_ids()
    {
        return $this->hasMany(RolePrivilege::class);
    }

    public function getTotalUserAttribute()
    {
        return $this->users()->count();
    }

    public function parent()
    {
        return $this->hasOne(Role::class, 'id', 'parent_id');
    }

    public function getParentNameAttribute()
    {
        return $this->parent->name ?? '-';
    }
}
