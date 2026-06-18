<?php

namespace Tests\Feature;

use App\Livewire\Admin\DataMaster\DataKelasIndex;
use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DataKelasSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmation_warns_when_class_contains_students(): void
    {
        [$kelas] = $this->createClassWithStudent();

        Livewire::test(DataKelasIndex::class)
            ->call('confirmDelete', $kelas->id)
            ->assertSet('deleteStudentCount', 1)
            ->assertSet('deleteClassName', 'X RPL 1')
            ->assertSet('deleteMessage', fn ($message) => str_contains($message, '1 siswa')
                && str_contains($message, 'soft delete')
                && str_contains($message, 'Riwayat nilai dan pembayaran tetap tersimpan'));
    }

    public function test_deleting_class_soft_deletes_class_and_students_and_disables_accounts(): void
    {
        [$kelas, $siswa, $user] = $this->createClassWithStudent();

        Livewire::test(DataKelasIndex::class)
            ->call('confirmDelete', $kelas->id)
            ->call('delete');

        $this->assertSoftDeleted('kelas', ['id' => $kelas->id]);
        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);
        $this->assertNotNull(Kelas::withTrashed()->find($kelas->id));
        $this->assertNotNull(Siswa::withTrashed()->find($siswa->id));
    }

    public function test_payment_history_still_resolves_soft_deleted_student(): void
    {
        [$kelas, $siswa] = $this->createClassWithStudent();
        $tahunAjaran = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $spp = Spp::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenjang' => 'SMK',
            'kategori' => 'SPP Bulanan',
            'nominal' => 500000,
            'is_active' => true,
        ]);
        $payment = PembayaranSpp::create([
            'siswa_id' => $siswa->id,
            'spp_id' => $spp->id,
            'user_id' => $siswa->user_id,
            'tahun' => 2026,
            'bulan' => 1,
            'tanggal_bayar' => '2026-07-10',
            'jumlah_bayar' => 500000,
            'potongan' => 0,
            'status' => 'Lunas',
        ]);

        Livewire::test(DataKelasIndex::class)
            ->call('confirmDelete', $kelas->id)
            ->call('delete');

        $this->assertSame($siswa->id, $payment->fresh()->siswa->id);
        $this->assertSame('Siswa Soft Delete', $payment->fresh()->siswa->user->name);
    }

    private function createClassWithStudent(): array
    {
        $kelas = Kelas::create([
            'nama_kelas' => 'X RPL 1',
            'jenjang' => 'SMK',
        ]);
        $user = User::create([
            'name' => 'Siswa Soft Delete',
            'email' => 'soft-delete@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $siswa = Siswa::create([
            'user_id' => $user->id,
            'kelas_id' => $kelas->id,
            'nisn' => '0011223344',
            'nis' => 'SMK001',
            'jenjang' => 'SMK',
            'status' => 'Aktif',
        ]);

        return [$kelas, $siswa, $user];
    }
}
