<?php $__env->startSection('judul', 'Lupa kata sandi'); ?>

<?php $__env->startSection('isi'); ?>
    <div class="kartu masuk-kotak">
        <h1>Lupa kata sandi</h1>
        <p class="redup">Masukkan email akun Anda. Permintaan diteruskan ke admin.</p>

        <?php if(session('sukses')): ?>
            <p class="info" role="status"><?php echo e(session('sukses')); ?></p>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('lupa.kirim')); ?>" novalidate>
            <?php echo csrf_field(); ?>

            <div class="kolom">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>"
                       autocomplete="username" autofocus required
                       <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> aria-invalid="true" aria-describedby="galat-email" <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>>
                <?php echo $__env->make('partials.galat', ['nama' => 'email'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <button type="submit">Kirim permintaan</button>
        </form>

        <p class="tautan-bawah">
            <a href="<?php echo e(route('login')); ?>">Kembali ke halaman masuk</a>
        </p>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tata-letak-publik', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/rapaabdullah/Pictures/akademik-app/resources/views/lupa-kata-sandi.blade.php ENDPATH**/ ?>