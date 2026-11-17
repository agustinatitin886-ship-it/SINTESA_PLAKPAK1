<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kriteria;
use App\Models\Siswa;
use App\Models\Penilaian;
use App\Models\LogBwm;
use App\Models\ApprovalKepsek;
use App\Models\HasilWaspas;
use App\Services\BwmService;
use App\Services\WaspasService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::create([
            'username' => 'admin',
            'nama_pengguna' => 'Budi Santoso, S.Kom. (Admin TU)',
            'email' => 'admin@sdnplakpak1.sch.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'kelas_binaan' => null,
        ]);

        $wali1 = User::create([
            'username' => 'walikelas1',
            'nama_pengguna' => 'Siti Rahmawati, S.Pd.',
            'email' => 'wali4@sdnplakpak1.sch.id',
            'password' => Hash::make('wali123'),
            'role' => 'walikelas',
            'kelas_binaan' => 'Kelas 4',
        ]);

        $wali2 = User::create([
            'username' => 'walikelas2',
            'nama_pengguna' => 'Moh. Farhan, S.Pd.',
            'email' => 'wali5@sdnplakpak1.sch.id',
            'password' => Hash::make('wali123'),
            'role' => 'walikelas',
            'kelas_binaan' => 'Kelas 5',
        ]);

        $wali3 = User::create([
            'username' => 'walikelas3',
            'nama_pengguna' => 'Nurul Hidayati, S.Pd.',
            'email' => 'wali6@sdnplakpak1.sch.id',
            'password' => Hash::make('wali123'),
            'role' => 'walikelas',
            'kelas_binaan' => 'Kelas 6',
        ]);

        $kepsek = User::create([
            'username' => 'kepsek',
            'nama_pengguna' => 'H. Achmad Zaini, S.Pd., M.Pd.',
            'email' => 'kepsek@sdnplakpak1.sch.id',
            'password' => Hash::make('kepsek123'),
            'role' => 'kepsek',
            'kelas_binaan' => null,
        ]);

        // 2. Seed Kriteria
        $kriteriaData = [
            ['kode' => 'C1', 'nama' => 'Nilai Rata-rata Rapor', 'jenis' => 'Benefit', 'bobot' => 0.4158],
            ['kode' => 'C2', 'nama' => 'Kehadiran / Presensi', 'jenis' => 'Benefit', 'bobot' => 0.2366],
            ['kode' => 'C3', 'nama' => 'Sikap dan Karakter', 'jenis' => 'Benefit', 'bobot' => 0.1577],
            ['kode' => 'C4', 'nama' => 'Prestasi & Ekstrakurikuler', 'jenis' => 'Benefit', 'bobot' => 0.1183],
            ['kode' => 'C5', 'nama' => 'Pelanggaran / Kedisiplinan', 'jenis' => 'Cost', 'bobot' => 0.0716],
        ];

        $kriteriaModels = [];
        foreach ($kriteriaData as $kd) {
            $kriteriaModels[$kd['kode']] = Kriteria::create([
                'kode_kriteria' => $kd['kode'],
                'nama_kriteria' => $kd['nama'],
                'jenis_atribut' => $kd['jenis'],
                'bobot_bwm' => $kd['bobot'],
            ]);
        }

        // 3. Seed BWM Log
        $c1 = $kriteriaModels['C1'];
        $c5 = $kriteriaModels['C5'];
        $bToO = [
            $c1->id_kriteria => 1,
            $kriteriaModels['C2']->id_kriteria => 2,
            $kriteriaModels['C3']->id_kriteria => 3,
            $kriteriaModels['C4']->id_kriteria => 4,
            $c5->id_kriteria => 5,
        ];
        $oToW = [
            $c1->id_kriteria => 5,
            $kriteriaModels['C2']->id_kriteria => 4,
            $kriteriaModels['C3']->id_kriteria => 3,
            $kriteriaModels['C4']->id_kriteria => 2,
            $c5->id_kriteria => 1,
        ];

        LogBwm::create([
            'best_kriteria_id' => $c1->id_kriteria,
            'worst_kriteria_id' => $c5->id_kriteria,
            'vektor_best_to_others' => $bToO,
            'vektor_others_to_worst' => $oToW,
            'consistency_ratio' => 0.0249,
            'xi_value' => 0.0573,
            'periode' => '2024/2025',
        ]);

        // 4. Seed Siswa (Alternatif)
        $siswaData = [
            ['nisn' => '0101928371', 'nama' => 'Ahmad Faiz Al-Ghifari', 'kelas' => 'Kelas 6', 'jk' => 'L', 'ta' => '2024/2025', 'nilai' => [92.5, 96.0, 90.0, 85.0, 0]],
            ['nisn' => '0101928372', 'nama' => 'Zahra Putri Wardani', 'kelas' => 'Kelas 6', 'jk' => 'P', 'ta' => '2024/2025', 'nilai' => [95.0, 98.0, 95.0, 92.0, 0]],
            ['nisn' => '0101928373', 'nama' => 'Muhammad Rifqi Pratama', 'kelas' => 'Kelas 6', 'jk' => 'L', 'ta' => '2024/2025', 'nilai' => [88.0, 92.0, 85.0, 75.0, 1]],
            ['nisn' => '0101928374', 'nama' => 'Nabila Syifa Az-Zahra', 'kelas' => 'Kelas 6', 'jk' => 'P', 'ta' => '2024/2025', 'nilai' => [86.5, 90.0, 88.0, 80.0, 1]],

            ['nisn' => '0112938471', 'nama' => 'Fathan Dwi Saputra', 'kelas' => 'Kelas 5', 'jk' => 'L', 'ta' => '2024/2025', 'nilai' => [91.0, 94.0, 92.0, 88.0, 0]],
            ['nisn' => '0112938472', 'nama' => 'Khadijah Ayu Lestari', 'kelas' => 'Kelas 5', 'jk' => 'P', 'ta' => '2024/2025', 'nilai' => [93.5, 97.0, 90.0, 85.0, 0]],
            ['nisn' => '0112938473', 'nama' => 'Bagas Maulana Malik', 'kelas' => 'Kelas 5', 'jk' => 'L', 'ta' => '2024/2025', 'nilai' => [84.0, 88.0, 82.0, 70.0, 2]],
            ['nisn' => '0112938474', 'nama' => 'Salma Kirana Dewi', 'kelas' => 'Kelas 5', 'jk' => 'P', 'ta' => '2024/2025', 'nilai' => [89.0, 93.0, 89.0, 82.0, 0]],

            ['nisn' => '0123948571', 'nama' => 'Dimas Aditya Nugraha', 'kelas' => 'Kelas 4', 'jk' => 'L', 'ta' => '2024/2025', 'nilai' => [90.0, 95.0, 88.0, 84.0, 0]],
            ['nisn' => '0123948572', 'nama' => 'Aisyah Humaira', 'kelas' => 'Kelas 4', 'jk' => 'P', 'ta' => '2024/2025', 'nilai' => [94.0, 99.0, 94.0, 90.0, 0]],
            ['nisn' => '0123948573', 'nama' => 'Rizky Bilal Akbar', 'kelas' => 'Kelas 4', 'jk' => 'L', 'ta' => '2024/2025', 'nilai' => [82.0, 85.0, 80.0, 65.0, 3]],
            ['nisn' => '0123948574', 'nama' => 'Tiara Anindya Putri', 'kelas' => 'Kelas 4', 'jk' => 'P', 'ta' => '2024/2025', 'nilai' => [87.5, 91.0, 86.0, 78.0, 1]],
        ];

        $kriteriaKeys = ['C1', 'C2', 'C3', 'C4', 'C5'];

        foreach ($siswaData as $sd) {
            $siswa = Siswa::create([
                'nisn' => $sd['nisn'],
                'nama_lengkap' => $sd['nama'],
                'kelas' => $sd['kelas'],
                'jenis_kelamin' => $sd['jk'],
                'tahun_ajaran' => $sd['ta'],
            ]);

            foreach ($kriteriaKeys as $index => $kCode) {
                Penilaian::create([
                    'id_siswa' => $siswa->id_siswa,
                    'id_kriteria' => $kriteriaModels[$kCode]->id_kriteria,
                    'nilai_mentah' => $sd['nilai'][$index],
                ]);
            }
        }

        // 5. Run initial WASPAS ranking so dashboard has rich data right out of the box
        $waspasService = new WaspasService();
        $waspasRes = $waspasService->calculate('2024/2025', 0.5);

        if ($waspasRes['success']) {
            foreach ($waspasRes['results'] as $item) {
                HasilWaspas::create([
                    'id_siswa' => $item['id_siswa'],
                    'periode' => '2024/2025',
                    'nilai_wsm' => $item['wsm'],
                    'nilai_wpm' => $item['wpm'],
                    'nilai_qi' => $item['qi'],
                    'peringkat' => $item['peringkat'],
                    'peringkat_kelas' => $item['peringkat_kelas'] ?? 1,
                    'status_kelayakan' => $item['status_kelayakan'] ?? 'Kandidat Berprestasi',
                ]);
            }
        }

        // 6. Seed Approval Kepsek (Status: Menunggu atau Disetujui)
        ApprovalKepsek::create([
            'periode' => '2024/2025',
            'status' => 'menunggu',
            'nomor_sk' => '421.2/084/SDN.PLK.1/2025',
            'catatan' => 'Hasil perankingan periode 2024/2025 menunggu verifikasi dan persetujuan Kepala Sekolah.',
            'tanggal_persetujuan' => null,
            'disetujui_oleh' => null,
        ]);
    }
}
