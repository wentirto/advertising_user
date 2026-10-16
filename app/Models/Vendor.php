<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendors';
    protected $guarded = [];

    public function devices()
    {
        return $this->hasMany(Device::class, 'id_vendor');
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'vendor_id');
    }
}
