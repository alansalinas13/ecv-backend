<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model {
    protected $fillable = [
        'user_id',
        'city_id',
        'hospital_id',
        'specialty',
        'phone',
        'description',
        'start_time',
        'end_time',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function appointments() {
        return $this->hasMany(Appointment::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function hospital() {
        return $this->belongsTo(Hospital::class);
    }
}
