<?php $__env->startSection('judul', 'Manajemen Pengguna'); ?>

<?php $__env->startSection('isi'); ?>
    <div class="kepala">
        <h1>Manajemen Pengguna</h1>
        <a class="tombol" href="<?php echo e(route('pengguna.buat')); ?>">Tambah akun</a>
    </div>

    <?php if(session('hasilAkun')): ?>
        <section aria-labelledby="judul-hasil">
            <h2 id="judul-hasil">Hasil pembuatan akun</h2>
            <p class="info bahaya" role="status">Catat kata sandi di bawah sekarang. Kata sandi tidak dapat ditampilkan lagi setelah halaman ini ditutup.</p>
            <div class="bungkus-tabel">
                <table>
                    <caption>Akun yang dibuat</caption>
                    <thead>
                        <tr><th scope="col">Nama</th><th scope="col">Email</th><th scope="col">Kata sandi</th><th scope="col">Status</th></tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = session('hasilAkun'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hasil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($hasil['nama']); ?></td>
                                <td><?php echo e($hasil['email']); ?></td>
                                <td><?php echo e($hasil['password']); ?></td>
                                <td>
                                    <span class="lencana <?php echo e($hasil['status'] === 'Dibuat' ? 'ok' : 'galat'); ?>"><?php echo e($hasil['status']); ?></span>
                                    <?php echo e($hasil['alasan']); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>

    <?php if($permintaanReset->isNotEmpty()): ?>
        <section aria-labelledby="judul-reset">
            <h2 id="judul-reset">Permintaan reset kata sandi</h2>
            <div class="bungkus-tabel">
                <table>
                    <caption>Permintaan yang menunggu</caption>
                    <thead>
                        <tr><th scope="col">Pengguna</th><th scope="col">Email</th><th scope="col">Diminta</th><th scope="col">Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $permintaanReset; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permintaan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($permintaan->user->name); ?></td>
                                <td><?php echo e($permintaan->user->email); ?></td>
                                <td><?php echo e($permintaan->created_at->format('d/m/Y H:i')); ?></td>
                                <td>
                                    <div class="aksi">
                                        <a class="tombol kecil" href="<?php echo e(route('pengguna.ubah', $permintaan->user)); ?>">Atur kata sandi</a>
                                        <form method="POST" action="<?php echo e(route('reset.abaikan', $permintaan)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="sekunder kecil">Abaikan</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>

    <section aria-labelledby="judul-akun">
        <h2 id="judul-akun">Semua akun</h2>

        <form class="aksi" method="GET" action="<?php echo e(route('pengguna.kelola')); ?>" role="search" style="margin-bottom:1rem">
            <label class="sr-saja" for="q" style="position:absolute;left:-9999px">Cari nama atau email</label>
            <input id="q" name="q" type="search" value="<?php echo e($kata); ?>" placeholder="Cari nama atau email" style="max-width:18rem">
            <label class="sr-saja" for="filter-peran" style="position:absolute;left:-9999px">Filter peran</label>
            <select id="filter-peran" name="peran" style="max-width:11rem">
                <option value="">Semua peran</option>
                <?php $__currentLoopData = \App\Models\User::DAFTAR_PERAN; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $peran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($peran); ?>" <?php if($peranDipilih === $peran): echo 'selected'; endif; ?>><?php echo e($peran); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit">Cari</button>
        </form>

        <div class="bungkus-tabel">
            <table>
                <caption>Daftar akun</caption>
                <thead>
                    <tr><th scope="col">Nama</th><th scope="col">Email</th><th scope="col">Peran</th><th scope="col">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $daftarPengguna; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengguna): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($pengguna->name); ?></td>
                            <td><?php echo e($pengguna->email); ?></td>
                            <td>
                                <?php if($pengguna->is(auth()->user())): ?>
                                    <span class="lencana"><?php echo e($pengguna->role); ?> (Anda)</span>
                                <?php else: ?>
                                    <form class="aksi" method="POST" action="<?php echo e(route('pengguna.peran', $pengguna)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <select name="role" aria-label="Peran untuk <?php echo e($pengguna->name); ?>" style="min-width:8.5rem">
                                            <?php $__currentLoopData = \App\Models\User::DAFTAR_PERAN; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $peran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($peran); ?>" <?php if($pengguna->role === $peran): echo 'selected'; endif; ?>><?php echo e($peran); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button type="submit" class="sekunder kecil">Terapkan</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="aksi">
                                    <a class="tombol sekunder kecil" href="<?php echo e(route('pengguna.ubah', $pengguna)); ?>">Ubah / reset kata sandi</a>
                                    <?php if (! ($pengguna->is(auth()->user()))): ?>
                                        <form method="POST" action="<?php echo e(route('pengguna.hapus', $pengguna)); ?>" data-konfirmasi="Hapus akun <?php echo e($pengguna->name); ?>? Data terkait akun ini ikut terhapus.">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="bahaya kecil">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="kosong">Tidak ada akun yang cocok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tata-letak', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/adzraaaliyyahalthaaf/Downloads/akademik-app_Adzraa_102042500138/resources/views/pengguna/indeks.blade.php ENDPATH**/ ?>