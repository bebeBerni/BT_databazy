<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    protected $fillable = [
        'public_id',
        'collection',
        'visibility',
        'disk',
        'path',
        'original_name',
        'stored_name',
        'mime_type',
        'size',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function publicUrl(): ?string
    {
        if ($this->visibility !== 'public') {
            return null;
        }

        return Storage::disk($this->disk)->url($this->path);
    }


// Model User.php:

public function profilePhoto(): MorphOne
{
    return $this->morphOne(Attachment::class, 'attachable')
        ->where('collection', 'profile_photo');
}


// Model Note.php:

public function attachments(): MorphMany
{
    return $this->morphMany(Attachment::class, 'attachable')
        ->where('collection', 'attachment');
}

    public function note()
    {
        return $this->belongsTo(\App\Models\Note::class);
    }


}
