<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'device_id');
    }
}
