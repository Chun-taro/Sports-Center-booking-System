<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'holiday_date',
        'is_recurring',
        'note',
    ];

    protected $casts = [
        'holiday_date' => 'date:Y-m-d',
        'is_recurring' => 'boolean',
    ];
}
