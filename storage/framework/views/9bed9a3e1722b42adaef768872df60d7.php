<?php $__env->startSection('judul', 'Kelas'); ?>

<?php $__env->startSection('isi'); ?>
    <div class="kepala">
        <h1>Kelas</h1>
        <a class="tombol" href="<?php echo e(route('kelas.buat')); ?>">Buat kelas</a>
    </div>

    <?php if($daftarKelas->isEmpty()): ?>
        <p class="kartu kosong">Belum ada kelas. Pastikan tahun ajaran, mata kuliah, dan akun dosen sudah dibuat.</p>
    <?php else: ?>
        <div class="bungkus-tabel">
            <table>
                <caption>Daftar kelas</caption>
                <thead>
                    <tr>
                        <th scope="col">Mata kuliah</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Tahun ajaran</th>
                        <th scope="col">Dosen</th>
                        <th scope="col">Mahasiswa</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $daftarKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($kelas->mataKuliah->kode); ?> <?php echo e($kelas->mataKuliah->nama); ?></td>
                            <td><?php echo e($kelas->nama); ?></td>
                            <td><?php echo e($kelas->tahunAjaran->nama); ?></td>
                            <td><?php echo e($kelas->dosen->name); ?></td>
                            <td><?php echo e($kelas->mahasiswa_count); ?></td>
                            <td>
                                <div class="aksi">
                                    <a class="tombol kecil" href="<?php echo e(route('kelola.tampil', $kelas)); ?>">Buka</a>
                                    <a class="tombol sekunder kecil" href="<?php echo e(route('kelas.mahasiswa', $kelas)); ?>">Mahasiswa</a>
                                    <a class="tombol sekunder kecil" href="<?php echo e(route('kelas.ubah', $kelas)); ?>">Ubah</a>
                                    <form method="POST" action="<?php echo e(route('kelas.hapus', $kelas)); ?>" data-konfirmasi="Hapus kelas <?php echo e($kelas->label()); ?>? Materi, tugas, kuis, presensi, dan nilai kelas ini ikut terhapus.">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="bahaya kecil">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tata-letak', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/rapaabdullah/Pictures/akademik-app/resources/views/kelas/indeks.blade.php ENDPATH**/ ?>