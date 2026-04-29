<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'title', 'description', 'priority', 'client_id', 'status'
    ];

    protected $casts = [
        'priority' => 'string',
        'created_at' => 'datetime',
        'client_id' => 'integer'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}