@extends('tata-letak')

@php($mengubah = $berita->exists)

@section('judul', $mengubah ? 'Ubah Berita' : 'Tulis Berita')

@section('isi')
    <div class="kepala"><h1>{{ $mengubah ? 'Ubah Berita' : 'Tulis Berita' }}</h1></div>

    <form class="formulir" method="POST" enctype="multipart/form-data" novalidate
          action="{{ $mengubah ? route('berita.perbarui', $berita) : route('berita.simpan') }}">
        @csrf
        @if ($mengubah)
            @method('PUT')
        @endif

        <div class="kolom">
            <label for="judul">Judul</label>
            <input id="judul" name="judul" type="text" value="{{ old('judul', $berita->judul) }}" required
                   @error('judul') aria-invalid="true" aria-describedby="galat-judul" @enderror>
            @include('partials.galat', ['nama' => 'judul'])
        </div>

        <div class="dua-kolom">
            <div class="kolom">
                <label for="tanggal_terbit">Tanggal terbit</label>
                <input id="tanggal_terbit" name="tanggal_terbit" type="date" required
                       value="{{ old('tanggal_terbit', $berita->tanggal_terbit?->format('Y-m-d')) }}"
                       @error('tanggal_terbit') aria-invalid="true" aria-describedby="galat-tanggal_terbit" @enderror>
                <p class="petunjuk">Berita tampil di dasbor mulai tanggal ini.</p>
                @include('partials.galat', ['nama' => 'tanggal_terbit'])
            </div>

            <div class="kolom">
                <label for="kedaluwarsa_pada">Tanggal kedaluwarsa (opsional)</label>
                <input id="kedaluwarsa_pada" name="kedaluwarsa_pada" type="date"
                       value="{{ old('kedaluwarsa_pada', $berita->kedaluwarsa_pada?->format('Y-m-d')) }}"
                       @error('kedaluwarsa_pada') aria-invalid="true" aria-describedby="galat-kedaluwarsa_pada" @enderror>
                <p class="petunjuk">Setelah tanggal ini berita hanya terlihat oleh admin dan dosen.</p>
                @include('partials.galat', ['nama' => 'kedaluwarsa_pada'])
            </div>
        </div>

        <div class="kolom">
            <label for="isi">Isi berita</label>
            <textarea id="isi" name="isi" required
                      @error('isi') aria-invalid="true" aria-describedby="galat-isi" @enderror>{{ old('isi', $berita->isi) }}</textarea>
            @include('partials.galat', ['nama' => 'isi'])
        </div>

        <div class="kolom">
            <label for="banner">Banner (opsional)</label>

            @if ($mengubah && $berita->adaBanner())
                <p><img src="{{ $berita->urlBanner() }}" alt="Banner saat ini" style="max-width:16rem;height:auto;border-radius:.75rem"></p>
                <label class="sebaris" for="hapus_banner">
                    <input id="hapus_banner" name="hapus_banner" type="checkbox" value="1">
                    Hapus banner saat ini
                </label>
            @endif

            <input id="banner" name="banner" type="file" accept="image/jpeg,image/png,image/webp"
                   data-lebar-maks="{{ \App\Models\Berita::BANNER_LEBAR_MAKS }}" data-tinggi-maks="{{ \App\Models\Berita::BANNER_TINGGI_MAKS }}"
                   @error('banner') aria-invalid="true" aria-describedby="galat-banner" @enderror>
            <p class="petunjuk">JPG, PNG, atau WebP. Resolusi maksimal {{ \App\Models\Berita::BANNER_LEBAR_MAKS }} x {{ \App\Models\Berita::BANNER_TINGGI_MAKS }} piksel. Gambar yang lebih besar dapat dipotong dan disesuaikan setelah dipilih.</p>
            @include('partials.galat', ['nama' => 'banner'])

            <div id="pratinjau-banner" hidden style="margin-top:.75rem">
                <img id="gambar-pratinjau" alt="Pratinjau banner" style="max-width:20rem;height:auto;border-radius:.75rem;display:block">
                <p class="petunjuk" id="info-pratinjau"></p>
                <button type="button" class="sekunder kecil" id="potong-ulang" hidden>Potong ulang</button>
            </div>
        </div>

        <div class="aksi">
            <button type="submit">Simpan</button>
            <a class="tombol sekunder" href="{{ route('berita.indeks') }}">Batal</a>
        </div>
    </form>

    <dialog class="dialog lebar" id="dialog-potong" aria-labelledby="judul-potong">
        <h2 id="judul-potong">Potong dan sesuaikan banner</h2>
        <p class="petunjuk">Seret gambar untuk menggeser area potong dan pakai penggeser untuk memperbesar. Hasil dipotong menjadi {{ \App\Models\Berita::BANNER_LEBAR_MAKS }} x {{ \App\Models\Berita::BANNER_TINGGI_MAKS }} piksel (16:9).</p>
        <canvas id="kanvas-potong" width="640" height="360" tabindex="0" role="img"
                aria-label="Area potong banner. Gunakan tombol panah untuk menggeser, plus dan minus untuk memperbesar."
                style="display:block;width:100%;height:auto;aspect-ratio:16/9;border-radius:.75rem;background:#fff;touch-action:none;cursor:grab"></canvas>
        <div class="kolom" style="margin-top:.75rem">
            <label for="zoom-potong">Perbesar</label>
            <input id="zoom-potong" type="range" min="0" max="1" step="0.001" value="0">
        </div>
        <div class="aksi">
            <button type="button" id="batal-potong" class="sekunder">Batal</button>
            <button type="button" id="terapkan-potong">Terapkan potongan</button>
        </div>
    </dialog>

    <span hidden id="t-sesuai">Resolusi sesuai, tidak perlu dipotong.</span>
    <span hidden id="t-dipotong">Gambar dipotong menjadi 1280 x 720 piksel.</span>
