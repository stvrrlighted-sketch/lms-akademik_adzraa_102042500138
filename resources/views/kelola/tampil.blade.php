@extends('tata-letak')

@section('judul', $kelas->label())

@section('isi')
    <div class="kepala">
        <div>
            <h1>{{ $kelas->mataKuliah->nama }}</h1>
            <p>{{ $kelas->mataKuliah->kode }}, Kelas {{ $kelas->nama }}, {{ $kelas->tahunAjaran->nama }}. Dosen: {{ $kelas->dosen->name }}</p>
        </div>
        @if (auth()->user()->role === 'admin')
            <a class="tombol sekunder" href="{{ route('kelas.mahasiswa', $kelas) }}">Atur pendaftaran mahasiswa</a>
        @endif
    </div>

    @include('partials.menu-kelas', ['kelas' => $kelas, 'aktif' => 'ringkasan'])

    <dl class="ringkasan">
        <div class="kartu"><dt>Mahasiswa</dt><dd>{{ $daftarMahasiswa->count() }}</dd></div>
        <div class="kartu"><dt>Tugas</dt><dd>{{ $kelas->tugas()->count() }}</dd></div>
        <div class="kartu"><dt>Kuis</dt><dd>{{ $kelas->kuis()->count() }}</dd></div>
        <div class="kartu"><dt>SKS</dt><dd>{{ $kelas->mataKuliah->sks }}</dd></div>
    </dl>

    <section aria-labelledby="judul-daftar">
        <h2 id="judul-daftar">Daftar mahasiswa</h2>

        @if ($daftarMahasiswa->isEmpty())
            <p class="kartu kosong">Belum ada mahasiswa di kelas ini.</p>
        @else
            <div class="bungkus-tabel">
                <table>
                    <caption>Mahasiswa terdaftar</caption>
                    <thead>
                        <tr><th scope="col">No</th><th scope="col">Nama</th><th scope="col">Email</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarMahasiswa as $mahasiswa)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mahasiswa->name }}</td>
                                <td>{{ $mahasiswa->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
