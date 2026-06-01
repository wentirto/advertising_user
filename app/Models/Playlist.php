<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    // Pastikan ini sesuai dengan nama tabel Anda di database
    protected $table = 'playlists';

    // Izinkan field agar bisa diisi (Mass Assignment)
    protected $fillable = [
    'playlist_name',
    'vendor_id',
    'start_contract',
    'end_contract',
    'contract_value'
];
}