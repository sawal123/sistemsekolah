<?php

namespace Tests\Feature;

use App\Livewire\Admin\Website\WebsitePopupIndex;
use App\Models\User;
use App\Models\WebsitePopup;
use App\Models\WebsitePopupImpression;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WebsitePopupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function popup(array $attributes = []): WebsitePopup
    {
        return WebsitePopup::create($attributes + [
            'judul' => 'Informasi Pendaftaran',
            'gambar' => 'website/popups/informasi.jpg',
            'link' => 'https://sekolah.example/ppdb',
            'is_active' => true,
        ]);
    }

    private function admin(): User
    {
        Role::firstOrCreate(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    public function test_a_visitor_only_sees_each_popup_once_across_pages(): void
    {
        $popup = $this->popup();
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])->withHeader('User-Agent', 'Browser-A Android');

        $this->get('/')->assertOk()->assertSee('id="visitor-popup"', false);
        $this->assertDatabaseCount('website_popup_impressions', 0);
        $this->postJson(route('landing.popup.impression', $popup))->assertOk()->assertJson(['show' => true]);
        $this->postJson(route('landing.popup.impression', $popup))->assertOk()->assertJson(['show' => false]);
        $this->get('/tentang')->assertOk()->assertDontSee('id="visitor-popup"', false);
        $this->get('/ppdb')->assertOk()->assertDontSee('id="visitor-popup"', false);
        $this->assertDatabaseCount('website_popup_impressions', 1);

        $impression = WebsitePopupImpression::firstOrFail();
        $this->assertSame(64, strlen($impression->ip_hash));
        $this->assertSame(64, strlen($impression->device_hash));
        $this->assertNotSame('192.0.2.10', $impression->ip_hash);
    }

    public function test_expired_display_history_is_deleted_after_the_popup_configured_interval(): void
    {
        $popup = $this->popup(['display_interval_hours' => 2]);
        $this->postJson(route('landing.popup.impression', $popup))->assertJson(['show' => true]);
        $impression = WebsitePopupImpression::firstOrFail();
        $impression->update(['displayed_at' => now()->subHours(2)->subSecond()]);

        $this->get('/')->assertOk()->assertSee('id="visitor-popup"', false);
        $this->assertDatabaseMissing('website_popup_impressions', ['id' => $impression->id]);
        $this->postJson(route('landing.popup.impression', $popup))->assertJson(['show' => true]);
        $this->assertDatabaseCount('website_popup_impressions', 1);
    }

    public function test_different_ip_or_device_gets_an_independent_display(): void
    {
        $popup = $this->popup();
        foreach ([['192.0.2.1', 'Android Chrome'], ['192.0.2.1', 'iPhone Safari'], ['192.0.2.2', 'Android Chrome']] as [$ip, $device]) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])->withHeader('User-Agent', $device);
            $this->postJson(route('landing.popup.impression', $popup))->assertOk()->assertJson(['show' => true]);
            $this->postJson(route('landing.popup.impression', $popup))->assertOk()->assertJson(['show' => false]);
        }
        $this->assertDatabaseCount('website_popup_impressions', 3);
    }

    public function test_only_one_unseen_active_popup_is_selected_newest_first(): void
    {
        $older = $this->popup(['judul' => 'Informasi Lama']);
        $newer = $this->popup(['judul' => 'Informasi Terbaru']);
        $inactive = $this->popup(['judul' => 'Informasi Nonaktif', 'is_active' => false]);
        $this->get('/kontak')->assertOk()->assertSee('aria-label="Informasi Terbaru"', false)
            ->assertSee('visitor-popup-stage', false)->assertDontSee('visitor-popup-header', false)
            ->assertDontSee('visitor-popup-actions', false)
            ->assertDontSee('Informasi Lama')->assertDontSee('Informasi Nonaktif');
        $this->postJson(route('landing.popup.impression', $newer))->assertJson(['show' => true]);
        $this->get('/kontak')->assertOk()->assertSee('aria-label="Informasi Lama"', false);
        $this->postJson(route('landing.popup.impression', $older))->assertJson(['show' => true]);
        $this->get('/kontak')->assertOk()->assertDontSee('id="visitor-popup"', false);
        $this->postJson(route('landing.popup.impression', $inactive))->assertNotFound();
    }

    public function test_editing_or_reactivating_a_popup_does_not_reset_its_display_history(): void
    {
        $popup = $this->popup();
        $this->postJson(route('landing.popup.impression', $popup))->assertJson(['show' => true]);
        $popup->update(['judul' => 'Judul Diubah', 'is_active' => false]);
        $popup->update(['is_active' => true]);
        $this->get('/')->assertOk()->assertDontSee('id="visitor-popup"', false);
    }

    public function test_database_constraint_prevents_duplicate_impressions(): void
    {
        $popup = $this->popup();
        $this->postJson(route('landing.popup.impression', $popup))->assertJson(['show' => true]);
        $data = WebsitePopupImpression::firstOrFail()->only(['website_popup_id', 'visitor_key', 'ip_hash', 'device_hash', 'displayed_at']);
        $this->expectException(UniqueConstraintViolationException::class);
        WebsitePopupImpression::create($data);
    }

    public function test_admin_can_manage_popup_images_links_status_and_deletion(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $this->get(route('admin.website.modal-pengunjung'))->assertOk()->assertSee('Tambah Modal')->assertSee('Tampilkan kembali setelah');

        $component = Livewire::test(WebsitePopupIndex::class)
            ->call('create')->set('judul', 'PPDB Baru')
            ->set('link', 'https://sekolah.example/ppdb')
            ->set('displayIntervalHours', 12)
            ->set('gambar', UploadedFile::fake()->image('ppdb.png', 800, 1000))
            ->call('save')->assertHasNoErrors();
        $popup = WebsitePopup::firstOrFail();
        $oldImage = $popup->gambar;
        $this->assertSame(12, $popup->display_interval_hours);
        Storage::disk('public')->assertExists($oldImage);

        $component->call('edit', $popup->id)->set('judul', 'PPDB Diubah')
            ->set('displayIntervalHours', 48)
            ->set('gambar', UploadedFile::fake()->image('baru.jpg', 900, 600))
            ->call('save')->assertHasNoErrors();
        $popup->refresh();
        $this->assertSame('PPDB Diubah', $popup->judul);
        $this->assertSame(48, $popup->display_interval_hours);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($popup->gambar);
        $component->call('toggleStatus', $popup->id);
        $this->assertFalse($popup->fresh()->is_active);
        $component->call('delete', $popup->id);
        $this->assertDatabaseMissing('website_popups', ['id' => $popup->id]);
        Storage::disk('public')->assertMissing($popup->gambar);
    }

    public function test_admin_cannot_save_unsafe_links_or_non_image_uploads(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        Livewire::test(WebsitePopupIndex::class)->set('judul', 'Informasi')
            ->set('link', 'javascript:alert(1)')
            ->set('displayIntervalHours', 0)
            ->set('gambar', UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'))
            ->call('save')->assertHasErrors(['link', 'gambar', 'displayIntervalHours']);
        $this->assertDatabaseCount('website_popups', 0);
    }

    public function test_guest_and_teacher_cannot_manage_popups(): void
    {
        $this->get(route('admin.website.modal-pengunjung'))->assertRedirect(route('login'));
        Livewire::test(WebsitePopupIndex::class)->assertForbidden();
        Role::firstOrCreate(['name' => 'guru']);
        $teacher = User::factory()->create();
        $teacher->assignRole('guru');
        $this->actingAs($teacher)->get(route('admin.website.modal-pengunjung'))->assertForbidden();
        Livewire::test(WebsitePopupIndex::class)->assertForbidden();
    }
}
