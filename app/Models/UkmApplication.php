<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UkmApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ukm_ormawa_id',
        'ukm_ormawa_name', // Tetap sertakan jika masih ada di tabel
        'ukm_ormawa_slug', // Tetap sertakan jika masih ada di tabel
        'reason_to_join',
        'skills_experience',
        'phone_contact',
        'province',       // <-- TAMBAHKAN
        'city',           // <-- TAMBAHKAN
        'district',       // <-- TAMBAHKAN
        'village',        // <-- TAMBAHKAN
        'full_address',   // <-- TAMBAHKAN
        'status',
    ];
// ...
public function ukmOrmawa()
{
    return $this->belongsTo(UkmOrmawa::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}
    // ...
}