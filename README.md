# LMS Telkom University (Laravel 13)

Sistem manajemen pembelajaran (LMS) sederhana dengan tiga peran: `admin`, `dosen`, dan `mahasiswa`. Hanya memakai database lokal (SQLite), tanpa API eksternal atau layanan pihak ketiga. Autentikasi (siapa Anda) ditangani login di `AutentikasiController`. Otorisasi (apa yang boleh Anda buka) ditangani middleware `CheckRole` per rute, ditambah pembatasan per data di tiap controller.

## Status verifikasi

Kode ini belum dijalankan. Lingkungan penyusunnya tidak punya PHP maupun akses jaringan, jadi seluruh berkas hanya diperiksa lewat inspeksi kode dan pemeriksaan struktur (kurung seimbang, nama rute, nama view, dan nama metode controller saling cocok). Jalankan daftar uji manual di bagian "Pengujian manual" sebelum dipakai, dan laporkan galat yang muncul.

## Menjalankan proyek

Folder `vendor` dan `node_modules` tidak disertakan. Dari folder proyek:

```bash
composer install
npm install
php artisan migrate:fresh --seed
php artisan serve
```

- `npm install` hanya mengunduh paket ikon `@phosphor-icons/core`. Tidak ada proses build: ikon dibaca langsung dari `node_modules/@phosphor-icons/core/assets/regular/` dan disisipkan sebagai SVG. Bila `npm install` belum dijalankan, aplikasi tetap berfungsi tetapi ikon tampil sebagai titik kecil.
- Gunakan `migrate:fresh` (bukan `migrate`) karena struktur tabel berubah dari versi sebelumnya (kolom `kedaluwarsa_pada` pada berita).
- Berkas `.env` dan `database/database.sqlite` sudah ada, jadi tidak perlu `key:generate`. Zona waktu Asia/Jakarta, bahasa awal Indonesia.
- Batas unggah berkas bawaan PHP adalah 2 MB (`upload_max_filesize` dan `post_max_size` di `php.ini`). Aplikasi mengizinkan 10 MB untuk materi dan tugas serta 5 MB untuk banner berita, jadi naikkan batas di `php.ini` bila perlu.

## Akun uji

| Peran | Email | Kata sandi |
|-------|-------|------------|
| admin | admin@admin.telkomuniversity.ac.id | password |
| dosen | dosen@dosen.telkomuniversity.ac.id | password |
| dosen (tanpa kelas) | dosen2@dosen.telkomuniversity.ac.id | password |
| mahasiswa | mahasiswa@student.telkomuniversity.ac.id | password |
| mahasiswa (tanpa kelas) | mahasiswa2@student.telkomuniversity.ac.id | password |

Seeder juga membuat program studi Informatika, tahun ajaran "2026/2027 Ganjil" (aktif), dua mata kuliah (IF-PAW dan IF-KKD) dengan kelas A diampu Dosen Uji, Mahasiswa Uji terdaftar di keduanya, 14 pertemuan per kelas, dan satu berita sambutan. Seeder berhenti sendiri di lingkungan `production`. Hapus atau ganti akun ini sebelum dipakai pengguna sungguhan. Email tidak benar-benar dikirim ke mana pun; domain `telkomuniversity.ac.id` hanya format penamaan akun di database lokal.

## Halaman sebelum login

Ada lima halaman: beranda (`/`), FAQ (`/faq`), Tutorial (`/tutorial`), masuk (`/login`), dan lupa kata sandi (`/lupa-kata-sandi`). Desainnya mengikuti referensi halaman LMS yang Anda lampirkan: bilah navigasi kaca di atas, kartu utama merah Telkom, tombol masuk berbingkai merah dengan ikon gembok, dan catatan singkat di bawahnya. Isinya branding (nama aplikasi dari `APP_NAME` di `.env`, sekarang "LMS Telkom University") dan akses autentikasi. FAQ dan Tutorial hanya berisi panduan cara mendapatkan akun, masuk, mengatur ulang kata sandi, mengganti bahasa dan tema, serta pesan "Jika ada pertanyaan, silakan chat admin."; keduanya tidak menjelaskan fitur LMS. Antarmuka sebelum login selebihnya tidak menampilkan atau menjelaskan fitur LMS; deskripsi fitur ada di README ini. Bagian referensi yang sengaja tidak dibuat: menu About dan panel pengumuman. Kontak admin hanya berupa teks; belum ada fitur chat di aplikasi. Logo dan merek resmi institusi tidak disertakan.

