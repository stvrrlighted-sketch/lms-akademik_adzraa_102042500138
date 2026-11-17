@extends('tata-letak')

@section('judul', 'Dasbor Dosen')

@section('isi')
    <div class="kepala"><h1>Dasbor Dosen</h1></div>

    @include('partials.berita-dasbor')

    <section aria-labelledby="judul-kelas">
        <h2 id="judul-kelas">Kelas aktif {{ $tahunAktif ? 'semester '.$tahunAktif->nama : '' }}</h2>

        @if ($daftarKelas->isEmpty())
            <p class="kartu kosong">Anda belum mengampu kelas pada semester ini.</p>
        @else
            <div class="grid-kartu">
                @foreach ($daftarKelas as $kelas)
                    <a class="kartu interaktif" href="{{ route('kelola.tampil', $kelas) }}" style="text-decoration:none;color:inherit">
                        <h3>{{ $kelas->mataKuliah->nama }}</h3>
                        <p class="redup teks-kecil">{{ $kelas->mataKuliah->kode }}, Kelas {{ $kelas->nama }}, {{ $kelas->mataKuliah->sks }} SKS</p>
                        <span class="lencana">{{ $kelas->mahasiswa_count }} mahasiswa</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection
