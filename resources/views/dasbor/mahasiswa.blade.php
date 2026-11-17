@extends('tata-letak')

@section('judul', 'Dasbor Mahasiswa')

@section('isi')
    <div class="kepala"><h1>Dasbor Mahasiswa</h1></div>

    @include('partials.berita-dasbor')

    <section aria-labelledby="judul-pengingat">
        <h2 id="judul-pengingat">Pengingat tenggat tugas</h2>

        @if ($tugasMendatang->isEmpty())
            <p class="kartu kosong">Tidak ada tugas dengan tenggat mendatang yang belum dikumpulkan.</p>
        @else
            <div class="bungkus-tabel">
                <table>
                    <caption>Tugas yang belum dikumpulkan</caption>
                    <thead>
                        <tr><th scope="col">Tugas</th><th scope="col">Mata kuliah</th><th scope="col">Tenggat</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($tugasMendatang as $tugas)
                            <tr>
                                <td><a href="{{ route('kelassaya.tugas', $tugas) }}">{{ $tugas->judul }}</a></td>
                                <td>{{ $tugas->kelas->mataKuliah->nama }}</td>
                                <td>{{ $tugas->tenggat->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section aria-labelledby="judul-kelas">
        <h2 id="judul-kelas">Mata kuliah yang diikuti</h2>

        @if ($daftarKelas->isEmpty())
            <p class="kartu kosong">Anda belum terdaftar di kelas mana pun. Hubungi admin.</p>
        @else
            <div class="grid-kartu">
                @foreach ($daftarKelas as $kelas)
                    <a class="kartu interaktif" href="{{ route('kelassaya.tampil', $kelas) }}" style="text-decoration:none;color:inherit">
                        <h3>{{ $kelas->mataKuliah->nama }}</h3>
                        <p class="redup teks-kecil">{{ $kelas->mataKuliah->kode }}, Kelas {{ $kelas->nama }}, {{ $kelas->mataKuliah->sks }} SKS</p>
                        <p class="teks-kecil">Dosen: {{ $kelas->dosen->name }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection
