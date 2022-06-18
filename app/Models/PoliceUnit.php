<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliceUnit extends Model
{
    use HasFactory;

    protected $table = "police_units";

    protected $fillable = [
        'direction', 'lat', 'lng', 'name',
    ];
}
