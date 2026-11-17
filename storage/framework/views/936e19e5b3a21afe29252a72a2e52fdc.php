<section aria-labelledby="judul-berita">
    <div class="kepala-bagian">
        <h2 id="judul-berita">Berita kampus</h2>

        <?php if($daftarBerita->isNotEmpty()): ?>
            <div class="kontrol-slider" data-kontrol hidden>
                <button type="button" class="sekunder panah" data-arah="-1" aria-label="Berita sebelumnya"><?php echo $__env->make('partials.ikon', ['nama' => 'caret-left'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></button>
                <button type="button" class="sekunder panah" data-arah="1" aria-label="Berita berikutnya"><?php echo $__env->make('partials.ikon', ['nama' => 'caret-right'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></button>
            </div>
        <?php endif; ?>
    </div>

    <?php if($daftarBerita->isEmpty()): ?>
        <p class="kartu kosong">Belum ada berita.</p>
    <?php else: ?>
        <div class="slider" data-slider tabindex="0" role="region" aria-label="Daftar berita">
            <?php $__currentLoopData = $daftarBerita; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="kartu interaktif berita-kartu" href="<?php echo e(route('berita.tampil', $berita->id)); ?>">
                    <?php if($berita->adaBanner()): ?>
                        <img src="<?php echo e($berita->urlBanner()); ?>" alt="" loading="lazy" width="640" height="360">
                    <?php endif; ?>
                    <div class="berita-isi">
                        <div class="berita-meta">
                            <time datetime="<?php echo e($berita->tanggal_terbit->format('Y-m-d')); ?>"><?php echo e($berita->tanggal_terbit->format('d/m/Y')); ?></time>
                            <?php if($berita->kedaluwarsa()): ?>
                                <span class="lencana galat">Kedaluwarsa</span>
                            <?php endif; ?>
                            <?php if($berita->terjadwal()): ?>
                                <span class="lencana">Terjadwal</span>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo e($berita->judul); ?></h3>
                        <p class="ringkas"><?php echo e(\Illuminate\Support\Str::limit($berita->isi, 200)); ?></p>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</section>

<?php $__env->startPush('skrip'); ?>
    <script>
        (function () {
            var slider = document.querySelector('[data-slider]');
            var kontrol = document.querySelector('[data-kontrol]');
            if (!slider || !kontrol) { return; }

            var sebelumnya = kontrol.querySelector('[data-arah="-1"]');
            var berikutnya = kontrol.querySelector('[data-arah="1"]');

            function perbarui() {
                kontrol.hidden = slider.scrollWidth <= slider.clientWidth + 2;
                sebelumnya.disabled = slider.scrollLeft <= 2;
                berikutnya.disabled = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 2;
            }

            [sebelumnya, berikutnya].forEach(function (tombol) {
                tombol.addEventListener('click', function () {
                    var kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    slider.scrollBy({ left: Number(tombol.getAttribute('data-arah')) * slider.clientWidth, behavior: kurangiGerak ? 'auto' : 'smooth' });
                });
            });

            slider.addEventListener('scroll', perbarui, { passive: true });
            window.addEventListener('resize', perbarui);
            perbarui();
        })();
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/adzraaaliyyahalthaaf/Downloads/akademik-app_Adzraa_102042500138/resources/views/partials/berita-dasbor.blade.php ENDPATH**/ ?>