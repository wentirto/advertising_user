<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $guarded = [];

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'cust_id', 'id');
    }
}
