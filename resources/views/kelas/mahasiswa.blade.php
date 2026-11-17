@extends('tata-letak')

@section('judul', 'Mahasiswa Kelas')

@section('isi')
    <div class="kepala">
        <div>
            <h1>Mahasiswa Kelas</h1>
            <p>{{ $kelas->label() }}, {{ $kelas->tahunAjaran->nama }}</p>
        </div>
        <a class="tombol sekunder" href="{{ route('kelas.indeks') }}">Kembali</a>
    </div>

    <form class="formulir" method="POST" action="{{ route('kelas.daftarkan', $kelas) }}" novalidate>
        @csrf
        <div class="kolom">
            <label for="mahasiswa_id">Daftarkan mahasiswa</label>
            <select id="mahasiswa_id" name="mahasiswa_id[]" multiple required
                    @error('mahasiswa_id') aria-invalid="true" aria-describedby="galat-mahasiswa_id" @enderror>
                @foreach ($calon as $mahasiswa)
                    <option value="{{ $mahasiswa->id }}">{{ $mahasiswa->name }} ({{ $mahasiswa->email }})</option>
                @endforeach
            </select>
            <p class="petunjuk">Tahan Ctrl (atau Cmd) untuk memilih lebih dari satu.</p>
            @include('partials.galat', ['nama' => 'mahasiswa_id'])
        </div>
        <button type="submit" @disabled($calon->isEmpty())>Daftarkan</button>
    </form>

    <section aria-labelledby="judul-terdaftar">
        <h2 id="judul-terdaftar">Terdaftar ({{ $terdaftar->count() }})</h2>

        @if ($terdaftar->isEmpty())
            <p class="kartu kosong">Belum ada mahasiswa di kelas ini.</p>
        @else
            <div class="bungkus-tabel">
                <table>
                    <caption>Mahasiswa terdaftar</caption>
                    <thead>
                        <tr><th scope="col">Nama</th><th scope="col">Email</th><th scope="col">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($terdaftar as $mahasiswa)
                            <tr>
                                <td>{{ $mahasiswa->name }}</td>
                                <td>{{ $mahasiswa->email }}</td>
                                <td>
                                    <form method="POST" action="{{ route('kelas.keluarkan', [$kelas, $mahasiswa]) }}" data-konfirmasi="Keluarkan {{ $mahasiswa->name }} dari kelas ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bahaya kecil">Keluarkan</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
