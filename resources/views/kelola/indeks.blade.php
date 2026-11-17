@extends('tata-letak')

@section('judul', 'Kelola Kelas')

@section('isi')
    <div class="kepala">
        <h1>{{ auth()->user()->role === 'admin' ? 'Semua Kelas' : 'Kelas Saya' }}</h1>
    </div>

    @if ($daftarKelas->isEmpty())
        <p class="kartu kosong">Belum ada kelas yang dapat Anda kelola.</p>
    @else
        <div class="grid-kartu">
            @foreach ($daftarKelas as $kelas)
                <a class="kartu interaktif" href="{{ route('kelola.tampil', $kelas) }}" style="text-decoration:none;color:inherit">
                    <h3>{{ $kelas->mataKuliah->nama }}</h3>
                    <p class="redup teks-kecil">{{ $kelas->mataKuliah->kode }}, Kelas {{ $kelas->nama }}, {{ $kelas->mataKuliah->sks }} SKS</p>
                    <p class="teks-kecil">{{ $kelas->tahunAjaran->nama }}, dosen {{ $kelas->dosen->name }}</p>
                    <span class="lencana">{{ $kelas->mahasiswa_count }} mahasiswa</span>
                </a>
            @endforeach
        </div>
    @endif
@endsection
