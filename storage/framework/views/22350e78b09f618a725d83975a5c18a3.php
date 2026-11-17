<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="utf-8">
    <?php echo $__env->make('partials.tema-awal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo $__env->yieldContent('judul'); ?> | <?php echo e(config('app.name')); ?></title>
    <style>
        <?php echo $__env->make('partials.gaya-dasar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        body { padding-top: calc(var(--tinggi-bar) + 1.25rem); padding-bottom: 2.5rem; }
        .konten { display: grid; align-content: start; gap: 1.5rem; width: min(72rem, calc(100% - 2rem)); margin: 0 auto; }
        .kepala { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem 1rem; }
        .kepala p { margin: 0.25rem 0 0; color: var(--teks-redup); }
        .sub-menu { display: flex; flex-wrap: wrap; gap: 0.5rem; margin: 0; padding: 0; list-style: none; }
        .sub-menu a { display: inline-flex; align-items: center; min-height: 2.5rem; padding: 0.25rem 1rem; border: 1px solid var(--kaca-garis); border-radius: 999px; background: var(--kaca-kuat); color: var(--teks); text-decoration: none; white-space: nowrap; transition: background-color 0.2s ease; }
        .sub-menu a:hover { background: var(--hover); }
        .sub-menu a[aria-current="page"] { background: var(--tombol); border-color: var(--tombol); color: var(--tombol-teks); font-weight: 600; }
        .grid-kartu { display: grid; grid-template-columns: repeat(auto-fit, minmax(15rem, 1fr)); gap: 1rem; }
        .ringkasan { display: grid; grid-template-columns: repeat(auto-fit, minmax(9rem, 1fr)); gap: 1rem; margin: 0; }
        .ringkasan div { padding: 1rem 1.125rem; }
        .ringkasan dt { color: var(--teks-redup); font-size: 0.875rem; }
        .ringkasan dd { margin: 0; font-size: 1.75rem; font-weight: 700; letter-spacing: -0.02em; }

        /* berita: penggeser horizontal, maksimal 3 kartu terlihat */
        .kepala-bagian { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 0.75rem; }
        .kepala-bagian h2 { margin: 0; }
        .kontrol-slider { display: flex; gap: 0.5rem; }
        .kontrol-slider[hidden] { display: none; }
        button.panah { min-width: 2.5rem; width: 2.5rem; height: 2.5rem; padding: 0; border-radius: 50%; }
        .slider { display: grid; grid-auto-flow: column; grid-auto-columns: calc((100% - 2rem) / 3); gap: 1rem; margin: 0; padding: 0.375rem 0.125rem 1rem; overflow-x: auto; overscroll-behavior-x: contain; scroll-snap-type: x mandatory; scrollbar-width: none; }
        .slider::-webkit-scrollbar { display: none; }
        @media (prefers-reduced-motion: no-preference) { .slider { scroll-behavior: smooth; } }
        a.berita-kartu { display: flex; flex-direction: column; padding: 0; overflow: hidden; color: var(--teks); text-decoration: none; scroll-snap-align: start; }
        .berita-kartu img { display: block; width: 100%; aspect-ratio: 16 / 9; object-fit: cover; }
        .berita-isi { display: grid; flex: 1; align-content: start; gap: 0.375rem; padding: 1rem 1.125rem 1.125rem; }
        .berita-isi h3 { display: -webkit-box; margin: 0; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .berita-isi .ringkas { display: -webkit-box; margin: 0; overflow: hidden; color: var(--teks-redup); -webkit-box-orient: vertical; -webkit-line-clamp: 3; }
        .berita-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 0.375rem; color: var(--teks-redup); font-size: 0.8125rem; }
        .artikel { display: grid; gap: 1rem; padding: clamp(1rem, 3vw, 2rem); }
        .artikel h1 { font-size: clamp(1.5rem, 4vw, 2.25rem); }
        .artikel .isi-teks { max-width: 46rem; font-size: 1.0625rem; line-height: 1.7; }
        .gambar-asli { display: grid; justify-items: start; gap: 0.5rem; margin: 0; }
        .gambar-asli img { display: block; max-width: 100%; height: auto; border-radius: 0.75rem; }

        @media (max-width: 1023px) {
            body { padding-top: calc(var(--tinggi-bar) + 1rem); padding-bottom: calc(var(--tinggi-bawah) + env(safe-area-inset-bottom) + 1rem); }
            .slider { grid-auto-columns: calc((100% - 1rem) / 2); }
            .sub-menu { flex-wrap: nowrap; overflow-x: auto; scrollbar-width: none; padding-bottom: 0.25rem; }
            .formulir { max-width: none; }
        }
        @media (max-width: 640px) {
            .slider { grid-auto-columns: 100%; }
            .merek-teks { display: none; }
            th, td { padding: 0.625rem 0.75rem; }
        }
    </style>
</head>
<body>
    <?php
        $menu = match (auth()->user()->role) {
            'admin' => [
                ['Dasbor', route('admin.dasbor'), ['admin'], 'squares-four'],
                ['Berita', route('berita.indeks'), ['admin/berita*'], 'newspaper'],
                ['Pengguna', route('pengguna.kelola'), ['admin/pengguna*'], 'users'],
                ['Tahun ajaran', route('master.indeks', 'tahun-ajaran'), ['admin/master/tahun-ajaran*'], 'calendar-blank'],
                ['Program studi', route('master.indeks', 'program-studi'), ['admin/master/program-studi*'], 'graduation-cap'],
                ['Mata kuliah', route('master.indeks', 'mata-kuliah'), ['admin/master/mata-kuliah*'], 'books'],
                ['Daftar kelas', route('kelas.indeks'), ['admin/kelas*', 'kelola/*'], 'chalkboard-teacher'],
            ],
            'dosen' => [
                ['Dasbor', route('dosen.dasbor'), ['dosen'], 'squares-four'],
                ['Kelas saya', route('kelola.indeks'), ['kelola/*'], 'chalkboard-teacher'],
            ],
            default => [
                ['Dasbor', route('mahasiswa.dasbor'), ['mahasiswa'], 'squares-four'],
                ['Kelas saya', route('kelassaya.indeks'), ['kelas-saya*'], 'book-open'],
            ],
        };
    ?>

    <a class="lompat" href="#isi">Lewati ke konten</a>

    <?php echo $__env->make('partials.navigasi', ['menu' => $menu, 'publik' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main id="isi" class="konten">
        <?php echo $__env->yieldContent('isi'); ?>
    </main>

    <div class="area-toast" id="area-toast" role="region" aria-label="Notifikasi">
        <?php if(session('sukses')): ?>
            <div class="toast" role="status">
                <?php echo $__env->make('partials.ikon', ['nama' => 'check-circle'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <p><?php echo e(session('sukses')); ?></p>
                <button type="button" class="toast-tutup" aria-label="Tutup notifikasi"><?php echo $__env->make('partials.ikon', ['nama' => 'x'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></button>
            </div>
        <?php endif; ?>
        <?php if(session('galat')): ?>
            <div class="toast galat" role="alert">
                <?php echo $__env->make('partials.ikon', ['nama' => 'warning-circle'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <p><?php echo e(session('galat')); ?></p>
                <button type="button" class="toast-tutup" aria-label="Tutup notifikasi"><?php echo $__env->make('partials.ikon', ['nama' => 'x'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></button>
            </div>
        <?php endif; ?>
    </div>

    <dialog class="dialog" id="dialog-konfirmasi" aria-labelledby="judul-dialog">
        <form method="dialog">
            <h2 id="judul-dialog">Konfirmasi</h2>
            <p id="teks-dialog"></p>
            <div class="aksi">
                <button value="batal" class="sekunder">Batal</button>
                <button value="ya" class="bahaya">Ya, lanjutkan</button>
            </div>
        </form>
    </dialog>

    <?php echo $__env->make('partials.skrip-dasar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('skrip'); ?>
</body>
</html>
<?php /**PATH /Users/adzraaaliyyahalthaaf/Downloads/akademik-app_Adzraa_102042500138/resources/views/tata-letak.blade.php ENDPATH**/ ?>