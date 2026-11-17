<?php $__env->startSection('judul', 'Berita Kampus'); ?>

<?php $__env->startSection('isi'); ?>
    <div class="kepala">
        <h1>Berita dan Pengumuman</h1>
        <a class="tombol" href="<?php echo e(route('berita.buat')); ?>">Tulis berita</a>
    </div>

    <?php if($daftarBerita->isEmpty()): ?>
        <p class="kartu kosong">Belum ada berita.</p>
    <?php else: ?>
        <div class="bungkus-tabel">
            <table>
                <caption>Daftar berita</caption>
                <thead>
                    <tr>
                        <th scope="col">Judul</th>
                        <th scope="col">Tanggal terbit</th>
                        <th scope="col">Kedaluwarsa</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $daftarBerita; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="aksi">
                                    <?php if($berita->adaBanner()): ?>
                                        <img src="<?php echo e($berita->urlBanner()); ?>" alt="" width="64" height="36" style="object-fit:cover;border-radius:.5rem">
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('berita.tampil', $berita->id)); ?>"><?php echo e($berita->judul); ?></a>
                                </div>
                            </td>
                            <td><?php echo e($berita->tanggal_terbit->format('d/m/Y')); ?></td>
                            <td><?php echo e($berita->kedaluwarsa_pada?->format('d/m/Y') ?? '-'); ?></td>
                            <td>
                                <?php if($berita->kedaluwarsa()): ?>
                                    <span class="lencana galat">Kedaluwarsa</span>
                                <?php elseif($berita->terjadwal()): ?>
                                    <span class="lencana">Terjadwal</span>
                                <?php else: ?>
                                    <span class="lencana">Tayang</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="aksi">
                                    <a class="tombol sekunder kecil" href="<?php echo e(route('berita.ubah', $berita)); ?>">Ubah</a>
                                    <form method="POST" action="<?php echo e(route('berita.hapus', $berita)); ?>" data-konfirmasi="Hapus berita <?php echo e($berita->judul); ?>?">
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

<?php echo $__env->make('tata-letak', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/rapaabdullah/Pictures/akademik-app/resources/views/berita/indeks.blade.php ENDPATH**/ ?>