Tidak ada pendaftaran akun mandiri: tidak ada rute, tombol, maupun halaman "Daftar", dan `/register` menghasilkan 404. Akun hanya dibuat admin.

### Lupa kata sandi

Alur lokal tanpa email: pengguna mengisi email di `/lupa-kata-sandi`, sistem mencatat permintaan, lalu admin melihatnya di halaman Pengguna (bagian "Permintaan reset kata sandi"), mengatur kata sandi baru, dan menyampaikannya kepada pemilik akun. Permintaan otomatis ditandai selesai saat admin menyimpan kata sandi baru, atau bisa diabaikan. Jawaban formulir selalu sama, ada atau tidaknya akun, supaya daftar email terdaftar tidak bisa ditebak. Formulir dibatasi 5 permintaan per menit.

### Tata letak satu layar

Setiap halaman sebelum login dirancang muat penuh dalam satu viewport tanpa gulir halaman dan tanpa elemen terpotong:

- Tinggi halaman sama dengan tinggi viewport (`100dvh`), dibagi dua baris: isi (mengambil sisa ruang dengan `minmax(0, 1fr)`) dan kaki, dengan ruang untuk bilah navigasi tetap di atas (desktop) atau di atas dan di bawah (tablet dan ponsel). `body` memakai `overflow: hidden`.
- Semua ukuran memakai satuan `rem`, dan ukuran dasar `rem` dihitung dari ukuran jendela: `clamp(9px, min(2.4dvh, 4vw), 16px)`. Saat jendela pendek atau sempit, seluruh elemen mengecil bersama dan tetap utuh.
- Cadangan: kartu formulir dapat digulir sendiri bila isinya (misalnya banyak pesan galat) melebihi ruang yang tersedia, sehingga tidak ada bagian yang terpotong.
- Konsekuensi: ukuran huruf dasar halaman ini tidak mengikuti pengaturan ukuran huruf browser. Pada jendela yang sangat pendek (sekitar 300 piksel tinggi atau kurang, misalnya zoom 400%) teks menjadi sangat kecil karena batas minimumnya 9px.
- Halaman galat bawaan Laravel (misalnya 429 saat terlalu banyak percobaan login) tidak memakai tata letak ini.

## Tampilan (Liquid Glass, tema, bahasa, navigasi)

**Warna.** Latar dasar putih polos (mode terang) atau hitam polos (mode gelap). Tidak ada biru, ungu, atau gradien warna. Warna merah Telkom (`#d6141c`, dan `#ff5a61` untuk teks merah di mode gelap) dipakai untuk tombol dan aksen interaktif utama: tombol, tautan, cincin fokus, menu aktif, dan kotak centang. Semua elemen lain netral (abu-abu, putih, hitam). Lencana status bersifat netral, dan status negatif (Terlambat, Kedaluwarsa) berwarna merah.

**Liquid Glass.** Bilah navigasi, kartu, tabel, formulir, notifikasi, dan dialog berpermukaan kaca: transparansi, `backdrop-filter: blur`, tepi tipis semi-transparan, kilau tipis di tepi atas, dan bayangan lembut, di atas latar polos. Di atas latar polos kaca paling terlihat saat konten bergulir di bawah bilah navigasi. Transisi halus saat disorot. Browser tanpa `backdrop-filter`, atau pengguna yang memilih "kurangi transparansi", mendapat permukaan solid; pengguna yang memilih "kurangi gerakan" tidak melihat animasi.

**Tema.** Tombol tema di bilah atas bergilir Sistem, Terang, Gelap. Bawaannya mengikuti tema perangkat (`prefers-color-scheme`); pilihan disimpan di `localStorage` browser dan diterapkan sebelum halaman tampil (tanpa kedip).

**Bahasa.** Tombol bahasa (ikon terjemahan dan kode ID atau EN) berpindah antara Indonesia dan Inggris di seluruh antarmuka, termasuk halaman sebelum login, pesan galat, dan notifikasi. Pilihan disimpan di sesi dan cookie. Cara kerjanya: teks sumber aplikasi berbahasa Indonesia, dan untuk bahasa Inggris, `TerjemahkanHalaman` (middleware) mengganti teks antarmuka berdasarkan kamus `lang/en.json` sebelum halaman dikirim, sedangkan pesan validasi memakai berkas bahasa Laravel (`lang/id`, `lang/en`). Teks yang tidak ada di kamus tetap berbahasa Indonesia. Konten yang diketik pengguna (judul berita, nama, materi) tidak diterjemahkan. Untuk menambah atau memperbaiki terjemahan, ubah `lang/en.json`: kunci adalah teks Indonesia, dan token `:a`, `:b` mewakili bagian yang berubah (`:n_a` khusus angka).

