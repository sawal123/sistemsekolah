<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);
    }

    public function test_user_can_access_profile_page_and_update_information()
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Dahlan',
            'email' => 'ahmad@sekolah.com',
            'is_active' => true,
        ]);
        $user->assignRole('admin');

        $this->actingAs($user);

        $response = $this->get(route('admin.profile'));
        $response->assertOk();
        $response->assertSee('Profil Saya');
        $response->assertSee('Ahmad Dahlan');

        Livewire::test(\App\Livewire\Admin\Profile\ProfileIndex::class)
            ->set('name', 'Ahmad Dahlan, M.Pd')
            ->set('email', 'ahmad.dahlan@sekolah.com')
            ->call('updateProfile')
            ->assertDispatched('notify', fn ($event, $params) => $params['type'] === 'success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Ahmad Dahlan, M.Pd',
            'email' => 'ahmad.dahlan@sekolah.com',
        ]);
    }

    public function test_guru_can_update_teacher_biodata_in_profile()
    {
        $user = User::factory()->create([
            'name' => 'Siti Aminah',
            'email' => 'siti@sekolah.com',
            'is_active' => true,
        ]);
        $user->assignRole('guru');

        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => '19850101',
            'no_telp' => '08123456789',
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Admin\Profile\ProfileIndex::class)
            ->set('no_telp', '08987654321')
            ->set('tempat_lahir', 'Bandung')
            ->set('tanggal_lahir', '1985-05-12')
            ->set('agama', 'Islam')
            ->set('alamat', 'Jl. Merdeka No. 10')
            ->call('updateProfile')
            ->assertDispatched('notify', fn ($event, $params) => $params['type'] === 'success');

        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'no_telp' => '08987654321',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1985-05-12',
            'agama' => 'Islam',
            'alamat' => 'Jl. Merdeka No. 10',
        ]);
    }

    public function test_user_can_access_settings_and_update_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama_123'),
            'is_active' => true,
        ]);
        $user->assignRole('guru');

        $this->actingAs($user);

        $response = $this->get(route('admin.settings'));
        $response->assertOk();
        $response->assertSee('Pengaturan');
        $response->assertSee('Ubah Kata Sandi');

        Livewire::test(\App\Livewire\Admin\Profile\SettingsIndex::class)
            ->set('current_password', 'password_lama_123')
            ->set('new_password', 'password_baru_456')
            ->set('new_password_confirmation', 'password_baru_456')
            ->call('updatePassword')
            ->assertDispatched('notify', fn ($event, $params) => $params['type'] === 'success');

        $user->refresh();
        $this->assertTrue(Hash::check('password_baru_456', $user->password));
    }

    public function test_update_password_fails_if_current_password_is_incorrect()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_asli'),
            'is_active' => true,
        ]);
        $user->assignRole('guru');

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Admin\Profile\SettingsIndex::class)
            ->set('current_password', 'password_salah')
            ->set('new_password', 'password_baru_456')
            ->set('new_password_confirmation', 'password_baru_456')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);

        $user->refresh();
        $this->assertTrue(Hash::check('password_asli', $user->password));
    }

    public function test_admin_can_update_kbm_settings_in_settings_page()
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Profile\SettingsIndex::class)
            ->set('durasi_jam_pelajaran', 50)
            ->set('jam_mulai_pelajaran', '07:30')
            ->call('updateKbmSettings')
            ->assertDispatched('notify', fn ($event, $params) => $params['type'] === 'success');

        $this->assertEquals('50', Setting::where('key', 'durasi_jam_pelajaran')->value('value'));
        $this->assertEquals('07:30', Setting::where('key', 'jam_mulai_pelajaran')->value('value'));
    }

    public function test_guru_cannot_update_kbm_settings()
    {
        $guru = User::factory()->create(['is_active' => true]);
        $guru->assignRole('guru');

        $this->actingAs($guru);

        Livewire::test(\App\Livewire\Admin\Profile\SettingsIndex::class)
            ->set('durasi_jam_pelajaran', 50)
            ->call('updateKbmSettings')
            ->assertStatus(403);
    }
}

