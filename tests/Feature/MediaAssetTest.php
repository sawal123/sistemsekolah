<?php

namespace Tests\Feature;

use App\Livewire\Admin\Website\GaleriSliderIndex;
use App\Models\MediaAsset;
use App\Models\Slider;
use App\Models\User;
use App\Services\MediaAssetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MediaAssetTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($role);

        return $admin;
    }

    public function test_admin_can_upload_media_asset(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $asset = app(MediaAssetService::class)->store(
            UploadedFile::fake()->image('kampus.jpg', 1280, 720),
            $admin,
            'Gedung sekolah'
        );
        $this->assertSame('kampus.jpg', $asset->original_name);
        $this->assertSame('Gedung sekolah', $asset->alt_text);
        $this->assertStringStartsWith('media/', $asset->path);
        Storage::disk('public')->assertExists($asset->path);
    }

    public function test_media_asset_can_be_selected_for_slider_and_is_not_deleted_with_content(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        Storage::disk('public')->put('media/shared.jpg', 'image-content');
        $asset = MediaAsset::create([
            'original_name' => 'shared.jpg',
            'path' => 'media/shared.jpg',
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'size' => 13,
            'uploaded_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(GaleriSliderIndex::class)
            ->set('sliderJudul', 'Slider dari asset media')
            ->set('sliderMediaId', $asset->id)
            ->call('saveSlider')
            ->assertHasNoErrors();

        $slider = Slider::firstOrFail();
        $this->assertSame($asset->path, $slider->foto);
        $this->assertTrue(app(MediaAssetService::class)->isUsed($asset));
        $slider->delete();
        $this->assertFalse(app(MediaAssetService::class)->isUsed($asset));
        Storage::disk('public')->assertExists($asset->path);
    }

    public function test_existing_content_uploads_can_be_synchronized_to_media_library(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('website/sliders/legacy.jpg', 'legacy-image');
        Slider::create([
            'judul' => 'Slider lama',
            'foto' => 'website/sliders/legacy.jpg',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $this->assertSame(1, app(MediaAssetService::class)->syncExistingContent());
        $this->assertDatabaseHas('media_assets', [
            'path' => 'website/sliders/legacy.jpg',
            'original_name' => 'legacy.jpg',
        ]);
    }
}
