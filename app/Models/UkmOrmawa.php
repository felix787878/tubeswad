<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 
use Carbon\Carbon;

class UkmOrmawa extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengurus_id', 
        'name',
        'slug',
        'type',
        'category',
        'logo_url',
        'banner_url',
        'description_short',
        'description_full',
        'visi',
        'misi', 
        'contact_email',
        'contact_instagram',
        'alamat_lengkap',
        'provinsi',
        'kabkota',
        'kecamatan',
        'desakel',
        'Maps_link',
        'is_registration_open',
        'registration_deadline',
        'status', 
        'verification_notes', 

    ];

    protected $casts = [
        'misi' => 'array',
        'is_registration_open' => 'boolean',
        'registration_deadline' => 'datetime',
        // Hapus 'is_visible' jika ada
    ];

    // ... (boot method dan relasi lainnya tetap sama) ...
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ukmOrmawa) {
            if (empty($ukmOrmawa->slug)) {
                $ukmOrmawa->slug = Str::slug($ukmOrmawa->name);
            }
        });
        static::updating(function ($ukmOrmawa) {
            if ($ukmOrmawa->isDirty('name')) {
                $originalSlug = Str::slug($ukmOrmawa->getOriginal('name'));
                if (empty($ukmOrmawa->getOriginal('slug')) || $ukmOrmawa->getOriginal('slug') === $originalSlug) {
                    $ukmOrmawa->slug = Str::slug($ukmOrmawa->name);
                }
            }
        });
    }

    public function applications()
    {
        return $this->hasMany(UkmApplication::class, 'ukm_ormawa_id');
    }

    public function pengurus()
    {
        return $this->belongsTo(User::class, 'pengurus_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}