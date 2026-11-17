<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" class="publik">
<head>
    <meta charset="utf-8">
    <?php echo $__env->make('partials.tema-awal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo $__env->yieldContent('judul'); ?> | <?php echo e(config('app.name')); ?></title>
    <style>
        <?php echo $__env->make('partials.gaya-dasar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        /* Seluruh ukuran memakai rem, dan rem mengikuti tinggi serta lebar jendela,
           sehingga satu halaman selalu muat dalam satu layar tanpa digulir. */
        html.publik { height: 100%; font-size: clamp(9px, min(2.4vh, 4vw), 16px); }
        @supports (height: 1dvh) {
            html.publik { font-size: clamp(9px, min(2.4dvh, 4vw), 16px); }
        }
        body.publik { height: 100vh; height: 100dvh; margin: 0; overflow: hidden; }
        .bingkai { display: grid; grid-template-rows: minmax(0, 1fr) auto; height: 100%; padding-top: var(--tinggi-bar); }
        main { display: grid; place-items: center; min-height: 0; padding: 0.75rem 1rem 0; }
        .kaki-situs { padding: 0.5rem 1rem 0.75rem; color: var(--teks-redup); font-size: 0.875rem; text-align: center; }

        .beranda { display: grid; justify-items: center; gap: 1rem; width: min(44rem, 100%); }
        .hero { position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; gap: 1rem; width: 100%; min-height: 13rem; padding: 1.75rem; border-radius: 1.5rem; background: var(--tombol); color: #ffffff; box-shadow: var(--kilau), var(--bayangan); }
        .hero::before { content: ""; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0) 55%); pointer-events: none; }
        .hero-teks { position: relative; display: grid; gap: 0.5rem; min-width: 0; }
        .hero-teks small { font-size: 1rem; }
        .hero-teks h1 { margin: 0; font-size: 2.25rem; line-height: 1.1; letter-spacing: -0.02em; text-wrap: balance; }
        .hero-logo { position: relative; flex: none; display: block; width: 15rem; height: auto; aspect-ratio: 512 / 184; padding: 0.5rem; border-radius: 1rem; background: #ffffff; box-shadow: 0 0.5rem 1.25rem -0.5rem rgba(0, 0, 0, 0.35); }
        .beranda a.tombol.garis:hover { background-color: var(--tombol); color: #ffffff; border-color: var(--tombol); }
        .beranda .hero { background: #891F1C; }
        .beranda a.tombol.garis { color: var(--teks); border-color: var(--teks); }
        .beranda a.tombol.garis .ikon { color: var(--merah); }
        .beranda a.tombol.garis:hover { background-color: #891F1C; color: #ffffff; border-color: #891F1C; }
        .beranda a.tombol.garis:hover .ikon { color: #ffffff; }
        .beranda a.tombol.garis:hover .ikon { color: #ffffff; }
        .beranda a.tombol.garis { color: var(--teks); }
        .beranda a.tombol.garis .ikon { color: var(--merah); }
        .beranda-catatan { max-width: 30rem; margin: 0; color: var(--teks-redup); font-size: 0.9375rem; line-height: 1.4; text-align: center; }

        .masuk-kotak { width: min(24rem, 100%); max-height: 100%; overflow: auto; padding: 1.5rem; }
        .masuk-kotak h1 { margin: 0 0 0.25rem; font-size: 1.625rem; }
        .masuk-kotak .redup { margin: 0 0 0.875rem; }
        .masuk-kotak .kolom { margin-bottom: 0.75rem; }
        .masuk-kotak button[type="submit"] { width: 100%; }
        .masuk-kotak .info { margin: 0 0 0.75rem; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.3; }
        .tautan-bawah { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 0.25rem 1rem; margin: 0.75rem 0 0; font-size: 0.9375rem; }

        .kotak-lebar { width: min(36rem, 100%); }
        .faq { margin: 0 0 0.75rem; }
        .faq details { border-bottom: 1px solid var(--garis-halus); }
        .faq summary { display: flex; align-items: center; justify-content: space-between; gap: 1rem; min-height: 2.75rem; padding: 0.5rem 0; font-weight: 600; list-style: none; cursor: pointer; }
        .faq summary::-webkit-details-marker { display: none; }
        .faq summary::after { content: ""; flex: none; width: 0.5rem; height: 0.5rem; margin-right: 0.25rem; border-right: 0.125rem solid currentColor; border-bottom: 0.125rem solid currentColor; transform: rotate(45deg); transition: transform 0.2s ease; }
        .faq details[open] summary::after { transform: rotate(-135deg); }
        .faq p { margin: 0 0 0.75rem; color: var(--teks-redup); font-size: 0.9375rem; line-height: 1.4; }
        .langkah { display: grid; gap: 0.625rem; margin: 0 0 0.875rem; padding: 0; list-style: none; counter-reset: langkah; }
        .langkah li { display: grid; grid-template-columns: 1.75rem 1fr; align-items: start; gap: 0.75rem; counter-increment: langkah; font-size: 0.9375rem; line-height: 1.4; }
        .langkah li::before { content: counter(langkah); display: grid; place-items: center; width: 1.75rem; height: 1.75rem; border-radius: 50%; background: var(--tombol); color: var(--tombol-teks); font-size: 0.875rem; font-weight: 700; }
        .catatan-chat { display: flex; align-items: center; gap: 0.5rem; margin: 0; padding: 0.625rem 0.875rem; border: 1px solid var(--kaca-garis); border-radius: 0.875rem; background: var(--kaca-kuat); font-size: 0.9375rem; line-height: 1.4; }
        .catatan-chat .ikon { color: var(--merah); }

        @media (max-width: 1023px) {
            .bingkai { padding-bottom: calc(var(--tinggi-bawah) + env(safe-area-inset-bottom)); }
        }
        @media (max-width: 480px) {
            .merek-teks { display: none; }
        }
        <?php echo $__env->yieldPushContent('gaya'); ?>
    </style>
</head>
<body class="publik <?php echo $__env->yieldContent('kelas-body'); ?>">
    <?php
        $menu = [
            ['Beranda', url('/'), ['/'], 'house'],
            ['FAQ', route('faq'), ['faq'], 'question'],
            ['Tutorial', route('tutorial'), ['tutorial'], 'book-open-text'],
            ['Masuk', route('login'), ['login'], 'sign-in'],
        ];
    ?>

    <?php echo $__env->make('partials.navigasi', ['menu' => $menu, 'publik' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bingkai">
        <main>
            <?php echo $__env->yieldContent('isi'); ?>
        </main>

        <footer class="kaki-situs">&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?></footer>
    </div>

    <?php echo $__env->make('partials.skrip-dasar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('skrip'); ?>
</body>
</html>
<?php /**PATH /Users/adzraaaliyyahalthaaf/Downloads/akademik-app_Adzraa_102042500138/resources/views/tata-letak-publik.blade.php ENDPATH**/ ?>