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
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $roleGuru = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $roleSiswa = Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'alumni', 'guard_name' => 'web']);

        // 2. Buat Akun Dummy Super Admin
        $admin = User::updateOrCreate(['email' => 'admin@sekolah.com'], [
            'name' => 'Super Administrator',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password'), // Password default: password
            'staff_position' => 'Kepala Tata Usaha',
        ]);
        $admin->syncRoles([$roleAdmin]);

        // 3. Buat Akun Dummy Guru
        $guru = User::updateOrCreate(['email' => 'guru@sekolah.com'], [
            'name' => 'Budi Santoso, S.Pd',
            'email' => 'guru@sekolah.com',
            'password' => Hash::make('password'),
        ]);
        $guru->syncRoles([$roleGuru]);

        // 4. Buat Akun Dummy Siswa
        $siswa = User::updateOrCreate(['email' => 'siswa@sekolah.com'], [
            'name' => 'Andi Pratama',
            'email' => 'siswa@sekolah.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $siswa->syncRoles([$roleSiswa]);
    }
}
