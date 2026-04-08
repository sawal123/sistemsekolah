<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Role Dasar (Tanpa harus mikir permission detail dulu)
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleGuru = Role::create(['name' => 'guru']);
        $roleSiswa = Role::create(['name' => 'siswa']);

        // 2. Buat Akun Dummy Super Admin
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password'), // Password default: password
        ]);
        $admin->assignRole($roleAdmin); // Jadikan dia Admin

        // 3. Buat Akun Dummy Guru
        $guru = User::create([
            'name' => 'Budi Santoso, S.Pd',
            'email' => 'guru@sekolah.com',
            'password' => Hash::make('password'),
        ]);
        $guru->assignRole($roleGuru); // Jadikan dia Guru

        // 4. Buat Akun Dummy Siswa
        $siswa = User::create([
            'name' => 'Andi Pratama',
            'email' => 'siswa@sekolah.com',
            'password' => Hash::make('password'),
        ]);
        $siswa->assignRole($roleSiswa); // Jadikan dia Siswa
    }
}
