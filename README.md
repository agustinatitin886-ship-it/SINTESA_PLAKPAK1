# SINTESA - SPK Siswa Berprestasi SD Negeri Plakpak 1 Pegantenan
### Sistem Pendukung Keputusan Penilaian Siswa Berprestasi Menggunakan Metode BWM dan WASPAS Berbasis Web

Aplikasi web Sistem Pendukung Keputusan (SPK) yang dirancang secara khusus untuk **SD Negeri Plakpak 1 Pegantenan**, Kabupaten Pamekasan, Jawa Timur. Sistem ini mengintegrasikan metode **Best-Worst Method (BWM)** untuk pembobotan kriteria secara konsisten dan **WASPAS (Weighted Aggregated Sum Product Assessment)** untuk perankingan alternatif siswa berprestasi secara objektif, transparan, dan akuntabel.

---

## 1. Arsitektur & Spesifikasi Teknologi
- **Framework Back-end**: PHP 8.2+ / Laravel 12 (Model-View-Controller)
- **Basis Data**: SQLite / MySQL (MariaDB)
- **Antarmuka (Front-end)**: HTML5, CSS3, Bootstrap 5.3.3, Bootstrap Icons
- **Visualisasi Data**: Chart.js 4.4.1 (Doughnut, Bar, Radar Charts)
- **Metode Matematika SPK**:
  1. **BWM (Best-Worst Method)**: Menggunakan optimasi Linear Programming (Two-Phase Simplex Solver) dengan evaluasi Rasio Konsistensi ($CR \le 0.10$).
  2. **WASPAS (Weighted Aggregated Sum Product Assessment)**: Kombinasi WSM ($Q_i^{(1)}$) dan WPM ($Q_i^{(2)}$) dengan parameter agregasi $\lambda$.

---

## 2. Hak Akses & Fitur Pengguna (3 Aktor)

### A. Modul Administrator (Staf Tata Usaha / Kurikulum)
1. **Dashboard Admin**: Ringkasan data master, visualisasi grafik bobot kriteria BWM, distribusi siswa per kelas, dan status pengesahan SK Kepala Sekolah.
2. **Manajemen Pengguna**: Fitur CRUD untuk mengelola akun Wali Kelas, Kepala Sekolah, dan Admin.
3. **Manajemen Kriteria**: Data master kriteria, atribut (*Benefit* / *Cost*), dan nilai bobot.
4. **Manajemen Siswa (Data Alternatif)**: Pengelolaan data kandidat siswa (NISN, Nama Lengkap, Kelas, Jenis Kelamin, Tahun Ajaran).
5. **Kalkulator BWM (Best-Worst Method)**:
   - Pemilihan Kriteria Terbaik ($C_B$) dan Terburuk ($C_W$).
   - Form input perbandingan vektor *Best-to-Others* ($a_{Bj}$) dan *Others-to-Worst* ($a_{jW}$) skala Saaty 1-9.
   - Solver otomatis nilai bobot kriteria ($w_j$), nilai optimal $\xi^*$, Indeks Konsistensi ($CI$), dan Rasio Konsistensi ($CR$).
   - Penyimpanan riwayat ke tabel `log_bwm`.
6. **Kalkulator WASPAS**:
   - Tampilan Matriks Keputusan ($X$), Matriks Ternormalisasi ($R$), perhitungan WSM, WPM, dan Skor Akhir WASPAS ($Q_i$).
   - Slider pengaturan parameter agregasi $\lambda$ (default 0.5).
   - Tombol simpan dan ajukan hasil ke Kepala Sekolah.
7. **Cetak & Ekspor Laporan**:
   - Ekspor laporan perankingan ke format Excel (.CSV).
   - Cetak dokumen resmi dengan Kop Surat SDN Plakpak 1 Pegantenan.

