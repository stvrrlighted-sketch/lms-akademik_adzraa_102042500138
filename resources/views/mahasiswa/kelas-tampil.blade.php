@extends('tata-letak')

@section('judul', $kelas->label())

@section('isi')
    <div class="kepala">
        <div>
            <h1>{{ $kelas->mataKuliah->nama }}</h1>
            <p>{{ $kelas->mataKuliah->kode }}, Kelas {{ $kelas->nama }}, {{ $kelas->tahunAjaran->nama }}. Dosen: {{ $kelas->dosen->name }}</p>
        </div>
        <a class="tombol sekunder" href="{{ route('kelassaya.indeks') }}">Semua kelas</a>
    </div>

    <ul class="sub-menu">
        <li><a href="#materi">Materi</a></li>
        <li><a href="#tugas">Tugas</a></li>
        <li><a href="#kuis">Kuis</a></li>
        <li><a href="#presensi">Presensi</a></li>
        <li><a href="#nilai">Nilai</a></li>
    </ul>

    <section id="materi" aria-labelledby="judul-materi">
        <h2 id="judul-materi">Pertemuan dan materi</h2>
        @foreach ($daftarPertemuan as $pertemuan)
            <details class="kartu akordeon" style="margin-bottom:.75rem" @if ($pertemuan->materi->isNotEmpty()) open @endif>
                <summary>Pertemuan {{ $pertemuan->nomor }}: {{ $pertemuan->topik ?: 'Topik belum ditentukan' }}</summary>
                @if ($pertemuan->materi->isEmpty())
                    <p class="kosong">Belum ada materi.</p>
                @else
                    <ul style="list-style:none;margin:0;padding:0;display:grid;gap:.5rem">
                        @foreach ($pertemuan->materi as $materi)
                            <li class="aksi">
                                <span class="lencana">{{ $materi->jenis === 'berkas' ? 'Berkas' : 'Tautan' }}</span>
                                <strong>{{ $materi->judul }}</strong>
                                @if ($materi->jenis === 'berkas')
                                    <a class="tombol sekunder kecil" href="{{ route('unduh.materi', $materi) }}">Unduh {{ $materi->nama_asli }}</a>
                                @else
                                    <a href="{{ $materi->url }}" target="_blank" rel="noopener noreferrer">Buka tautan</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </details>
        @endforeach
    </section>

    <section id="tugas" aria-labelledby="judul-tugas">
        <h2 id="judul-tugas">Tugas</h2>
        @if ($daftarTugas->isEmpty())
            <p class="kartu kosong">Belum ada tugas.</p>
        @else
            <div class="bungkus-tabel">
                <table>
                    <caption>Daftar tugas</caption>
                    <thead>
                        <tr><th scope="col">Tugas</th><th scope="col">Tenggat</th><th scope="col">Status</th><th scope="col">Skor</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarTugas as $tugas)
                            @php($kumpul = $pengumpulanSaya->get($tugas->id))
                            <tr>
                                <td><a href="{{ route('kelassaya.tugas', $tugas) }}">{{ $tugas->judul }}</a></td>
                                <td>{{ $tugas->tenggat->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($kumpul)
                                        <span class="lencana {{ $kumpul->status === 'Terlambat' ? 'peringatan' : 'ok' }}">{{ $kumpul->status }}</span>
                                    @elseif ($tugas->tenggat->isPast())
                                        <span class="lencana galat">Belum dikumpulkan, lewat tenggat</span>
                                    @else
                                        <span class="lencana">Belum dikumpulkan</span>
                                    @endif
                                </td>
                                <td>{{ $kumpul?->skor ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section id="kuis" aria-labelledby="judul-kuis">
        <h2 id="judul-kuis">Kuis</h2>
        @if ($daftarKuis->isEmpty())
            <p class="kartu kosong">Belum ada kuis.</p>
        @else
            <div class="bungkus-tabel">
                <table>
                    <caption>Daftar kuis</caption>
                    <thead>
                        <tr><th scope="col">Kuis</th><th scope="col">Tenggat</th><th scope="col">Status</th><th scope="col">Skor</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarKuis as $kuis)
                            @php($percobaan = $percobaanSaya->get($kuis->id))
                            <tr>
                                <td><a href="{{ route('kelassaya.kuis', $kuis) }}">{{ $kuis->judul }}</a> <span class="teks-kecil redup">({{ $kuis->soal_count }} soal)</span></td>
                                <td>{{ $kuis->tenggat?->format('d/m/Y H:i') ?? 'Tanpa tenggat' }}</td>
                                <td>
                                    @if ($percobaan)
                                        <span class="lencana ok">Selesai</span>
                                    @elseif ($kuis->tenggat && $kuis->tenggat->isPast())
                                        <span class="lencana galat">Ditutup</span>
                                    @else
                                        <span class="lencana">Belum dikerjakan</span>
                                    @endif
                                </td>
                                <td>{{ $percobaan?->skor ?? ($percobaan ? 'Menunggu nilai' : '-') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section id="presensi" aria-labelledby="judul-presensi">
        <h2 id="judul-presensi">Presensi</h2>
        @if ($rekapPresensi)
            <dl class="ringkasan" style="margin-bottom:1rem">
                <div class="kartu"><dt>Kehadiran</dt><dd>{{ $rekapPresensi['persen'] === null ? '-' : $rekapPresensi['persen'].'%' }}</dd></div>
                <div class="kartu"><dt>Hadir</dt><dd>{{ $rekapPresensi['hadir'] }}</dd></div>
                <div class="kartu"><dt>Izin</dt><dd>{{ $rekapPresensi['izin'] }}</dd></div>
                <div class="kartu"><dt>Sakit</dt><dd>{{ $rekapPresensi['sakit'] }}</dd></div>
                <div class="kartu"><dt>Alpa</dt><dd>{{ $rekapPresensi['alpa'] }}</dd></div>
            </dl>
        @endif

        <div class="bungkus-tabel">
            <table>
                <caption>Presensi per pertemuan</caption>
                <thead>
                    <tr><th scope="col">Pertemuan</th><th scope="col">Status Anda</th><th scope="col">Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach ($daftarPertemuan as $pertemuan)
                        @php($sesi = $pertemuan->sesiPresensi)
                        <tr>
                            <td>{{ $pertemuan->nomor }}{{ $pertemuan->topik ? ': '.$pertemuan->topik : '' }}</td>
                            <td>
                                @if (! $sesi)
                                    <span class="kosong">Belum ada sesi</span>
                                @else
                                    {{ $presensiSaya->get($sesi->id)?->status ?? ($sesi->dibuka ? 'Belum check-in' : 'Alpa') }}
                                @endif
                            </td>
                            <td>
                                @if ($sesi && $sesi->dibuka)
                                    <form method="POST" action="{{ route('kelassaya.hadir', $sesi) }}">
                                        @csrf
                                        <button type="submit" class="kecil">Check-in hadir</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section id="nilai" aria-labelledby="judul-nilai">
        <h2 id="judul-nilai">Nilai dan umpan balik</h2>

        <div class="bungkus-tabel" style="margin-bottom:1rem">
            <table>
                <caption>Rincian nilai tugas dan kuis</caption>
                <thead>
                    <tr><th scope="col">Komponen</th><th scope="col">Skor</th><th scope="col">Umpan balik dosen</th></tr>
                </thead>
                <tbody>
                    @foreach ($daftarTugas as $tugas)
                        @php($kumpul = $pengumpulanSaya->get($tugas->id))
                        <tr>
                            <td>Tugas: {{ $tugas->judul }}</td>
                            <td>{{ $kumpul?->skor ?? '-' }}</td>
                            <td>{{ $kumpul?->umpan_balik ?? '-' }}</td>
                        </tr>
                    @endforeach
                    @foreach ($daftarKuis as $kuis)
                        @php($percobaan = $percobaanSaya->get($kuis->id))
                        <tr>
                            <td>Kuis: {{ $kuis->judul }}</td>
                            <td>{{ $percobaan?->skor ?? '-' }}</td>
                            <td>{{ $percobaan?->umpan_balik ?? '-' }}</td>
                        </tr>
                    @endforeach
                    @if ($daftarTugas->isEmpty() && $daftarKuis->isEmpty())
                        <tr><td colspan="3" class="kosong">Belum ada tugas atau kuis.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if ($rekapNilai)
            <dl class="ringkasan">
                <div class="kartu"><dt>Tugas dan kuis (bobot {{ $kelas->bobot_tugas }}%)</dt><dd>{{ $rekapNilai['tugas'] ?? '-' }}</dd></div>
                <div class="kartu"><dt>UTS (bobot {{ $kelas->bobot_uts }}%)</dt><dd>{{ $rekapNilai['uts'] ?? '-' }}</dd></div>
                <div class="kartu"><dt>UAS (bobot {{ $kelas->bobot_uas }}%)</dt><dd>{{ $rekapNilai['uas'] ?? '-' }}</dd></div>
                <div class="kartu"><dt>Nilai akhir</dt><dd>{{ $rekapNilai['akhir'] }}</dd></div>
                <div class="kartu"><dt>Huruf</dt><dd>{{ $rekapNilai['huruf'] }}</dd></div>
            </dl>
            <p class="petunjuk" style="margin-top:.75rem">Komponen yang belum dinilai dihitung 0 pada nilai akhir sementara ini.</p>
            @if ($rekapNilai['umpan_balik'])
                <div class="kartu" style="margin-top:1rem">
                    <h3>Catatan dosen</h3>
                    <p class="isi-teks">{{ $rekapNilai['umpan_balik'] }}</p>
                </div>
            @endif
        @endif
    </section>
@endsection