@endsection

@push('skrip')
    <script>
        (function () {
            var input = document.getElementById('banner');
            var dialog = document.getElementById('dialog-potong');
            if (!input || !dialog || typeof dialog.showModal !== 'function') { return; }

            var LEBAR_MAKS = parseInt(input.getAttribute('data-lebar-maks'), 10);
            var TINGGI_MAKS = parseInt(input.getAttribute('data-tinggi-maks'), 10);
            var BINGKAI_W = 640;
            var BINGKAI_H = 360;

            var kanvas = document.getElementById('kanvas-potong');
            var ctx = kanvas.getContext('2d');
            var geser = document.getElementById('zoom-potong');
            var kotakPratinjau = document.getElementById('pratinjau-banner');
            var gambarPratinjau = document.getElementById('gambar-pratinjau');
            var infoPratinjau = document.getElementById('info-pratinjau');
            var tombolUlang = document.getElementById('potong-ulang');

            var gambar = null;
            var urlAsli = null;
            var urlHasil = null;
            var namaDasar = 'banner';
            var skala = 1;
            var skalaMin = 1;
            var skalaMaks = 1;
            var offX = 0;
            var offY = 0;
            var seret = null;

            function teks(id) {
                var el = document.getElementById(id);
                return el ? el.textContent.trim() : '';
            }

            function batasi() {
                var lebar = gambar.naturalWidth * skala;
                var tinggi = gambar.naturalHeight * skala;
                offX = Math.min(0, Math.max(BINGKAI_W - lebar, offX));
                offY = Math.min(0, Math.max(BINGKAI_H - tinggi, offY));
            }

            function gambarUlang() {
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, BINGKAI_W, BINGKAI_H);
                ctx.drawImage(gambar, offX, offY, gambar.naturalWidth * skala, gambar.naturalHeight * skala);
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.75)';
                ctx.lineWidth = 1;
                for (var i = 1; i < 3; i++) {
                    ctx.beginPath();
                    ctx.moveTo((BINGKAI_W * i) / 3, 0);
                    ctx.lineTo((BINGKAI_W * i) / 3, BINGKAI_H);
                    ctx.moveTo(0, (BINGKAI_H * i) / 3);
                    ctx.lineTo(BINGKAI_W, (BINGKAI_H * i) / 3);
                    ctx.stroke();
                }
            }

            function ubahSkala(baru) {
                var pusatX = (BINGKAI_W / 2 - offX) / skala;
                var pusatY = (BINGKAI_H / 2 - offY) / skala;
                skala = Math.min(skalaMaks, Math.max(skalaMin, baru));
                offX = BINGKAI_W / 2 - pusatX * skala;
                offY = BINGKAI_H / 2 - pusatY * skala;
                batasi();
                geser.value = skala;
                gambarUlang();
            }

            function tampilkanPratinjau(url, info) {
                gambarPratinjau.src = url;
                infoPratinjau.textContent = info;
                tombolUlang.hidden = !gambar;
                kotakPratinjau.hidden = false;
            }

            function sembunyikanPratinjau() {
                kotakPratinjau.hidden = true;
                gambarPratinjau.removeAttribute('src');
            }

            function bukaPotong() {
                skalaMin = Math.max(BINGKAI_W / gambar.naturalWidth, BINGKAI_H / gambar.naturalHeight);
                skalaMaks = Math.max(0.5, skalaMin * 2);
                geser.min = skalaMin;
                geser.max = skalaMaks;
                geser.step = (skalaMaks - skalaMin) / 200;
                skala = skalaMin;
                offX = (BINGKAI_W - gambar.naturalWidth * skala) / 2;
                offY = (BINGKAI_H - gambar.naturalHeight * skala) / 2;
                batasi();
                geser.value = skala;
                gambarUlang();
                dialog.setAttribute('data-hasil', 'batal');
                dialog.showModal();
                kanvas.focus();
            }

            function terapkan() {
                var keluar = document.createElement('canvas');
                keluar.width = LEBAR_MAKS;
                keluar.height = TINGGI_MAKS;
                var c = keluar.getContext('2d');
                var rasio = LEBAR_MAKS / BINGKAI_W;
                c.fillStyle = '#ffffff';
                c.fillRect(0, 0, LEBAR_MAKS, TINGGI_MAKS);
                c.drawImage(gambar, offX * rasio, offY * rasio, gambar.naturalWidth * skala * rasio, gambar.naturalHeight * skala * rasio);

                keluar.toBlob(function (blob) {
                    if (!blob) { return; }
                    var berkas = new File([blob], namaDasar + '.jpg', { type: 'image/jpeg' });
                    var transfer = new DataTransfer();
                    transfer.items.add(berkas);
                    input.files = transfer.files;
                    if (urlHasil) { URL.revokeObjectURL(urlHasil); }
                    urlHasil = URL.createObjectURL(blob);
                    tampilkanPratinjau(urlHasil, teks('t-dipotong'));
                    dialog.setAttribute('data-hasil', 'ya');
                    dialog.close();
                }, 'image/jpeg', 0.92);
            }

            input.addEventListener('change', function () {
                var berkas = input.files && input.files[0];
                if (!berkas) { sembunyikanPratinjau(); return; }

                if (urlAsli) { URL.revokeObjectURL(urlAsli); }
                urlAsli = URL.createObjectURL(berkas);
                namaDasar = berkas.name.replace(/\.[^.]+$/, '') || 'banner';

                var gambarBaru = new Image();
                gambarBaru.onload = function () {
                    if (gambarBaru.naturalWidth > LEBAR_MAKS || gambarBaru.naturalHeight > TINGGI_MAKS) {
                        gambar = gambarBaru;
                        bukaPotong();
                    } else {
                        gambar = null;
                        tampilkanPratinjau(urlAsli, teks('t-sesuai'));
                    }
                };
                gambarBaru.onerror = function () { sembunyikanPratinjau(); };
                gambarBaru.src = urlAsli;
            });

            tombolUlang.addEventListener('click', function () { if (gambar) { bukaPotong(); } });
            document.getElementById('terapkan-potong').addEventListener('click', terapkan);
            document.getElementById('batal-potong').addEventListener('click', function () { dialog.close(); });

            dialog.addEventListener('close', function () {
                if (dialog.getAttribute('data-hasil') !== 'ya') {
                    input.value = '';
                    gambar = null;
                    sembunyikanPratinjau();
                }
            });

            geser.addEventListener('input', function () { ubahSkala(parseFloat(geser.value)); });

            kanvas.addEventListener('pointerdown', function (e) {
                seret = { x: e.clientX, y: e.clientY, ox: offX, oy: offY };
                kanvas.setPointerCapture(e.pointerId);
                kanvas.style.cursor = 'grabbing';
            });
            kanvas.addEventListener('pointermove', function (e) {
                if (!seret) { return; }
                var rasio = BINGKAI_W / kanvas.getBoundingClientRect().width;
                offX = seret.ox + (e.clientX - seret.x) * rasio;
                offY = seret.oy + (e.clientY - seret.y) * rasio;
                batasi();
                gambarUlang();
            });
            ['pointerup', 'pointercancel'].forEach(function (nama) {
                kanvas.addEventListener(nama, function () { seret = null; kanvas.style.cursor = 'grab'; });
            });
            kanvas.addEventListener('keydown', function (e) {
                var langkah = 16;
                var dipakai = true;
                if (e.key === 'ArrowLeft') { offX += langkah; }
                else if (e.key === 'ArrowRight') { offX -= langkah; }
                else if (e.key === 'ArrowUp') { offY += langkah; }
                else if (e.key === 'ArrowDown') { offY -= langkah; }
                else if (e.key === '+' || e.key === '=') { ubahSkala(skala * 1.1); return e.preventDefault(); }
                else if (e.key === '-') { ubahSkala(skala / 1.1); return e.preventDefault(); }
                else { dipakai = false; }
                if (dipakai) { batasi(); gambarUlang(); e.preventDefault(); }
            });
        })();
    </script>
@endpush
