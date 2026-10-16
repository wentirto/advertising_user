<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaylistItem extends Model
{
    protected $table = 'playlist_items';
    protected $guarded = [];

    protected $casts = [
        'days'           => 'array',
        'specific_times'  => 'array',
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class, 'playlist_id');
    }

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
