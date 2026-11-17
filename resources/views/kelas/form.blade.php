@extends('tata-letak')

@php($mengubah = $kelas->exists)

@section('judul', $mengubah ? 'Ubah Kelas' : 'Buat Kelas')

@section('isi')
    <div class="kepala"><h1>{{ $mengubah ? 'Ubah Kelas' : 'Buat Kelas' }}</h1></div>

    <form class="formulir" method="POST" novalidate
          action="{{ $mengubah ? route('kelas.perbarui', $kelas) : route('kelas.simpan') }}">
        @csrf
        @if ($mengubah)
            @method('PUT')
        @endif

        <div class="kolom">
            <label for="mata_kuliah_id">Mata kuliah</label>
            <select id="mata_kuliah_id" name="mata_kuliah_id" required
                    @error('mata_kuliah_id') aria-invalid="true" aria-describedby="galat-mata_kuliah_id" @enderror>
                @foreach ($daftarMataKuliah as $mk)
                    <option value="{{ $mk->id }}" @selected((int) old('mata_kuliah_id', $kelas->mata_kuliah_id) === $mk->id)>{{ $mk->kode }} {{ $mk->nama }}</option>
                @endforeach
            </select>
            @include('partials.galat', ['nama' => 'mata_kuliah_id'])
        </div>

        <div class="kolom">
            <label for="tahun_ajaran_id">Tahun ajaran</label>
            <select id="tahun_ajaran_id" name="tahun_ajaran_id" required
                    @error('tahun_ajaran_id') aria-invalid="true" aria-describedby="galat-tahun_ajaran_id" @enderror>
                @foreach ($daftarTahun as $tahun)
                    <option value="{{ $tahun->id }}" @selected((int) old('tahun_ajaran_id', $kelas->tahun_ajaran_id) === $tahun->id)>{{ $tahun->nama }}{{ $tahun->aktif ? ' (aktif)' : '' }}</option>
                @endforeach
            </select>
            @include('partials.galat', ['nama' => 'tahun_ajaran_id'])
        </div>

        <div class="kolom">
            <label for="nama">Nama kelas</label>
            <input id="nama" name="nama" type="text" value="{{ old('nama', $kelas->nama) }}" required
                   @error('nama') aria-invalid="true" aria-describedby="galat-nama" @enderror>
            <p class="petunjuk">Contoh: A, B, atau 01.</p>
            @include('partials.galat', ['nama' => 'nama'])
        </div>

        <div class="kolom">
            <label for="dosen_id">Dosen pengampu</label>
            <select id="dosen_id" name="dosen_id" required
                    @error('dosen_id') aria-invalid="true" aria-describedby="galat-dosen_id" @enderror>
                @foreach ($daftarDosen as $dosen)
                    <option value="{{ $dosen->id }}" @selected((int) old('dosen_id', $kelas->dosen_id) === $dosen->id)>{{ $dosen->name }} ({{ $dosen->email }})</option>
                @endforeach
            </select>
            @include('partials.galat', ['nama' => 'dosen_id'])
        </div>

        <div class="aksi">
            <button type="submit">Simpan</button>
            <a class="tombol sekunder" href="{{ route('kelas.indeks') }}">Batal</a>
        </div>
    </form>
@endsection
