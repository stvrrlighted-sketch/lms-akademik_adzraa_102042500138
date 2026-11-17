<?php $__env->startSection('judul', 'Tambah Akun'); ?>

<?php $__env->startSection('isi'); ?>
    <div class="kepala">
        <div>
            <h1>Tambah Akun</h1>
            <p>Buat satu akun atau banyak akun sekaligus. Email dibuat otomatis dari username dan peran.</p>
        </div>
    </div>

    <form class="formulir lebar" method="POST" action="<?php echo e(route('pengguna.simpan')); ?>" novalidate>
        <?php echo csrf_field(); ?>

        <?php if($errors->any()): ?>
            <div class="info bahaya" role="alert" style="margin-bottom:1rem">
                <ul style="margin:0;padding-left:1.25rem">
                    <?php $__currentLoopData = array_unique($errors->all()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pesan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($pesan); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="kolom" style="max-width:24rem">
            <label for="role">Peran untuk semua akun di tabel</label>
            <select id="role" name="role" required>
                <?php $__currentLoopData = \App\Models\User::DAFTAR_PERAN; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $peran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($peran); ?>" <?php if(old('role', 'mahasiswa') === $peran): echo 'selected'; endif; ?>><?php echo e($peran); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="petunjuk">Format email: <strong id="contoh-email"></strong></p>
        </div>

        <p class="petunjuk">Isi tabel seperti spreadsheet. Anda bisa menempel (Ctrl+V) daftar dari Excel: tiap baris menjadi satu akun, kolom dipisah tab. Username kosong dibuat dari nama, kata sandi kosong dibuat acak. Maksimal 200 akun sekali kirim.</p>

        <div class="bungkus-tabel" style="margin-bottom:1rem">
            <table class="grid-excel">
                <caption>Tabel input akun</caption>
                <thead>
                    <tr>
                        <th scope="col" style="width:3rem">No</th>
                        <th scope="col">Nama lengkap</th>
                        <th scope="col">Username (opsional)</th>
                        <th scope="col">Kata sandi (opsional, min. 8 karakter)</th>
                    </tr>
                </thead>
                <tbody id="badan-grid">
                    <?php $__currentLoopData = old('baris', [['nama' => '', 'username' => '', 'password' => '']]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kunci => $baris): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td></td>
                            <td><input type="text" name="baris[<?php echo e($kunci); ?>][nama]" value="<?php echo e($baris['nama'] ?? ''); ?>" maxlength="255"></td>
                            <td><input type="text" name="baris[<?php echo e($kunci); ?>][username]" value="<?php echo e($baris['username'] ?? ''); ?>" maxlength="60"></td>
                            <td><input type="text" name="baris[<?php echo e($kunci); ?>][password]" value="" maxlength="100" autocomplete="off"></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="aksi">
            <button type="button" id="tambah-baris" class="sekunder">Tambah baris</button>
            <button type="submit">Buat akun</button>
            <a class="tombol sekunder" href="<?php echo e(route('pengguna.kelola')); ?>">Batal</a>
        </div>
    </form>

    <script>
        (function () {
            var badan = document.getElementById('badan-grid');
            var urutan = 10000;
            var domain = {
                admin: 'admin.telkomuniversity.ac.id',
                dosen: 'dosen.telkomuniversity.ac.id',
                mahasiswa: 'student.telkomuniversity.ac.id'
            };
            var judulKolom = ['Nama', 'Username', 'Kata sandi'];

            function beriNomor() {
                for (var i = 0; i < badan.rows.length; i++) {
                    badan.rows[i].cells[0].textContent = i + 1;
                    for (var k = 1; k <= 3; k++) {
                        badan.rows[i].cells[k].querySelector('input').setAttribute('aria-label', judulKolom[k - 1] + ' baris ' + (i + 1));
                    }
                }
            }

            function tambahBaris() {
                var kunci = urutan++;
                var tr = document.createElement('tr');
                tr.innerHTML = '<td></td>' +
                    '<td><input type="text" name="baris[' + kunci + '][nama]" maxlength="255"></td>' +
                    '<td><input type="text" name="baris[' + kunci + '][username]" maxlength="60"></td>' +
                    '<td><input type="text" name="baris[' + kunci + '][password]" maxlength="100" autocomplete="off"></td>';
                badan.appendChild(tr);
                beriNomor();
                return tr;
            }

            document.getElementById('tambah-baris').addEventListener('click', function () {
                tambahBaris().querySelector('input').focus();
            });

            badan.addEventListener('paste', function (e) {
                var teks = (e.clipboardData || window.clipboardData).getData('text');
                if (teks.indexOf('\n') === -1 && teks.indexOf('\t') === -1) { return; }

                var sel = e.target.closest('td');
                var baris = e.target.closest('tr');
                if (!sel || !baris) { return; }

                e.preventDefault();
                var kolomAwal = sel.cellIndex;
                var barisAwal = baris.rowIndex - 1;
                var data = teks.replace(/\r/g, '').split('\n');
                if (data[data.length - 1] === '') { data.pop(); }

                data.forEach(function (garis, i) {
                    if (barisAwal + i >= 200) { return; }
                    var tr = badan.rows[barisAwal + i] || tambahBaris();
                    garis.split('\t').forEach(function (nilai, j) {
                        var sel2 = tr.cells[kolomAwal + j];
                        var input = sel2 ? sel2.querySelector('input') : null;
                        if (input) { input.value = nilai.trim(); }
                    });
                });
            });

            var pilihanPeran = document.getElementById('role');
            var contoh = document.getElementById('contoh-email');
            function perbaruiContoh() { contoh.textContent = 'username@' + domain[pilihanPeran.value]; }
            pilihanPeran.addEventListener('change', perbaruiContoh);

            beriNomor();
            perbaruiContoh();
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tata-letak', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/adzraaaliyyahalthaaf/Downloads/akademik-app_Adzraa_102042500138/resources/views/pengguna/buat.blade.php ENDPATH**/ ?>