**Navigasi.** Di desktop (lebar 1024 piksel ke atas), bilah navigasi kaca menempel di atas layar dan tetap terlihat saat halaman digulir. Isinya ikon saja (ikon menu di tengah, bahasa, tema, dan keluar di kanan); nama menu muncul sebagai tooltip saat kursor diarahkan atau saat ikon difokus dengan keyboard. Di tablet dan ponsel, menu pindah ke bilah bawah yang menempel di bawah layar (ikon dengan label kecil, dapat digeser bila menu banyak, dan menghormati area aman perangkat), sedangkan merek, bahasa, tema, dan keluar tetap di bilah tipis di atas.

**Ikon.** Semua ikon berasal dari Phosphor Icons (gaya Regular) lewat paket `@phosphor-icons/core`, dibaca oleh `App\Support\Ikon` dan disisipkan sebagai SVG.

**Notifikasi (toast).** Setiap tambah, ubah, atau hapus data (dan pesan galat tindakan) menampilkan notifikasi mengambang yang hilang sendiri setelah 5 detik, dengan bilah waktu dan tombol tutup untuk menutupnya lebih awal.

**Responsif.** Kartu berita 3, 2, atau 1 kolom menurut lebar layar, tabel dapat digeser horizontal, formulir satu kolom di layar sempit, tombol dan menu berukuran sentuh, serta dukungan area aman (notch dan bilah gestur).

## Berita kampus

- **Dasbor:** maksimal 3 kartu berita terlihat sekaligus (2 di tablet, 1 di ponsel). Bila ada lebih banyak berita, kartu tersusun dalam penggeser horizontal dengan tombol panah kiri dan kanan (muncul hanya bila perlu, nonaktif di ujung), selain geser sentuh dan keyboard.
- **Detail:** klik kartu mana pun membuka halaman detail berisi teks lengkap dan gambar dalam ukuran aslinya tanpa dipotong (hanya diperkecil bila lebih lebar dari layar, dengan tautan "Buka gambar ukuran asli"). Kartu di dasbor hanya menampilkan ringkasan.
- **Tanpa gambar:** bila berita tidak punya gambar, wadah gambar tidak ditampilkan sama sekali, di dasbor, detail, maupun daftar admin.
- **Kedaluwarsa:** berita dapat diberi tanggal kedaluwarsa (opsional, berlaku sampai akhir tanggal itu). Berita kedaluwarsa hanya terlihat oleh admin dan dosen, ditandai "Kedaluwarsa"; mahasiswa tidak melihatnya dan mendapat 404 bila membuka alamatnya. Berita bertanggal terbit masa depan hanya terlihat oleh admin ("Terjadwal").
- **Banner:** resolusi maksimal 1280 x 720 piksel. Setelah memilih gambar yang lebih besar, alat potong interaktif terbuka otomatis: seret untuk menggeser, penggeser atau tombol plus dan minus untuk memperbesar, tombol panah untuk menggeser halus, lalu "Terapkan potongan" menghasilkan JPEG 1280 x 720 yang diunggah (dapat dipotong ulang sebelum disimpan). Gambar yang sudah sesuai langsung dipakai. Server memeriksa batas resolusi lagi (maksimal 1280 x 720, 5 MB), jadi gambar terlalu besar yang lolos dari alat potong tetap ditolak.

## Fitur per peran

### Admin (akses penuh)

