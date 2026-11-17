@extends('tata-letak')

@section('judul', 'Kehadiran pertemuan '.$pertemuan->nomor)

@section('isi')
    <div class="kepala">
        <div>
            <h1>Kehadiran Pertemuan {{ $pertemuan->nomor }}</h1>
            <p>{{ $kelas->label() }}. Sesi {{ $sesi->dibuka ? 'sedang dibuka untuk check-in mandiri' : 'ditutup' }}.</p>
        </div>
        <a class="tombol sekunder" href="{{ route('presensi.indeks', $kelas) }}">Kembali</a>
    </div>

    @if ($daftarMahasiswa->isEmpty())
        <p class="kartu kosong">Belum ada mahasiswa di kelas ini.</p>
    @else
        <form method="POST" action="{{ route('presensi.simpan', $pertemuan) }}" novalidate>
            @csrf
            <div class="bungkus-tabel" style="margin-bottom:1rem">
                <table>
                    <caption>Status kehadiran</caption>
                    <thead>
                        <tr><th scope="col">Mahasiswa</th><th scope="col">Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarMahasiswa as $mahasiswa)
                            <tr>
                                <td>{{ $mahasiswa->name }}</td>
                                <td>
                                    <select name="status[{{ $mahasiswa->id }}]" aria-label="Status {{ $mahasiswa->name }}" style="max-width:10rem">
                                        @foreach (\App\Models\Presensi::DAFTAR_STATUS as $status)
                                            <option value="{{ $status }}" @selected(($catatan->get($mahasiswa->id)?->status ?? 'Alpa') === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit">Simpan kehadiran</button>
        </form>
    @endif
@endsection
