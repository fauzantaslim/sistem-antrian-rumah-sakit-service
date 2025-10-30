<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'queue_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'queue_id',
        'counter_id',
        'queue_number',
        'status',
        'called_at',
        'called_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'called_at' => 'datetime',
    ];

    /**
     * Get the counter that owns the queue.
     */
    public function counter()
    {
        return $this->belongsTo(Counter::class, 'counter_id', 'counter_id');
    }

    /**
     * Get the user who called this queue.
     */
    public function calledBy()
    {
        return $this->belongsTo(User::class, 'called_by', 'user_id');
    }
}
