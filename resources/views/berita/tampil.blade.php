@extends('tata-letak')

@section('judul', $berita->judul)

@section('isi')
    <article class="kartu artikel">
        <div>
            <a class="tombol sekunder kecil" href="{{ route('dashboard') }}">
                @include('partials.ikon', ['nama' => 'caret-left'])
                Kembali ke dasbor
            </a>
        </div>

        <div class="berita-meta">
            <time datetime="{{ $berita->tanggal_terbit->format('Y-m-d') }}">{{ $berita->tanggal_terbit->format('d/m/Y') }}</time>
            @if ($berita->pembuat)
                <span>{{ $berita->pembuat->name }}</span>
            @endif
            @if ($berita->kedaluwarsa_pada)
                <span class="lencana {{ $berita->kedaluwarsa() ? 'galat' : '' }}">
                    {{ $berita->kedaluwarsa() ? 'Kedaluwarsa' : 'Berlaku sampai' }} {{ $berita->kedaluwarsa_pada->format('d/m/Y') }}
                </span>
            @endif
        </div>

        <h1>{{ $berita->judul }}</h1>

        @if ($berita->adaBanner())
            @php($ukuran = $berita->ukuranBanner())
            <figure class="gambar-asli">
                <img src="{{ $berita->urlBanner() }}" alt="{{ $berita->judul }}"
                     @if ($ukuran) width="{{ $ukuran[0] }}" height="{{ $ukuran[1] }}" @endif>
                @if ($ukuran)
                    <figcaption class="petunjuk">
                        {{ $ukuran[0] }} x {{ $ukuran[1] }} px.
                        <a href="{{ $berita->urlBanner() }}" target="_blank" rel="noopener noreferrer">Buka gambar ukuran asli</a>
                    </figcaption>
                @endif
            </figure>
        @endif

        <div class="isi-teks">{{ $berita->isi }}</div>
    </article>
@endsection
