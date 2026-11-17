<?php $__env->startSection('judul', 'Beranda'); ?>

<?php $__env->startSection('isi'); ?>
    <div class="beranda">
        <section class="hero" aria-labelledby="judul-hero">
            <div class="hero-teks">
                <small>Learning Management System</small>
                <h1 id="judul-hero"><?php echo e(config('app.name')); ?></h1>
            </div>

            <img class="hero-logo" src="<?php echo e(asset('images/logo-telkom.png')); ?>" width="512" height="184" alt="" aria-hidden="true">
        </section>

        <a class="tombol garis" href="<?php echo e(route('login')); ?>">
            <?php echo $__env->make('partials.ikon', ['nama' => 'lock-simple'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            Masuk ke LMS
        </a>

        <p class="beranda-catatan">Gunakan akun akademik Anda. Mengalami kendala pada akun? Hubungi admin.</p>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tata-letak-publik', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/adzraaaliyyahalthaaf/Downloads/akademik-app_Adzraa_102042500138/resources/views/beranda.blade.php ENDPATH**/ ?>