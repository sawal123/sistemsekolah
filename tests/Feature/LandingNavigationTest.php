<?php

namespace Tests\Feature;

use App\Models\KalenderAkademik;
use App\Models\Kategori;
use App\Models\KegiatanAlumni;
use App\Models\Post;
use App\Models\PpdbGelombang;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_navigation_link_has_a_public_destination(): void
    {
        $response = $this->get('/tentang')->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML($response->getContent());
        $xpath = new \DOMXPath($dom);
        $urls = [];

        foreach ($xpath->query('//header//a[@href] | //aside[@id="mobile-menu-drawer"]//a[@href] | //footer//a[@href]') as $link) {
            $url = $link->getAttribute('href');
            $this->assertFalse(str_starts_with($url, '#'), 'Menu still uses a local fragment: '.$url);
            if (str_starts_with($url, url('/'))) {
                $urls[] = $url;
            }
        }

        foreach (array_unique($urls) as $url) {
            $this->get(strtok($url, '#'))->assertOk();
        }
    }

    public function test_ppdb_banner_uses_the_open_wave_and_school_settings(): void
    {
        Setting::create(['key' => 'school_name', 'value' => 'Sekolah Pengujian']);
        $response = $this->get('/kontak')->assertOk()->assertSee('Sekolah Pengujian');
        $response->assertDontSee('Gelombang I Dibuka');

        $wave = PpdbGelombang::create([
            'tahun_pendaftaran' => 2028,
            'nama_gelombang' => 'Gelombang Khusus',
            'tanggal_mulai' => today(),
            'tanggal_selesai' => today()->addMonth(),
            'status' => 'Dibuka',
        ]);
        $this->get('/kontak')->assertOk()->assertSee('PPDB 2028 · Gelombang Khusus Dibuka');
        $wave->update(['status' => 'Ditutup']);
        $this->get('/kontak')->assertOk()->assertDontSee('Gelombang Khusus Dibuka');
    }

    public function test_homepage_links_point_to_existing_pages_and_sections(): void
    {
        $dom = new \DOMDocument;
        @$dom->loadHTML($this->get('/')->assertOk()->getContent());
        $xpath = new \DOMXPath($dom);
        $urls = [];
        foreach ($xpath->query('//main//a[@href]') as $link) {
            $urls[] = $link->getAttribute('href');
        }
        foreach (array_unique($urls) as $url) {
            $this->assertFalse(str_starts_with($url, '#'), 'Home link has no page destination: '.$url);
            $response = $this->get(strtok($url, '#'))->assertOk();
            if ($fragment = parse_url($url, PHP_URL_FRAGMENT)) {
                $response->assertSee('id="'.$fragment.'"', false);
            }
        }
    }

    public function test_publications_only_show_published_posts_in_the_matching_category(): void
    {
        $user = User::factory()->create();
        foreach (['pengumuman', 'prestasi', 'berita'] as $category) {
            $kategori = Kategori::create(['nama_kategori' => ucfirst($category), 'slug' => $category]);
            foreach (['Published', 'Draft'] as $status) {
                Post::create([
                    'user_id' => $user->id, 'kategori_id' => $kategori->id,
                    'judul' => $category.' '.$status, 'slug' => $category.'-'.strtolower($status),
                    'konten' => 'Isi publikasi', 'status' => $status,
                ]);
            }
        }
        $this->get('/pengumuman')->assertOk()->assertSee('pengumuman Published')
            ->assertDontSee('pengumuman Draft')->assertDontSee('prestasi Published')->assertDontSee('berita Published');
        $this->get('/prestasi')->assertOk()->assertSee('prestasi Published')
            ->assertDontSee('prestasi Draft')->assertDontSee('pengumuman Published');
    }

    public function test_alumni_directory_only_displays_verified_public_records_without_private_contacts(): void
    {
        foreach ([['Verified', true, 'Publik'], ['Pending', true, 'Pending'], ['Verified', false, 'Privat']] as [$status, $public, $name]) {
            $user = User::factory()->create(['name' => 'Alumni '.$name]);
            $siswa = Siswa::create(['user_id' => $user->id, 'nis' => $name, 'jenjang' => 'SMA', 'status' => 'Lulus']);
            KegiatanAlumni::create([
                'siswa_id' => $siswa->id, 'jenis_kegiatan' => 'Kuliah',
                'nama_instansi' => 'Universitas '.$name, 'tahun_mulai' => '2026',
                'status_verifikasi' => $status, 'is_public' => $public,
            ]);
        }
        $this->get('/alumni')->assertOk()->assertSee('Universitas Publik')
            ->assertDontSee('Universitas Pending')->assertDontSee('Universitas Privat');
    }

    public function test_calendar_displays_current_and_upcoming_school_events(): void
    {
        KalenderAkademik::create(['tanggal' => today()->startOfMonth()->subDay(), 'keterangan' => 'Agenda Lama', 'is_libur' => false]);
        KalenderAkademik::create(['tanggal' => today(), 'keterangan' => 'Kegiatan Terbaru', 'is_libur' => false]);
        $this->get('/kalender-akademik')->assertOk()->assertSee('Kegiatan Terbaru')->assertDontSee('Agenda Lama');
    }

    public function test_footer_information_pages_are_available(): void
    {
        foreach (['galeri', 'kebijakan-privasi', 'standar-pelayanan', 'peta-situs'] as $page) {
            $this->get('/'.$page)->assertOk();
        }
    }
}
