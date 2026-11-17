@extends('tata-letak')

@section('judul', 'Tugas '.$kelas->mataKuliah->kode)

@section('isi')
    <div class="kepala">
        <div>
            <h1>Tugas</h1>
            <p>{{ $kelas->label() }}</p>
        </div>
        <a class="tombol" href="{{ route('tugas.buat', $kelas) }}">Buat tugas</a>
    </div>

    @include('partials.menu-kelas', ['kelas' => $kelas, 'aktif' => 'tugas'])

    @if ($daftarTugas->isEmpty())
        <p class="kartu kosong">Belum ada tugas.</p>
    @else
        <div class="bungkus-tabel">
            <table>
                <caption>Daftar tugas</caption>
                <thead>
                    <tr><th scope="col">Judul</th><th scope="col">Tenggat</th><th scope="col">Terkumpul</th><th scope="col">Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach ($daftarTugas as $tugas)
                        <tr>
                            <td>{{ $tugas->judul }}</td>
                            <td>{{ $tugas->tenggat->format('d/m/Y H:i') }}</td>
                            <td>{{ $tugas->pengumpulan_count }} / {{ $jumlahMahasiswa }}</td>
                            <td>
                                <div class="aksi">
                                    <a class="tombol kecil" href="{{ route('tugas.tampil', $tugas) }}">Pengumpulan</a>
                                    <a class="tombol sekunder kecil" href="{{ route('tugas.ubah', $tugas) }}">Ubah</a>
                                    <form method="POST" action="{{ route('tugas.hapus', $tugas) }}" data-konfirmasi="Hapus tugas {{ $tugas->judul }} beserta semua pengumpulannya?">
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