| Menu | Fitur |
|------|-------|
| Dasbor | Berita kampus, ringkasan jumlah akun, kelas, mata kuliah, dan permintaan reset |
| Berita | Tambah, ubah, hapus berita (judul, isi, tanggal terbit, tanggal kedaluwarsa opsional, banner dengan alat potong). Tampil di dasbor semua peran mulai tanggal terbit |
| Pengguna | Cari dan filter akun, ubah peran langsung, ubah nama, reset kata sandi, hapus akun, proses permintaan reset |
| Tambah akun | Satu atau banyak akun sekaligus lewat tabel bergaya Excel |
| Tahun ajaran | Tambah, ubah, hapus, tandai semester aktif |
| Program studi | Tambah, ubah, hapus |
| Mata kuliah | Kode, nama, SKS, program studi |
| Kelas | Buat kelas (mata kuliah, tahun ajaran, nama kelas, dosen pengampu), daftarkan atau keluarkan mahasiswa, ubah, hapus. Tombol Buka masuk ke seluruh menu kelas (materi, tugas, kuis, presensi, nilai) untuk kelas mana pun, termasuk koreksi presensi dan nilai serta ekspor rekap |

Admin tidak mengumpulkan tugas, mengerjakan kuis, atau check-in presensi karena itu tindakan mahasiswa; admin dapat mengubah semua datanya lewat menu kelas.

#### Pembuatan akun massal

Menu Tambah akun berisi tabel input: Nama lengkap, Username (opsional), Kata sandi (opsional). Pilih satu peran untuk semua baris. Daftar dari Excel dapat ditempel (Ctrl+V): tiap baris menjadi satu akun, kolom dipisah tab. Satu baris saja berarti satu akun. Email dibentuk otomatis:

| Peran | Format email |
|-------|--------------|
| admin | `username@admin.telkomuniversity.ac.id` |
| dosen | `username@dosen.telkomuniversity.ac.id` |
| mahasiswa | `username@student.telkomuniversity.ac.id` |

Username kosong dibuat dari nama (huruf kecil tanpa spasi), dan diberi angka di belakangnya bila sudah dipakai. Username yang diisi manual dan sudah dipakai akan dilewati dan dilaporkan. Kata sandi kosong dibuat acak 10 karakter. Hasilnya ditampilkan satu kali di halaman Pengguna (catat sebelum menutup halaman). Maksimal 200 baris per kiriman. Mengubah peran akun ikut mengubah domain email (bagian username tetap).

### Dosen

Dasbor menampilkan berita kampus di bagian atas (termasuk yang sudah kedaluwarsa) dan daftar kelas aktif semester ini. Semua pengelolaan hanya untuk kelas yang ia ampu:

| Menu kelas | Fitur |
|------------|-------|
| Ringkasan | Daftar mahasiswa terdaftar (hanya baca) |
| Materi | 14 pertemuan: topik, unggah berkas PDF, PPTX, atau DOCX (maks. 10 MB), tautan referensi http atau https |
| Tugas | Buat tugas dengan instruksi dan tenggat, lihat dan unduh pengumpulan, isi skor dan umpan balik |
| Kuis | Soal pilihan ganda (opsi A sampai D plus kunci) dan esai singkat, lihat jawaban, nilai dan umpan balik |
| Presensi | Buka dan tutup sesi per pertemuan, atur status Hadir, Izin, Sakit, Alpa, rekap persentase, ekspor CSV |
| Nilai | Atur bobot (tugas dan kuis, UTS, UAS, total 100), isi UTS, UAS, dan umpan balik, nilai akhir dan huruf otomatis, ekspor CSV |

### Mahasiswa

Dasbor menampilkan berita kampus, mata kuliah yang diikuti, dan pengingat tugas bertenggat mendatang yang belum dikumpulkan. Halaman tiap kelas memuat: materi per pertemuan (unduh berkas, buka tautan), tugas dengan status Terkumpul atau Terlambat, kuis online (satu kali pengerjaan), check-in presensi mandiri saat sesi dibuka beserta persentase kehadiran, serta rincian nilai (skor tugas, kuis, UTS, UAS, nilai akhir, huruf, dan umpan balik dosen). Mahasiswa hanya melihat data kelasnya dan nilainya sendiri.

## Aturan penilaian

- Nilai tugas dan kuis: rata-rata semua tugas dan semua kuis di kelas. Tugas atau kuis yang belum dikumpulkan atau belum dinilai dihitung 0.
- Nilai akhir = (tugas dan kuis x bobot tugas + UTS x bobot UTS + UAS x bobot UAS) / 100. UTS atau UAS yang belum diisi dihitung 0. Bobot awal 30, 30, 40.
- Kuis tanpa soal esai dinilai otomatis (persentase benar). Kuis dengan soal esai menunggu skor dari dosen. Dosen dapat menimpa skor kapan saja.
- Pengumpulan tugas setelah tenggat tetap diterima dan ditandai Terlambat. Setelah tugas dinilai, mahasiswa tidak bisa mengumpulkan ulang.
- Presensi: persentase = jumlah Hadir dibagi jumlah sesi yang sudah dibuat. Mahasiswa tanpa catatan pada sesi yang ada dihitung Alpa.
- Konversi huruf (asumsi, ubah di `PerhitunganNilai::BATAS_HURUF` sesuai pedoman akademik Anda): A mulai 80, AB 70, B 65, BC 60, C 50, D 40, E di bawah 40.

