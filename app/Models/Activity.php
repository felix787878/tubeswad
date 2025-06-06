<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'ukm_ormawa_id',
        'user_id', // Pengurus yang membuat
        'name',
        'description',
        'date_start',
        'date_end',
        'time_start',
        'time_end',
        'location',
        'type',
        'image_banner_url',
        'is_published',
        'is_registration_open',          // <-- TAMBAHAN
        'registration_deadline_activity', // <-- TAMBAHAN
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
        'is_published' => 'boolean',
        'is_registration_open' => 'boolean',                     // <-- TAMBAHAN
        'registration_deadline_activity' => 'datetime',         // <-- TAMBAHAN
    ];

    public function ukmOrmawa()
    {
        return $this->belongsTo(UkmOrmawa::class);
    }

    public function user() // Pengurus yang membuat/mengelola kegiatan ini
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke pendaftar/partisipan kegiatan
    public function attendees()
    {
        return $this->hasMany(ActivityAttendance::class);
    }
}