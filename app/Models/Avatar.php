<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    use HasFactory;
    protected $table = "avatars";

    protected $fillable = [
        'id',
         'url',
          'code_image',
           'police_id'
        ];

    public function user()
    {
        return $this->belongsTo(Police::class);
    }
}
