<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'counter_id';

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
        'counter_id',
        'counter_name',
        'description',
    ];

    /**
     * Get the users assigned to this counter.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'counter_id', 'counter_id');
    }

    /**
     * Get the queues for this counter.
     */
    public function queues()
    {
        return $this->hasMany(Queue::class, 'counter_id', 'counter_id');
    }
}
