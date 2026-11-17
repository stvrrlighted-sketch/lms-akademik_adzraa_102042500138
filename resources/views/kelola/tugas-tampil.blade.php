@extends('tata-letak')

@section('judul', $tugas->judul)

@section('isi')
    <div class="kepala">
        <div>
            <h1>{{ $tugas->judul }}</h1>
            <p>{{ $kelas->label() }}. Tenggat {{ $tugas->tenggat->format('d/m/Y H:i') }}</p>
        </div>
        <a class="tombol sekunder" href="{{ route('tugas.indeks', $kelas) }}">Kembali ke daftar tugas</a>
    </div>

    <section class="kartu" aria-labelledby="judul-instruksi">
        <h2 id="judul-instruksi">Instruksi</h2>
        <p class="isi-teks">{{ $tugas->instruksi }}</p>
    </section>

    @if ($errors->any())
        <div class="info bahaya" role="alert">
            <ul style="margin:0;padding-left:1.25rem">
                @foreach (array_unique($errors->all()) as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tugas.nilai', $tugas) }}" novalidate>
        @csrf
        <div class="bungkus-tabel" style="margin-bottom:1rem">
            <table>
                <caption>Pengumpulan tugas</caption>
                <thead>
                    <tr>
                        <th scope="col">Mahasiswa</th>
                        <th scope="col">Status</th>
                        <th scope="col">Berkas</th>
                        <th scope="col">Skor (0-100)</th>
                        <th scope="col">Umpan balik</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarMahasiswa as $mahasiswa)
                        @php($kumpul = $pengumpulan->get($mahasiswa->id))
                        <tr>
                            <td>{{ $mahasiswa->name }}</td>
                            @if ($kumpul)
                                <td>
                                    <span class="lencana {{ $kumpul->status === 'Terlambat' ? 'peringatan' : 'ok' }}">{{ $kumpul->status }}</span>
                                    <br><span class="teks-kecil redup">{{ $kumpul->dikumpulkan_pada->format('d/m/Y H:i') }}</span>
                                </td>
                                <td><a href="{{ route('unduh.pengumpulan', $kumpul) }}">{{ $kumpul->nama_asli }}</a></td>
                                <td>
                                    <input type="number" min="0" max="100" inputmode="numeric" style="width:6rem"
                                           name="nilai[{{ $kumpul->id }}][skor]" value="{{ old('nilai.'.$kumpul->id.'.skor', $kumpul->skor) }}"
                                           aria-label="Skor {{ $mahasiswa->name }}">
                                </td>
                                <td>
                                    <input type="text" maxlength="1000" style="min-width:14rem"
                                           name="nilai[{{ $kumpul->id }}][umpan_balik]" value="{{ old('nilai.'.$kumpul->id.'.umpan_balik', $kumpul->umpan_balik) }}"
                                           aria-label="Umpan balik {{ $mahasiswa->name }}">
                                </td>
                            @else
                                <td><span class="lencana galat">Belum mengumpulkan</span></td>
                                <td colspan="3" class="kosong">-</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit">Simpan nilai dan umpan balik</button>
    </form>
@endsection
