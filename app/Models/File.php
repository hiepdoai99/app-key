<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Scopes\LatestScope;
use App\Traits\Files\FileHandler;
use App\Traits\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use SpatieLogsActivity, FileHandler;
    // use HasFactory;
    protected $fillable = [
        'path', 'type'
    ];

    protected $appends = ['full_url'];

    protected $hidden = [
        'fileable_type', 'fileable_id'
    ];

    public function getFullUrlAttribute()
    {
        if (in_array(config('filesystems.default'), ['local', 'public'])) {
            return request()->root().$this->path;
        }
        return $this->path;
    }

    public function fileable()
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::addGlobalScope(new LatestScope);
    }
}
