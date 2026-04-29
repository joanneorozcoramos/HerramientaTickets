<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    // Solo los campos que existen en tu tabla
    protected $fillable = [
        'name'
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}