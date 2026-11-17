@extends('tata-letak')

@section('judul', 'Tambah Akun')

@section('isi')
    <div class="kepala">
        <div>
            <h1>Tambah Akun</h1>
            <p>Buat satu akun atau banyak akun sekaligus. Email dibuat otomatis dari username dan peran.</p>
        </div>
    </div>

    <form class="formulir lebar" method="POST" action="{{ route('pengguna.simpan') }}" novalidate>
        @csrf

        @if ($errors->any())
            <div class="info bahaya" role="alert" style="margin-bottom:1rem">
                <ul style="margin:0;padding-left:1.25rem">
                    @foreach (array_unique($errors->all()) as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="kolom" style="max-width:24rem">
            <label for="role">Peran untuk semua akun di tabel</label>
            <select id="role" name="role" required>
                @foreach (\App\Models\User::DAFTAR_PERAN as $peran)
                    <option value="{{ $peran }}" @selected(old('role', 'mahasiswa') === $peran)>{{ $peran }}</option>
                @endforeach
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
                    @foreach (old('baris', [['nama' => '', 'username' => '', 'password' => '']]) as $kunci => $baris)
                        <tr>
                            <td></td>
                            <td><input type="text" name="baris[{{ $kunci }}][nama]" value="{{ $baris['nama'] ?? '' }}" maxlength="255"></td>
                            <td><input type="text" name="baris[{{ $kunci }}][username]" value="{{ $baris['username'] ?? '' }}" maxlength="60"></td>
                            <td><input type="text" name="baris[{{ $kunci }}][password]" value="" maxlength="100" autocomplete="off"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="aksi">
            <button type="button" id="tambah-baris" class="sekunder">Tambah baris</button>
            <button type="submit">Buat akun</button>
            <a class="tombol sekunder" href="{{ route('pengguna.kelola') }}">Batal</a>
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
@endsection
