<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_complete' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'is_complete',
        'deadline_utc',
        'deadline_local',
        'local_timezone'
    ];

    /**
     * The relationship to the owning user.
     *
     * @return BelongsTo
    //  */
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

}