### B. Modul Wali Kelas (Data Entry)
1. **Dashboard Personal**: Ringkasan jumlah siswa binaan pada kelasnya, status pengisian nilai (lengkap / belum).
2. **Input Penilaian**:
   - Form per siswa dengan panduan jenis kriteria.
   - **Mode Matriks Cepat**: Form input spreadsheet-like untuk mengisi seluruh nilai siswa kelas sekaligus dalam satu layar.
3. **Rekapitulasi Nilai Mentah**: Melihat tabel rekap nilai mentah siswa sebelum diproses dan dihitung oleh Admin.

### C. Modul Kepala Sekolah (Decision Maker)
1. **Dashboard Pimpinan**:
   - Pemantauan eksekutif dan status pengesahan surat keputusan.
   - Podium Juara Utama Top 3 Siswa Berprestasi.
   - Grafik performa rata-rata skor WASPAS tiap jenjang kelas.
   - Radar chart ketercapaian kriteria sekolah.
2. **Verifikasi Hasil**: Memeriksa daftar peringkat akhir siswa beserta rincian nilai WSM, WPM, dan $Q_i$.
3. **Persetujuan (Digital Approval)**: Klik validasi digital untuk mengesahkan perankingan secara sah (Disetujui / Ditolak / Revisi) dengan catatan resmi.
4. **Unduh & Cetak SK Resmi**: Format Surat Keputusan (SK) Kepala Sekolah resmi lengkap dengan Konsideran, Diktum Ketetapan, Lampiran Nama Juara, dan Tanda Tangan Digital.

---

## 3. Akun Pengguna Bawaan (Default Login Demo)

Pada halaman login telah disediakan tombol **Quick Test (1-Klik Auto Fill)** untuk memudahkan pengujian:

| Role Pengguna | Username | Password | Keterangan / Nama |
|---|---|---|---|
| **Administrator TU** | `admin` | `admin123` | Budi Santoso, S.Kom. (Tata Usaha) |
| **Wali Kelas 6** | `walikelas3` | `wali123` | Nurul Hidayati, S.Pd. (Wali Kelas 6) |
| **Wali Kelas 5** | `walikelas2` | `wali123` | Moh. Farhan, S.Pd. (Wali Kelas 5) |
| **Wali Kelas 4** | `walikelas1` | `wali123` | Siti Rahmawati, S.Pd. (Wali Kelas 4) |
| **Kepala Sekolah** | `kepsek` | `kepsek123` | H. Achmad Zaini, S.Pd., M.Pd. (Kepsek) |

---

## 4. Struktur Database

Sistem telah dilengkapi skema database relasional lengkap:
1. `users`: Autentikasi dan hak akses (role: admin, walikelas, kepsek).
2. `kriteria`: Parameter penilaian, jenis atribut (Benefit/Cost), dan bobot BWM ($w_j$).
3. `siswa`: Data induk kandidat alternatif (NISN, Nama, Kelas, Tahun Ajaran).
4. `penilaian`: Nilai mentah siswa pada tiap kriteria.
5. `hasil_waspas`: Hasil perankingan akhir WASPAS (WSM, WPM, Qi, Peringkat).
6. `log_bwm`: Rekam jejak preferensi dan rasio konsistensi BWM.
7. `approval_kepsek`: Rekam pengesahan digital dan nomor SK Kepala Sekolah.

---

## 5. Cara Menjalankan Aplikasi

### Pilihan A: Melalui Laragon (Rekomendasi)
1. Pastikan folder berada di `C:\laragon\www\SINTESA_PLAKPAK_1`.
2. Buka Laragon, klik **Start All**.
3. Akses melalui browser: `http://sintesa_plakpak_1.test` atau `http://localhost/SINTESA_PLAKPAK_1/public`.

### Pilihan B: Menggunakan PHP Artisan Serve
1. Buka terminal di folder project:
   ```bash
   php artisan serve
   ```
2. Buka browser pada alamat:
   ```
   http://127.0.0.1:8000
   ```
