<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Note extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'notes';

    protected $primaryKey = 'id';

    //public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'status',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function pin()
    {
        $this->update([
            'is_pinned' => true
        ]);
    }

    public function unpin()
    {
        $this->update([
            'is_pinned' => false
        ]);
    }

    public function publish()
    {
        $this->update([
            'status' => 'published'
        ]);
    }

    public function archive()
    {
        $this->update([
            'status' => 'archived'
        ]);
    }

    public function draft()
    {
        $this->update([
            'status' => 'draft'
        ]);
    }

}
