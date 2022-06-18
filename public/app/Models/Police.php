<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Police extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'ci', 'name', 'last_name', 'dateOfBirth'];

    public function avatars()
    {
        return $this->hasMany(Avatar::class);
    }
}
