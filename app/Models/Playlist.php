<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $table = 'playlists';
    protected $guarded = [];

    protected $casts = [
        'start_contract' => 'datetime',
        'end_contract'   => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    // playlists.cust_id → customers.id
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function items()
    {
        return $this->hasMany(PlaylistItem::class, 'playlist_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'playlist_id');
    }
}