## Model data

| Tabel | Isi |
|-------|-----|
| `users` | `name`, `email`, `password`, `role` (bawaan `mahasiswa`, tidak ada di `#[Fillable]`) |
| `program_studi`, `tahun_ajaran`, `mata_kuliah` | Data master. Tahun ajaran punya penanda `aktif` (hanya satu) |
| `kelas` | Mata kuliah + tahun ajaran + nama kelas + dosen, serta bobot nilai |
| `pendaftaran_kelas` | Mahasiswa yang terdaftar di kelas |
| `pertemuan`, `materi` | 14 pertemuan per kelas, materi berupa berkas atau tautan |
| `tugas`, `pengumpulan_tugas` | Tugas dan berkas kiriman mahasiswa (status, skor, umpan balik) |
| `kuis`, `kuis_soal`, `kuis_percobaan` | Kuis, soal, dan jawaban mahasiswa |
| `sesi_presensi`, `presensi` | Sesi per pertemuan dan status kehadiran |
| `nilai_kelas` | UTS, UAS, dan catatan dosen per mahasiswa per kelas |
| `berita` | Berita kampus (judul, isi, tanggal terbit, tanggal kedaluwarsa, banner) |
| `permintaan_reset_kata_sandi` | Permintaan reset dari halaman lupa kata sandi |

Berkas materi dan pengumpulan tugas disimpan privat di `storage/app/private` dan hanya bisa diunduh lewat rute `/unduh/...` yang memeriksa hak akses. Banner berita disimpan di `public/uploads/berita` supaya langsung tampil tanpa `storage:link`.

## Matriks hak akses rute

| Rute | Peran |
|------|-------|
| `/admin`, `/admin/berita*`, `/admin/pengguna*`, `/admin/master/*`, `/admin/kelas*` | admin |
| `/dosen` | dosen |
| `/mahasiswa`, `/kelas-saya*` | mahasiswa |
| `/kelola/kelas*`, `/kelola/tugas*`, `/kelola/kuis*`, `/kelola/pertemuan*`, `/kelola/materi*`, `/kelola/soal*` | admin, dosen (dosen hanya untuk kelasnya) |
| `/berita/{id}` | semua peran yang login (mahasiswa tidak dapat membuka berita yang kedaluwarsa atau belum terbit) |
| `/bahasa/{kode}` | siapa saja (mengganti bahasa id atau en) |
| `/faq`, `/tutorial` | siapa saja (halaman bantuan sebelum login) |
| `/unduh/materi/*`, `/unduh/pengumpulan/*` | pengelola kelas atau mahasiswa terdaftar (pemilik berkas untuk pengumpulan) |
| `/kelola-materi`, `/kelola-nilai` | admin, dosen (pintasan ke `/kelola/kelas`) |
| `/lihat-materi`, `/nilai-saya` | mahasiswa (pintasan ke `/kelas-saya`) |

## Cara keamanan bekerja

**Autentikasi dan otorisasi dipisah.** Middleware `auth` menjawab "siapa Anda". `CheckRole` menjawab "apakah peran Anda boleh membuka rute ini". Tamu dialihkan ke `login`. Pengguna yang salah peran mendapat 403 "Anda tidak memiliki hak akses!".

**Dua lapis pemeriksaan.** Selain peran per rute, tiap controller memeriksa kepemilikan data lewat `Kelas::dapatDikelolaOleh` (admin semua kelas, dosen kelas yang ia ampu) dan `Kelas::dapatDiikutiOleh` (mahasiswa kelas yang ia ikuti). Id dari URL atau formulir tidak dipercaya: pengumpulan, percobaan kuis, dan mahasiswa yang dinilai selalu dicocokkan dengan kelas yang sah. Data milik kelas lain dijawab 404 supaya keberadaannya tidak terungkap.

