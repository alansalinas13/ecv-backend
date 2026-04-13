<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'specialty',
        'phone',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
