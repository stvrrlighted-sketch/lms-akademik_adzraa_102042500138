<section aria-labelledby="judul-berita">
    <div class="kepala-bagian">
        <h2 id="judul-berita">Berita kampus</h2>

        @if ($daftarBerita->isNotEmpty())
            <div class="kontrol-slider" data-kontrol hidden>
                <button type="button" class="sekunder panah" data-arah="-1" aria-label="Berita sebelumnya">@include('partials.ikon', ['nama' => 'caret-left'])</button>
                <button type="button" class="sekunder panah" data-arah="1" aria-label="Berita berikutnya">@include('partials.ikon', ['nama' => 'caret-right'])</button>
            </div>
        @endif
    </div>

    @if ($daftarBerita->isEmpty())
        <p class="kartu kosong">Belum ada berita.</p>
    @else
        <div class="slider" data-slider tabindex="0" role="region" aria-label="Daftar berita">
            @foreach ($daftarBerita as $berita)
                <a class="kartu interaktif berita-kartu" href="{{ route('berita.tampil', $berita->id) }}">
                    @if ($berita->adaBanner())
                        <img src="{{ $berita->urlBanner() }}" alt="" loading="lazy" width="640" height="360">
                    @endif
                    <div class="berita-isi">
                        <div class="berita-meta">
                            <time datetime="{{ $berita->tanggal_terbit->format('Y-m-d') }}">{{ $berita->tanggal_terbit->format('d/m/Y') }}</time>
                            @if ($berita->kedaluwarsa())
                                <span class="lencana galat">Kedaluwarsa</span>
                            @endif
                            @if ($berita->terjadwal())
                                <span class="lencana">Terjadwal</span>
                            @endif
                        </div>
                        <h3>{{ $berita->judul }}</h3>
                        <p class="ringkas">{{ \Illuminate\Support\Str::limit($berita->isi, 200) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>

@push('skrip')
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
@endpush