**Hak akses minimum dan integritas peran.** Peran bawaan akun baru adalah `mahasiswa`. Kolom `role` tidak bisa diisi massal; hanya `forceFill` di seeder, `PembuatAkun`, dan `PenggunaController` (rute `role:admin`) yang menetapkannya, dan nilainya dibatasi `Rule::in`. Admin tidak bisa mengubah peran atau menghapus akunnya sendiri, dan dosen yang masih mengampu kelas tidak bisa dihapus atau diubah perannya.

**Berkas.** Unggahan memakai nama acak, disimpan privat, dan diunduh sebagai lampiran lewat pemeriksaan hak akses. Banner berita diperiksa sebagai gambar JPG, PNG, atau WebP. Tautan materi hanya menerima skema http dan https. Ekspor CSV mengawali sel berbahaya (diawali `=`, `+`, `-`, `@`) dengan tanda kutip agar tidak dieksekusi Excel.

**Lain-lain.** Login dibatasi 5 percobaan per menit, sesi dibuat ulang setelah login dan dibatalkan saat logout, semua formulir memakai token CSRF, keluaran Blade di-escape, kuis dan presensi memeriksa tenggat dan status sesi di server.

**Batas yang perlu diketahui.** README ini bukan audit keamanan. Belum ada: kebijakan kata sandi selain panjang minimal 8 karakter, verifikasi email, pengiriman email, log audit, paginasi daftar (semua daftar dimuat penuh), dan pengecekan isi berkas selain ekstensi dan ukuran (pengecekan antivirus tidak ada). Dua rute (`/` dan `/dashboard`) berupa closure, jadi `php artisan route:cache` gagal sampai keduanya dipindah ke controller.

## Struktur berkas utama

| Lokasi | Isi |
|--------|-----|
| `database/migrations/2026_11_12_000000_add_role_to_users_table.php` | Kolom `role` |
| `database/migrations/2026_11_14_00000{1,2,3}_*.php` | Seluruh tabel LMS |
| `database/seeders/DatabaseSeeder.php` | Akun uji dan data contoh |
| `app/Http/Middleware/CheckRole.php` | Pemeriksaan peran |
| `app/Services/` | `PerhitunganNilai`, `RekapPresensi`, `PembuatAkun` |
| `app/Http/Controllers/` | Satu controller per fitur, dengan `Concerns/` untuk pemeriksaan akses kelas dan ekspor CSV |
| `app/Models/` | Model data, scope hak akses ada di `Kelas` |
| `routes/web.php` | Semua rute dan pengelompokan middleware |
| `app/Support/Ikon.php` | Pembaca ikon Phosphor dari `node_modules` |
| `app/Services/PenerjemahHalaman.php`, `app/Http/Middleware/AturBahasa.php`, `TerjemahkanHalaman.php` | Pengaturan dan penerjemahan bahasa |
| `resources/views/` | `beranda`, `masuk`, `lupa-kata-sandi` (publik, layout `tata-letak-publik`); `tata-letak` (layout aplikasi), `partials/` (gaya dasar, navigasi, ikon, slider berita), dan halaman per peran |
| `lang/en.json`, `lang/en/validation.php` | Kamus bahasa Inggris |
| `lang/id/validation.php` | Pesan validasi bahasa Indonesia |

## Matriks pengujian akses dasar

| Pelaku | `/admin` | `/dosen` | `/mahasiswa` | `/kelola-materi` |
|--------|----------|----------|--------------|------------------|
| Admin | Diizinkan | Ditolak (403) | Ditolak (403) | Diizinkan |
| Dosen | Ditolak (403) | Diizinkan | Ditolak (403) | Diizinkan |
| Mahasiswa | Ditolak (403) | Ditolak (403) | Diizinkan | Ditolak (403) |
| Tamu (belum login) | Dialihkan ke Login | Dialihkan ke Login | Dialihkan ke Login | Dialihkan ke Login |

## Pengujian manual

Jalankan dan catat hasilnya. Langkah 1 sampai 4 menguji tampilan dan fitur baru; langkah 5 dan seterusnya menguji fitur LMS.

