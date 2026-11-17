@extends('tata-letak')

@php($mengubah = $tugas->exists)

@section('judul', $mengubah ? 'Ubah Tugas' : 'Buat Tugas')

@section('isi')
    <div class="kepala">
        <div>
            <h1>{{ $mengubah ? 'Ubah Tugas' : 'Buat Tugas' }}</h1>
            <p>{{ $kelas->label() }}</p>
        </div>
    </div>

    <form class="formulir" method="POST" novalidate
          action="{{ $mengubah ? route('tugas.perbarui', $tugas) : route('tugas.simpan', $kelas) }}">
        @csrf
        @if ($mengubah)
            @method('PUT')
        @endif

        <div class="kolom">
            <label for="judul">Judul</label>
            <input id="judul" name="judul" type="text" value="{{ old('judul', $tugas->judul) }}" required
                   @error('judul') aria-invalid="true" aria-describedby="galat-judul" @enderror>
            @include('partials.galat', ['nama' => 'judul'])
        </div>

        <div class="kolom">
            <label for="instruksi">Instruksi</label>
            <textarea id="instruksi" name="instruksi" required
                      @error('instruksi') aria-invalid="true" aria-describedby="galat-instruksi" @enderror>{{ old('instruksi', $tugas->instruksi) }}</textarea>
            @include('partials.galat', ['nama' => 'instruksi'])
        </div>

        <div class="kolom">
            <label for="tenggat">Tenggat</label>
            <input id="tenggat" name="tenggat" type="datetime-local" required
                   value="{{ old('tenggat', $tugas->tenggat?->format('Y-m-d\TH:i')) }}"
                   @error('tenggat') aria-invalid="true" aria-describedby="galat-tenggat" @enderror>
            <p class="petunjuk">Pengumpulan setelah tenggat tetap diterima dan ditandai Terlambat.</p>
            @include('partials.galat', ['nama' => 'tenggat'])
        </div>

        <div class="aksi">
            <button type="submit">Simpan</button>
            <a class="tombol sekunder" href="{{ route('tugas.indeks', $kelas) }}">Batal</a>
        </div>
    </form>
@endsection
