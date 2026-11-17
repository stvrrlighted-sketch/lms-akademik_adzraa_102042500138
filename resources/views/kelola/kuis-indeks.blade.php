@extends('tata-letak')

@section('judul', 'Kuis '.$kelas->mataKuliah->kode)

@section('isi')
    <div class="kepala">
        <div>
            <h1>Kuis</h1>
            <p>{{ $kelas->label() }}</p>
        </div>
    </div>

    @include('partials.menu-kelas', ['kelas' => $kelas, 'aktif' => 'kuis'])

    <form class="formulir" method="POST" action="{{ route('kuis.simpan', $kelas) }}" novalidate>
        @csrf
        <h2>Buat kuis baru</h2>
        <div class="dua-kolom">
            <div class="kolom">
                <label for="judul">Judul kuis</label>
                <input id="judul" name="judul" type="text" value="{{ old('judul') }}" required
                       @error('judul') aria-invalid="true" aria-describedby="galat-judul" @enderror>
                @include('partials.galat', ['nama' => 'judul'])
            </div>
            <div class="kolom">
                <label for="tenggat">Tenggat (opsional)</label>
                <input id="tenggat" name="tenggat" type="datetime-local" value="{{ old('tenggat') }}"
                       @error('tenggat') aria-invalid="true" aria-describedby="galat-tenggat" @enderror>
                @include('partials.galat', ['nama' => 'tenggat'])
            </div>
        </div>
        <button type="submit">Buat kuis</button>
    </form>

    @if ($daftarKuis->isEmpty())
        <p class="kartu kosong">Belum ada kuis.</p>
    @else
        <div class="bungkus-tabel">
            <table>
                <caption>Daftar kuis</caption>
                <thead>
                    <tr><th scope="col">Judul</th><th scope="col">Tenggat</th><th scope="col">Soal</th><th scope="col">Dikerjakan</th><th scope="col">Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach ($daftarKuis as $kuis)
                        <tr>
                            <td>{{ $kuis->judul }}</td>
                            <td>{{ $kuis->tenggat?->format('d/m/Y H:i') ?? 'Tanpa tenggat' }}</td>
                            <td>{{ $kuis->soal_count }}</td>
                            <td>{{ $kuis->percobaan_count }}</td>
                            <td>
                                <div class="aksi">
                                    <a class="tombol kecil" href="{{ route('kuis.tampil', $kuis) }}">Kelola soal dan hasil</a>
                                    <form method="POST" action="{{ route('kuis.hapus', $kuis) }}" data-konfirmasi="Hapus kuis {{ $kuis->judul }} beserta semua jawaban?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bahaya kecil">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