1. Halaman sebelum login: tanpa login, buka `/`, `/faq`, `/tutorial`, `/login`, dan `/lupa-kata-sandi`. Pastikan tidak ada penjelasan fitur, tidak ada tombol pendaftaran (`/register` harus 404), dan bilah navigasi atas tetap terlihat. Buka satu pertanyaan di FAQ dan pastikan halaman masih utuh. Ubah ukuran jendela (atau pakai mode perangkat di DevTools) ke 320x568, 360x640, 768x1024, 1366x768, dan landscape 800x360, lalu pastikan seluruh elemen utuh tanpa bilah gulir halaman, juga saat pesan galat login muncul. Di lebar di bawah 1024 piksel, menu harus pindah ke bilah bawah.
2. Tema dan bahasa: klik tombol tema berulang (Sistem, Terang, Gelap) dan pastikan latar putih polos atau hitam polos, serta mengikuti tema perangkat saat "Sistem". Klik tombol bahasa dan pastikan seluruh halaman (juga setelah login, termasuk pesan notifikasi dan pesan galat) berganti ke Inggris dan kembali. Arahkan kursor ke ikon menu dan pastikan tooltip nama menu muncul.
3. Login sebagai admin dan buka Berita. Tulis berita dengan gambar yang lebih besar dari 1280 x 720: alat potong harus terbuka otomatis, hasilnya 1280 x 720. Tulis berita tanpa gambar dan pastikan tidak ada bingkai gambar kosong di dasbor, detail, dan daftar. Tulis lebih dari 3 berita: dasbor menampilkan 3 kartu dengan tombol panah kiri dan kanan. Klik sebuah kartu: halaman detail menampilkan teks lengkap dan gambar tanpa dipotong.
4. Kedaluwarsa dan notifikasi: beri satu berita tanggal kedaluwarsa kemarin. Admin dan dosen masih melihatnya (bertanda Kedaluwarsa), mahasiswa tidak melihatnya di dasbor dan mendapat 404 bila membuka alamat detailnya. Pastikan setiap tambah, ubah, dan hapus memunculkan notifikasi yang hilang sendiri setelah 5 detik atau saat tombol tutup diklik.
5. Tanpa login, buka empat URL pada matriks akses dasar (`/admin`, `/dosen`, `/mahasiswa`, `/kelola-materi`). Semuanya harus berakhir di `/login`. Login sebagai tiap akun uji dan cocokkan hasil dengan matriks, dan pastikan berita sambutan tampil di dasbor ketiganya.
6. Lupa kata sandi: kirim email `mahasiswa@student.telkomuniversity.ac.id`, login sebagai admin, buka Pengguna, atur kata sandi baru dari permintaan itu, lalu login sebagai mahasiswa dengan kata sandi baru. Kirim juga email yang tidak terdaftar dan pastikan pesannya sama.
7. Admin, akun: buka Tambah akun, tempel tiga baris nama dari Excel dengan peran dosen, dan pastikan email berformat `username@dosen.telkomuniversity.ac.id` serta kata sandi muncul satu kali. Ubah peran salah satunya menjadi mahasiswa lewat halaman Pengguna dan pastikan domain email berubah. Coba ubah peran dan hapus akun sendiri (harus ditolak).
8. Admin, data master dan kelas: tambah tahun ajaran baru dan tandai aktif (yang lama nonaktif), tambah program studi dan mata kuliah, buat kelas dengan dosen, daftarkan dan keluarkan mahasiswa. Coba hapus mata kuliah yang dipakai kelas (harus ditolak dengan pesan).
9. Dosen: isi topik dan unggah PDF serta tautan pada sebuah pertemuan, buat tugas, buat kuis dengan satu soal pilihan ganda dan satu esai, buka sesi presensi, atur bobot (total selain 100 harus ditolak), isi UTS dan UAS, dan ekspor CSV presensi dan nilai.
10. Mahasiswa: unduh materi, kumpulkan tugas sebelum dan sesudah tenggat (cek status Terkumpul dan Terlambat), kerjakan kuis (kedua kali harus ditolak), check-in saat sesi dibuka dan coba saat ditutup (harus ditolak), lalu cek persentase presensi dan rincian nilai dengan umpan balik.
11. Akses antarkelas: login sebagai `dosen2`, buka `/kelola/kelas/1`, `/kelola/tugas/1`, dan `/unduh/pengumpulan/1`. Semuanya harus 404. Login sebagai `mahasiswa2` (tanpa kelas), buka `/kelas-saya/1` dan `/unduh/materi/1`. Harus 404.
12. Klik Keluar lalu buka `/admin`. Harus kembali ke `/login`.
