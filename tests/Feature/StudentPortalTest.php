<?php

namespace Tests\Feature;

use App\Models\PaymentSubmission;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use App\Services\PaymentSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    private function role(string $name): Role
    {
        return Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    private function student(): array
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($this->role('siswa'));
        $siswa = Siswa::create([
            'user_id' => $user->id,
            'nisn' => 'NISN' . str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
            'nis' => 'NIS' . str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
            'jenjang' => 'SMA',
            'status' => 'Aktif',
        ]);

        return [$user, $siswa];
    }

    private function tagihan(Siswa $siswa, float $nominal = 250000): Tagihan
    {
        return Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_biaya' => 'SPP Bulanan',
            'tahun' => now()->year,
            'bulan' => now()->month,
            'nominal' => $nominal,
            'jatuh_tempo' => today()->addDays(7),
            'status' => 'Belum Lunas',
        ]);
    }

    public function test_student_approval_creates_ledger_only_after_admin_approves(): void
    {
        [$studentUser, $siswa] = $this->student();
        $admin = User::factory()->create();
        $admin->assignRole($this->role('admin'));
        $tagihan = $this->tagihan($siswa);
        $submission = PaymentSubmission::create([
            'tagihan_id' => $tagihan->id,
            'siswa_id' => $siswa->id,
            'submitted_by' => $studentUser->id,
            'nominal' => 250000,
            'tanggal_transfer' => today(),
            'metode' => 'Transfer Bank',
            'bukti_path' => 'student-payment-proofs/test.jpg',
            'status' => PaymentSubmission::PENDING,
        ]);

        $this->assertDatabaseCount('pembayarans', 0);
        app(PaymentSubmissionService::class)->approve($submission->id, $admin, 'Bukti valid');

        $this->assertDatabaseCount('pembayarans', 1);
        $this->assertDatabaseHas('payment_submissions', ['id' => $submission->id, 'status' => PaymentSubmission::APPROVED]);
        $this->assertSame('Lunas', $tagihan->fresh()->status);
    }

    public function test_rejected_submission_does_not_change_ledger(): void
    {
        [$studentUser, $siswa] = $this->student();
        $admin = User::factory()->create();
        $admin->assignRole($this->role('admin'));
        $tagihan = $this->tagihan($siswa);
        $submission = PaymentSubmission::create([
            'tagihan_id' => $tagihan->id,
            'siswa_id' => $siswa->id,
            'submitted_by' => $studentUser->id,
            'nominal' => 100000,
            'tanggal_transfer' => today(),
            'metode' => 'QRIS',
            'bukti_path' => 'student-payment-proofs/test.pdf',
            'status' => PaymentSubmission::PENDING,
        ]);

        app(PaymentSubmissionService::class)->reject($submission->id, $admin, 'Nominal pada bukti tidak terbaca.');

        $this->assertDatabaseCount('pembayarans', 0);
        $this->assertDatabaseHas('payment_submissions', ['id' => $submission->id, 'status' => PaymentSubmission::REJECTED]);
        $this->assertSame('Belum Lunas', $tagihan->fresh()->status);
    }

    public function test_student_can_access_own_mobile_portal_but_not_admin_dashboard(): void
    {
        [$studentUser] = $this->student();

        $this->actingAs($studentUser)->get(route('siswa.dashboard'))->assertOk();
        $this->actingAs($studentUser)->get(route('siswa.payments'))->assertOk();
        $this->actingAs($studentUser)->get(route('siswa.academic'))->assertOk();
        $this->actingAs($studentUser)->get(route('siswa.profile'))->assertOk();
        $this->actingAs($studentUser)->get(route('dashboard'))->assertForbidden();
    }

    public function test_login_page_provides_student_demo_option(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee("fillDemo('siswa')", false)
            ->assertSee('siswa@sekolah.com', false);
    }
}
