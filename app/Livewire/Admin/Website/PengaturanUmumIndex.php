<?php

namespace App\Livewire\Admin\Website;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Pengaturan Umum')]
class PengaturanUmumIndex extends Component
{
    use WithFileUploads;

    public string $site_name = '';
    public string $site_title = '';
    public string $site_tagline = '';
    public string $school_name = '';
    public string $school_address = '';
    public string $school_phone = '';
    public string $school_email = '';
    public string $school_website = '';
    public string $whatsapp = '';

    public string $seo_title = '';
    public string $seo_description = '';
    public string $seo_keywords = '';

    public string $facebook_url = '';
    public string $instagram_url = '';
    public string $youtube_url = '';
    public string $tiktok_url = '';
    public string $linkedin_url = '';

    public $logoFile;
    public $faviconFile;
    public ?string $existingLogo = null;
    public ?string $existingFavicon = null;

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key');

        $this->site_name = $settings->get('site_name', $settings->get('app_name', 'SMA Nusantara'));
        $this->site_title = $settings->get('site_title', 'Website Resmi Sekolah');
        $this->site_tagline = $settings->get('site_tagline', '');
        $this->school_name = $settings->get('school_name', $settings->get('nama_sekolah', 'SMA Nusantara'));
        $this->school_address = $settings->get('school_address', $settings->get('alamat', ''));
        $this->school_phone = $settings->get('school_phone', $settings->get('telepon', ''));
        $this->school_email = $settings->get('school_email', '');
        $this->school_website = $settings->get('school_website', '');
        $this->whatsapp = $settings->get('whatsapp', '');

        $this->seo_title = $settings->get('seo_title', '');
        $this->seo_description = $settings->get('seo_description', '');
        $this->seo_keywords = $settings->get('seo_keywords', '');

        $this->facebook_url = $settings->get('facebook_url', '');
        $this->instagram_url = $settings->get('instagram_url', '');
        $this->youtube_url = $settings->get('youtube_url', '');
        $this->tiktok_url = $settings->get('tiktok_url', '');
        $this->linkedin_url = $settings->get('linkedin_url', '');

        $this->existingLogo = $settings->get('app_logo', $settings->get('logo'));
        $this->existingFavicon = $settings->get('favicon', $settings->get('app_icon'));
    }

    public function save(): void
    {
        $this->validate([
            'site_name' => 'required|string|max:120',
            'site_title' => 'required|string|max:160',
            'site_tagline' => 'nullable|string|max:220',
            'school_name' => 'required|string|max:160',
            'school_address' => 'nullable|string|max:500',
            'school_phone' => 'nullable|string|max:40',
            'school_email' => 'nullable|email|max:120',
            'school_website' => 'nullable|string|max:160',
            'whatsapp' => 'nullable|string|max:40',
            'seo_title' => 'nullable|string|max:70',
            'seo_description' => 'nullable|string|max:180',
            'seo_keywords' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'logoFile' => 'nullable|image|max:2048',
            'faviconFile' => 'nullable|image|max:1024',
        ]);

        $logoPath = $this->existingLogo;
        if ($this->logoFile) {
            $logoPath = $this->logoFile->store('branding', 'public');
            $this->deleteOldFile($this->existingLogo);
        }

        $faviconPath = $this->existingFavicon;
        if ($this->faviconFile) {
            $faviconPath = $this->faviconFile->store('branding', 'public');
            $this->deleteOldFile($this->existingFavicon);
        }

        $settings = [
            'site_name' => $this->site_name,
            'site_title' => $this->site_title,
            'site_tagline' => $this->site_tagline,
            'school_name' => $this->school_name,
            'school_address' => $this->school_address,
            'school_phone' => $this->school_phone,
            'school_email' => $this->school_email,
            'school_website' => $this->school_website,
            'whatsapp' => $this->whatsapp,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'youtube_url' => $this->youtube_url,
            'tiktok_url' => $this->tiktok_url,
            'linkedin_url' => $this->linkedin_url,
            'app_name' => $this->site_name,
            'nama_sekolah' => $this->school_name,
            'alamat' => $this->school_address,
            'telepon' => $this->school_phone,
            'logo' => $logoPath,
            'app_logo' => $logoPath,
            'favicon' => $faviconPath,
            'app_icon' => $faviconPath,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?: null]);
        }

        $this->existingLogo = $logoPath;
        $this->existingFavicon = $faviconPath;
        $this->logoFile = null;
        $this->faviconFile = null;

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Pengaturan umum berhasil disimpan.']);
    }

    public function render()
    {
        return view('livewire.admin.website.pengaturan-umum-index');
    }

    private function deleteOldFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'branding/logo.png')) {
            Storage::disk('public')->delete($path);
        }
    }
}
