<?php $__env->startSection('judul', $definisi['judul']); ?>

<?php $__env->startSection('isi'); ?>
    <div class="kepala">
        <h1><?php echo e($definisi['judul']); ?></h1>
        <a class="tombol" href="<?php echo e(route('master.buat', $jenis)); ?>">Tambah <?php echo e(strtolower($definisi['judul'])); ?></a>
    </div>

    <?php if($daftar->isEmpty()): ?>
        <p class="kartu kosong">Belum ada data.</p>
    <?php else: ?>
        <div class="bungkus-tabel">
            <table>
                <caption>Daftar <?php echo e(strtolower($definisi['judul'])); ?></caption>
                <thead>
                    <tr>
                        <?php $__currentLoopData = $definisi['kolom']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kolom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th scope="col"><?php echo e($kolom['label']); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <?php $__currentLoopData = $definisi['kolom']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kolom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td>
                                    <?php if($kolom['tipe'] === 'centang'): ?>
                                        <?php if($item->{$kolom['nama']}): ?> <span class="lencana ok">Aktif</span> <?php else: ?> <span class="lencana">Tidak</span> <?php endif; ?>
                                    <?php elseif($kolom['tipe'] === 'pilih'): ?>
                                        <?php echo e($item->{$kolom['relasi']}?->nama); ?>

                                    <?php else: ?>
                                        <?php echo e($item->{$kolom['nama']}); ?>

                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td>
                                <div class="aksi">
                                    <a class="tombol sekunder kecil" href="<?php echo e(route('master.ubah', [$jenis, $item->id])); ?>">Ubah</a>
                                    <form method="POST" action="<?php echo e(route('master.hapus', [$jenis, $item->id])); ?>" data-konfirmasi="Hapus data ini?">
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

<?php echo $__env->make('tata-letak', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/adzraaaliyyahalthaaf/Downloads/akademik-app_Adzraa_102042500138/resources/views/master/indeks.blade.php ENDPATH**/ ?>