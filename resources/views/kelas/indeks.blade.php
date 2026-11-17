@extends('tata-letak')

@section('judul', 'Kelas')

@section('isi')
    <div class="kepala">
        <h1>Kelas</h1>
        <a class="tombol" href="{{ route('kelas.buat') }}">Buat kelas</a>
    </div>

    @if ($daftarKelas->isEmpty())
        <p class="kartu kosong">Belum ada kelas. Pastikan tahun ajaran, mata kuliah, dan akun dosen sudah dibuat.</p>
    @else
        <div class="bungkus-tabel">
            <table>
                <caption>Daftar kelas</caption>
                <thead>
                    <tr>
                        <th scope="col">Mata kuliah</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Tahun ajaran</th>
                        <th scope="col">Dosen</th>
                        <th scope="col">Mahasiswa</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarKelas as $kelas)
                        <tr>
                            <td>{{ $kelas->mataKuliah->kode }} {{ $kelas->mataKuliah->nama }}</td>
                            <td>{{ $kelas->nama }}</td>
                            <td>{{ $kelas->tahunAjaran->nama }}</td>
                            <td>{{ $kelas->dosen->name }}</td>
                            <td>{{ $kelas->mahasiswa_count }}</td>
                            <td>
                                <div class="aksi">
                                    <a class="tombol kecil" href="{{ route('kelola.tampil', $kelas) }}">Buka</a>
                                    <a class="tombol sekunder kecil" href="{{ route('kelas.mahasiswa', $kelas) }}">Mahasiswa</a>
                                    <a class="tombol sekunder kecil" href="{{ route('kelas.ubah', $kelas) }}">Ubah</a>
                                    <form method="POST" action="{{ route('kelas.hapus', $kelas) }}" data-konfirmasi="Hapus kelas {{ $kelas->label() }}? Materi, tugas, kuis, presensi, dan nilai kelas ini ikut terhapus.">
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
