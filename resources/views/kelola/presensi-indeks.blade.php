@extends('tata-letak')

@section('judul', 'Presensi '.$kelas->mataKuliah->kode)

@section('isi')
    <div class="kepala">
        <div>
            <h1>Presensi</h1>
            <p>{{ $kelas->label() }}</p>
        </div>
        <a class="tombol sekunder" href="{{ route('presensi.ekspor', $kelas) }}">Ekspor rekap (CSV)</a>
    </div>

    @include('partials.menu-kelas', ['kelas' => $kelas, 'aktif' => 'presensi'])

    <section aria-labelledby="judul-sesi">
        <h2 id="judul-sesi">Sesi per pertemuan</h2>
        <div class="bungkus-tabel">
            <table>
                <caption>Sesi presensi</caption>
                <thead>
                    <tr><th scope="col">Pertemuan</th><th scope="col">Topik</th><th scope="col">Status sesi</th><th scope="col">Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach ($daftarPertemuan as $pertemuan)
                        @php($sesi = $pertemuan->sesiPresensi)
                        <tr>
                            <td>{{ $pertemuan->nomor }}</td>
                            <td>{{ $pertemuan->topik ?: '-' }}</td>
                            <td>
                                @if ($sesi && $sesi->dibuka)
                                    <span class="lencana ok">Dibuka</span>
                                @elseif ($sesi)
                                    <span class="lencana">Ditutup</span>
                                @else
                                    <span class="lencana">Belum dibuka</span>
                                @endif
                            </td>
                            <td>
                                <div class="aksi">
                                    @if ($sesi && $sesi->dibuka)
                                        <form method="POST" action="{{ route('presensi.tutup', $pertemuan) }}">
                                            @csrf
                                            <button type="submit" class="sekunder kecil">Tutup sesi</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('presensi.buka', $pertemuan) }}">
                                            @csrf
                                            <button type="submit" class="kecil">Buka sesi</button>
                                        </form>
                                    @endif
                                    <a class="tombol sekunder kecil" href="{{ route('presensi.sesi', $pertemuan) }}">Kelola kehadiran</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section aria-labelledby="judul-rekap">
        <h2 id="judul-rekap">Rekap presensi</h2>
        <p class="petunjuk">Persentase dihitung dari jumlah sesi yang sudah dibuat. Mahasiswa tanpa catatan pada sesi yang ada dihitung Alpa.</p>

        @if ($daftarRekap->isEmpty())
            <p class="kartu kosong">Belum ada mahasiswa di kelas ini.</p>
        @else
            <div class="bungkus-tabel">
                <table>
                    <caption>Rekap presensi mahasiswa</caption>
                    <thead>
                        <tr>
                            <th scope="col">Mahasiswa</th><th scope="col">Hadir</th><th scope="col">Izin</th>
                            <th scope="col">Sakit</th><th scope="col">Alpa</th><th scope="col">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarRekap as $baris)
                            <tr>
                                <td>{{ $baris['mahasiswa']->name }}</td>
                                <td>{{ $baris['hadir'] }}</td>
                                <td>{{ $baris['izin'] }}</td>
                                <td>{{ $baris['sakit'] }}</td>
                                <td>{{ $baris['alpa'] }}</td>
                                <td>{{ $baris['persen'] === null ? '-' : $baris['persen'].'%' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
