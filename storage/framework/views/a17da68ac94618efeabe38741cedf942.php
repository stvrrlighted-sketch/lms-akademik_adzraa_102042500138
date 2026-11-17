<?php $__env->startSection('judul', 'Dasbor Admin'); ?>

<?php $__env->startSection('isi'); ?>
    <div class="kepala"><h1>Dasbor Admin</h1></div>

    <?php echo $__env->make('partials.berita-dasbor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section aria-labelledby="judul-ringkasan">
        <h2 id="judul-ringkasan">Ringkasan sistem</h2>
        <dl class="ringkasan">
            <div class="kartu"><dt>Admin</dt><dd><?php echo e($jumlahPerPeran['admin'] ?? 0); ?></dd></div>
            <div class="kartu"><dt>Dosen</dt><dd><?php echo e($jumlahPerPeran['dosen'] ?? 0); ?></dd></div>
            <div class="kartu"><dt>Mahasiswa</dt><dd><?php echo e($jumlahPerPeran['mahasiswa'] ?? 0); ?></dd></div>
            <div class="kartu"><dt>Mata kuliah</dt><dd><?php echo e($jumlahMataKuliah); ?></dd></div>
            <div class="kartu"><dt>Kelas</dt><dd><?php echo e($jumlahKelas); ?></dd></div>
            <div class="kartu"><dt>Permintaan reset</dt><dd><?php echo e($jumlahPermintaan); ?></dd></div>
        </dl>
    </section>

    <div class="aksi">
        <a class="tombol" href="<?php echo e(route('berita.buat')); ?>">Tulis berita</a>
        <a class="tombol sekunder" href="<?php echo e(route('pengguna.buat')); ?>">Tambah akun</a>
        <a class="tombol sekunder" href="<?php echo e(route('kelas.indeks')); ?>">Kelola kelas</a>
        <a class="tombol sekunder" href="<?php echo e(route('pengguna.kelola')); ?>">Permintaan reset kata sandi</a>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tata-letak', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/rapaabdullah/Pictures/akademik-app/resources/views/dasbor/admin.blade.php ENDPATH**/ ?>