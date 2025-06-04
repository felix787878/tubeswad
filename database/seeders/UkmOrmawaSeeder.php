<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UkmOrmawa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UkmOrmawaSeeder extends Seeder
{
    public function run(): void
    {
        $ukmData = [
            [
                'name' => 'UKM Tari Saman',
                'slug' => Str::slug('UKM Tari Saman'),
                'type' => 'UKM',
                'category' => 'Seni & Budaya',
                'description_short' => 'Unit Kegiatan Mahasiswa untuk seni Tari Saman dari Aceh.',
                'description_full' => 'Deskripsi lengkap UKM Tari Saman...',
                'visi' => 'Visi UKM Tari Saman...',
                'misi' => ['Misi 1 Saman', 'Misi 2 Saman', 'Misi 3 Saman'],
                'logo_url' => 'images/logos/ukm_saman_logo.png', 
                'banner_url' => 'images/banners/ukm_saman_banner.png', 
                'contact_email' => 'saman@example.com',
                'contact_instagram' => '@ukmsaman_tu',
                'is_registration_open' => false, 
                'registration_deadline' => null, 
                'status' => 'approved', // <-- UBAH INI
            ],
            [
                'name' => 'UKM Paduan Suara Mahasiswa Harmoni',
                'slug' => Str::slug('UKM Paduan Suara Mahasiswa Harmoni'),
                'type' => 'UKM',
                'category' => 'Seni & Budaya',
                'description_short' => 'Pengembangan bakat tarik suara dan paduan suara.',
                // ... (lengkapi field lainnya) ...
                'misi' => ['Misi 1 Padus', 'Misi 2 Padus'],
                'status' => 'approved', // <-- UBAH INI
                'is_registration_open' => false,
            ],
            [
                'name' => 'BEM Fakultas Rekayasa Industri',
                'slug' => Str::slug('BEM Fakultas Rekayasa Industri'),
                'type' => 'Ormawa',
                'category' => 'Organisasi Eksekutif',
                'description_short' => 'Badan Eksekutif Mahasiswa tingkat Fakultas Rekayasa Industri.',
                // ... (lengkapi field lainnya) ...
                'misi' => ['Misi 1 BEM FRI', 'Misi 2 BEM FRI'],
                'status' => 'approved', // <-- UBAH INI
                'is_registration_open' => false,
            ],
            [
                'name' => 'UKM Fotografi Lensa Kampus',
                'slug' => Str::slug('UKM Fotografi Lensa Kampus'),
                'type' => 'UKM',
                'category' => 'Seni & Media',
                'description_short' => 'Wadah bagi mahasiswa yang memiliki minat di bidang fotografi.',
                // ... (lengkapi field lainnya) ...
                'misi' => ['Misi 1 Fotografi', 'Misi 2 Fotografi'],
                'status' => 'approved', // <-- UBAH INI
                'is_registration_open' => false,
            ]
        ];

        foreach ($ukmData as $data) {
            UkmOrmawa::updateOrCreate(['name' => $data['name']], $data);
        }
    }
}