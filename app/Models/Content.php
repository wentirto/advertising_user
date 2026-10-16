<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'contents';
    protected $guarded = [];

    public function playlistItems()
    {
        return $this->hasMany(PlaylistItem::class, 'content_id');
    }

    /**
     * Return the full public URL for this content file.
     *
     * Handles three formats:
     *  - absolute URL (http/https)
     *  - local storage paths (contents/...) -> /storage/...
     *  - legacy app paths (device/..., uploads/...) -> try file proxy
     */
    public function getFileUrlAttribute()
    {
        $file = $this->attributes['file'] ?? '';

        // Already a full URL
        if (str_starts_with($file, 'http')) {
            return $file;
        }

        // Already contains the /storage/ prefix
        if (str_starts_with($file, '/storage/')) {
            return $file;
        }

        // Standard local upload path
        if (str_starts_with($file, 'contents/')) {
            return '/storage/' . $file;
        }

        // Legacy path (e.g. device/... or uploads/1/...) -> proxy through the
        // file proxy route so the same handling works for both local and remote files.
        // The fileProxy controller returns 404 when the file is missing, so the
        // JavaScript layer should handle that gracefully and show a placeholder.
        $encoded = urlencode($file);
        return route('file.proxy') . '?path=' . $encoded;
    }
}
