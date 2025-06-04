<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seeder lain yang ingin Anda panggil, contoh:
            UkmOrmawaSeeder::class, // Pastikan class ini ada dan benar
            PredefinedUsersSeeder::class, // Pastikan class ini ada dan benar
            // AdminSeeder::class, // Jika Anda menggunakan ini
        ]);

        // Atau jika Anda tidak ingin memanggil seeder lain dari sini,
        // Anda bisa mengosongkan array di atas atau mengomentari $this->call(...)
        // Contoh:
        // $this->call([]);
    }
}