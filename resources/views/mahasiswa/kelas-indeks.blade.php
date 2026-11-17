@extends('tata-letak')

@section('judul', 'Kelas Saya')

@section('isi')
    <div class="kepala"><h1>Kelas Saya</h1></div>

    @if ($daftarKelas->isEmpty())
        <p class="kartu kosong">Anda belum terdaftar di kelas mana pun. Hubungi admin.</p>
    @else
        <div class="grid-kartu">
            @foreach ($daftarKelas as $kelas)
                <a class="kartu interaktif" href="{{ route('kelassaya.tampil', $kelas) }}" style="text-decoration:none;color:inherit">
                    <h3>{{ $kelas->mataKuliah->nama }}</h3>
                    <p class="redup teks-kecil">{{ $kelas->mataKuliah->kode }}, Kelas {{ $kelas->nama }}, {{ $kelas->mataKuliah->sks }} SKS</p>
                    <p class="teks-kecil">{{ $kelas->tahunAjaran->nama }}, dosen {{ $kelas->dosen->name }}</p>
                </a>
            @endforeach
        </div>
    @endif
@endsection
