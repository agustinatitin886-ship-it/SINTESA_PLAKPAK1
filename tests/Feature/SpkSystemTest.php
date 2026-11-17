<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Kriteria;
use App\Models\Siswa;
use App\Models\Penilaian;
use App\Models\HasilWaspas;
use App\Models\ApprovalKepsek;
use App\Services\BwmService;
use App\Services\WaspasService;

class SpkSystemTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_flexible_login_scenarios()
    {
        // 1. Standard admin login
        $res = $this->post('/login', ['username' => 'admin', 'password' => 'admin123']);
        $res->assertRedirect('/admin/dashboard');

        $this->post('/logout');

        // 2. Case-insensitive & fallback password
        $res = $this->post('/login', ['username' => 'Admin', 'password' => 'admin']);
        $res->assertRedirect('/admin/dashboard');

        $this->post('/logout');

        // 3. Email login
        $res = $this->post('/login', ['username' => 'admin@sdnplakpak1.sch.id', 'password' => 'admin123']);
        $res->assertRedirect('/admin/dashboard');

        $this->post('/logout');

        // 4. Wali kelas login (alias 'walikelas' without number)
        $res = $this->post('/login', ['username' => 'walikelas', 'password' => 'wali123']);
        $res->assertRedirect('/walikelas/dashboard');

        $this->post('/logout');

        // 5. Kepsek login with alias
        $res = $this->post('/login', ['username' => 'kepsek', 'password' => 'kepsek123']);
        $res->assertRedirect('/kepsek/dashboard');
    }

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/walikelas/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/kepsek/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_routes()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $this->get('/admin/dashboard')->assertStatus(200)->assertSee('Dasbor Administrator');
        $this->get('/admin/kriteria')->assertStatus(200)->assertSee('Kriteria Penilaian');
        $this->get('/admin/siswa')->assertStatus(200)->assertSee('Data Siswa');
        $this->get('/admin/users')->assertStatus(200)->assertSee('Manajemen Pengguna');
        $this->get('/admin/bwm')->assertStatus(200)->assertSee('Pembobotan Kriteria Metode Best-Worst');
        $this->get('/admin/waspas')->assertStatus(200)->assertSee('Perankingan Siswa Metode WASPAS');
        $this->get('/admin/laporan')->assertStatus(200)->assertSee('Laporan Siswa Berprestasi');
        $this->get('/admin/laporan/per-kelas')->assertStatus(200)->assertSee('Rekapitulasi Siswa Berprestasi per Jenjang Kelas');
        $this->get('/admin/laporan/per-kelas/cetak')->assertStatus(200)->assertSee('REKAPITULASI PENETAPAN SISWA BERPRESTASI PER TINGKAT KELAS');
    }

    public function test_walikelas_can_access_walikelas_routes()
    {
        $wali = User::where('role', 'walikelas')->first();
        $this->actingAs($wali);

        $this->get('/walikelas/dashboard')->assertStatus(200)->assertSee('Dasbor Wali Kelas');
        $this->get('/walikelas/penilaian')->assertStatus(200)->assertSee('Input Penilaian Siswa');
        $this->get('/walikelas/penilaian/matriks')->assertStatus(200)->assertSee('Matriks Penilaian Siswa');
        $this->get('/walikelas/penilaian/rekap')->assertStatus(200)->assertSee('Rekapitulasi Nilai Mentah');

        // Wali Kelas should not access admin routes
        $this->get('/admin/dashboard')->assertRedirect('/walikelas/dashboard');
    }

    public function test_kepsek_can_access_kepsek_routes()
    {
        $kepsek = User::where('role', 'kepsek')->first();
        $this->actingAs($kepsek);

        $this->get('/kepsek/dashboard')->assertStatus(200)->assertSee('Dasbor Pimpinan');
        $this->get('/kepsek/verifikasi')->assertStatus(200)->assertSee('Verifikasi & Pengesahan');
        $this->get('/kepsek/verifikasi/sk')->assertStatus(200)->assertSee('SURAT KEPUTUSAN');

        // Kepsek should not access admin routes
        $this->get('/admin/dashboard')->assertRedirect('/kepsek/dashboard');
    }

    public function test_bwm_and_waspas_services()
    {
        $bwmService = new BwmService();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $this->assertGreaterThanOrEqual(2, $kriteria->count());

        $best = $kriteria->first();
        $worst = $kriteria->last();

        $bToO = [];
        $oToW = [];
        foreach ($kriteria as $k) {
            $bToO[$k->id_kriteria] = 1;
            $oToW[$k->id_kriteria] = 1;
        }

        $resBwm = $bwmService->calculate($kriteria->all(), $best->id_kriteria, $worst->id_kriteria, $bToO, $oToW);
        $this->assertTrue($resBwm['success']);
        $this->assertLessThanOrEqual(0.15, $resBwm['cr']);

        $waspasService = new WaspasService();
        $resWaspas = $waspasService->calculate('2024/2025', 0.5);
        $this->assertTrue($resWaspas['success']);
        $this->assertNotEmpty($resWaspas['results']);
        $this->assertEquals(1, $resWaspas['results'][0]['peringkat']);
    }
}
