<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'doctor_code',
        'academic_title',
        'level',
        'expertise',
        'experience_years',
        'license_number',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialties')->withPivot('is_primary');
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getFullTitleAttribute(): string
    {
        $title = $this->academic_title ? $this->academic_title . ' ' : '';
        return $title . optional($this->user)->full_name;
    }
}
