<?php $__env->startSection('judul', 'Tutorial'); ?>

<?php $__env->startSection('isi'); ?>
    <div class="kartu masuk-kotak kotak-lebar">
        <h1>Tutorial</h1>

        <ol class="langkah">
            <li><span><strong>Dapatkan akun.</strong> Minta admin membuatkan akun untuk Anda.</span></li>
            <li><span><strong>Masuk.</strong> Buka halaman Masuk, lalu isi email dan kata sandi Anda.</span></li>
            <li><span><strong>Lupa kata sandi?</strong> Pilih tautan Lupa kata sandi dan kirim permintaan ke admin.</span></li>
            <li><span><strong>Atur tampilan.</strong> Pilih bahasa dan tema terang atau gelap lewat ikon di bilah navigasi.</span></li>
            <li><span><strong>Mulai belajar.</strong> Setelah masuk, pakai menu di bilah navigasi untuk membuka dasbor dan kelas Anda.</span></li>
        </ol>

        <p class="catatan-chat">
            <?php echo $__env->make('partials.ikon', ['nama' => 'chat-circle-dots'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <span>Jika ada pertanyaan, silakan chat admin.</span>
        </p>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tata-letak-publik', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/rapaabdullah/Pictures/akademik-app/resources/views/tutorial.blade.php ENDPATH**/ ?>