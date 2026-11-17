<?php ($bahasa = app()->getLocale()); ?>
<?php ($bahasaLain = $bahasa === 'id' ? 'en' : 'id'); ?>
<?php ($namaBahasaLain = $bahasa === 'id' ? 'English' : 'Bahasa Indonesia'); ?>

<header class="navbar-atas">
    <a class="merek" href="<?php echo e($publik ? url('/') : route('dashboard')); ?>">
        <img class="merek-tanda" src="<?php echo e(asset('images/logo-telkom-lambang.png')); ?>" width="137" height="189" alt="">
        <span class="merek-teks"><?php echo e(config('app.name')); ?></span>
    </a>

    <nav class="menu-utama" aria-label="Menu utama">
        <ul>
            <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $url, $pola]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e($url); ?>" <?php if(request()->is(...$pola)): ?> aria-current="page" <?php endif; ?>><?php echo e($label); ?></a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </nav>

    <div class="alat">
        <a class="tombol-ikon" href="<?php echo e(route('bahasa.ganti', $bahasaLain)); ?>" hreflang="<?php echo e($bahasaLain); ?>"
           aria-label="Ganti bahasa: <?php echo e($namaBahasaLain); ?>"><?php echo e(strtoupper($bahasa)); ?></a>

        <button type="button" class="tombol-ikon" id="ganti-tema" aria-label="Tema: Sistem">
            <span class="tema-sistem"><?php echo $__env->make('partials.ikon', ['nama' => 'desktop'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
            <span class="tema-terang"><?php echo $__env->make('partials.ikon', ['nama' => 'sun'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
            <span class="tema-gelap"><?php echo $__env->make('partials.ikon', ['nama' => 'moon'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
        </button>

        <?php if (! ($publik)): ?>
            <span class="tombol-ikon avatar-tombol" role="img" aria-label="<?php echo e(auth()->user()->name); ?>"><?php echo e(mb_strtoupper(mb_substr(auth()->user()->name, 0, 1))); ?></span>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="tombol-keluar">Keluar</button>
            </form>
        <?php endif; ?>
    </div>
</header>


<span hidden id="teks-tema-sistem">Tema: Sistem</span>
<span hidden id="teks-tema-light">Tema: Terang</span>
<span hidden id="teks-tema-dark">Tema: Gelap</span>
<?php /**PATH /Users/rapaabdullah/Pictures/akademik-app/resources/views/partials/navigasi.blade.php ENDPATH**/ ?>