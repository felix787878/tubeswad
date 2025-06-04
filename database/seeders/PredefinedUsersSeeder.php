<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UkmOrmawa; // Tetap di-import untuk referensi jika suatu saat dibutuhkan
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PredefinedUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@seputartelkom.ac.id'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'nim' => 'ADMIN001',
                // ... (field lain)
                'email_verified_at' => now(),
            ]
        );

        // Mahasiswa Biasa
        User::updateOrCreate(
            ['email' => 'mahasiswa.biasa@seputartelkom.ac.id'],
            [
                'name' => 'Mahasiswa Contoh',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'nim' => '102022300001',
                // ... (field lain)
                'email_verified_at' => now(),
            ]
        );

        // Pengurus 1 (Awalnya tidak mengelola UKM apapun)
        User::updateOrCreate(
            ['email' => 'pengurus.satu@seputartelkom.ac.id'],
            [
                'name' => 'Pengurus Satu',
                'password' => Hash::make('password123'),
                'role' => 'pengurus',
                'nim' => 'PGRS001',
                // 'manages_ukm_ormawa_id' => null, // Akan null by default, tidak perlu di-set eksplisit
                // ... (field lain)
                'email_verified_at' => now(),
            ]
        );

        // Pengurus 2 (Awalnya tidak mengelola UKM apapun)
        User::updateOrCreate(
            ['email' => 'pengurus.dua@seputartelkom.ac.id'],
            [
                'name' => 'Pengurus Dua',
                'password' => Hash::make('password123'),
                'role' => 'pengurus',
                'nim' => 'PGRS002',
                // 'manages_ukm_ormawa_id' => null,
                // ... (field lain)
                'email_verified_at' => now(),
            ]
        );
        
        // Direktorat
        User::updateOrCreate(
            ['email' => 'direktorat@seputartelkom.ac.id'],
            [
                'name' => 'Staf Direktorat Kemahasiswaan',
                'password' => Hash::make('password123'),
                'role' => 'direktorat',
                'nim' => 'DIR001',
                // ... (field lain)
                'email_verified_at' => now(),
            ]
        );
    }
}