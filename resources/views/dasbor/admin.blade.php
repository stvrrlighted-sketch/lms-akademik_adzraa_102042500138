@extends('tata-letak')

@section('judul', 'Dasbor Admin')

@section('isi')
    <div class="kepala"><h1>Dasbor Admin</h1></div>

    @include('partials.berita-dasbor')

    <section aria-labelledby="judul-ringkasan">
        <h2 id="judul-ringkasan">Ringkasan sistem</h2>
        <dl class="ringkasan">
            <div class="kartu"><dt>Admin</dt><dd>{{ $jumlahPerPeran['admin'] ?? 0 }}</dd></div>
            <div class="kartu"><dt>Dosen</dt><dd>{{ $jumlahPerPeran['dosen'] ?? 0 }}</dd></div>
            <div class="kartu"><dt>Mahasiswa</dt><dd>{{ $jumlahPerPeran['mahasiswa'] ?? 0 }}</dd></div>
            <div class="kartu"><dt>Mata kuliah</dt><dd>{{ $jumlahMataKuliah }}</dd></div>
            <div class="kartu"><dt>Kelas</dt><dd>{{ $jumlahKelas }}</dd></div>
            <div class="kartu"><dt>Permintaan reset</dt><dd>{{ $jumlahPermintaan }}</dd></div>
        </dl>
    </section>

    <div class="aksi">
        <a class="tombol" href="{{ route('berita.buat') }}">Tulis berita</a>
        <a class="tombol sekunder" href="{{ route('pengguna.buat') }}">Tambah akun</a>
        <a class="tombol sekunder" href="{{ route('kelas.indeks') }}">Kelola kelas</a>
        <a class="tombol sekunder" href="{{ route('pengguna.kelola') }}">Permintaan reset kata sandi</a>
    </div>
@endsection
