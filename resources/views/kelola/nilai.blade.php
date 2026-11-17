@extends('tata-letak')

@section('judul', 'Nilai '.$kelas->mataKuliah->kode)

@section('isi')
    <div class="kepala">
        <div>
            <h1>Nilai</h1>
            <p>{{ $kelas->label() }}</p>
        </div>
        <a class="tombol sekunder" href="{{ route('nilai.ekspor', $kelas) }}">Ekspor rekap (CSV)</a>
    </div>

    @include('partials.menu-kelas', ['kelas' => $kelas, 'aktif' => 'nilai'])

    <form class="formulir" method="POST" action="{{ route('nilai.bobot', $kelas) }}" novalidate>
        @csrf
        @method('PUT')
        <h2>Bobot komponen (total 100)</h2>
        <div class="dua-kolom">
            <div class="kolom">
                <label for="bobot_tugas">Tugas dan kuis</label>
                <input id="bobot_tugas" name="bobot_tugas" type="number" min="0" max="100" value="{{ old('bobot_tugas', $kelas->bobot_tugas) }}" required
                       @error('bobot_tugas') aria-invalid="true" aria-describedby="galat-bobot_tugas" @enderror>
                @include('partials.galat', ['nama' => 'bobot_tugas'])
            </div>
            <div class="kolom">
                <label for="bobot_uts">UTS</label>
                <input id="bobot_uts" name="bobot_uts" type="number" min="0" max="100" value="{{ old('bobot_uts', $kelas->bobot_uts) }}" required
                       @error('bobot_uts') aria-invalid="true" aria-describedby="galat-bobot_uts" @enderror>
                @include('partials.galat', ['nama' => 'bobot_uts'])
            </div>
            <div class="kolom">
                <label for="bobot_uas">UAS</label>
                <input id="bobot_uas" name="bobot_uas" type="number" min="0" max="100" value="{{ old('bobot_uas', $kelas->bobot_uas) }}" required
                       @error('bobot_uas') aria-invalid="true" aria-describedby="galat-bobot_uas" @enderror>
                @include('partials.galat', ['nama' => 'bobot_uas'])
            </div>
        </div>
        <button type="submit">Simpan bobot</button>
    </form>

    <section aria-labelledby="judul-rekap">
        <h2 id="judul-rekap">Rekap nilai</h2>
        <p class="petunjuk">Nilai tugas dan kuis adalah rata-rata semua tugas dan kuis kelas ini; yang belum dikumpulkan atau belum dinilai dihitung 0. Skor tugas dan kuis diisi pada halaman masing-masing.</p>
        <div class="aksi" style="margin-bottom:1rem">
            <span class="petunjuk" style="margin:0">Huruf:</span>
            @foreach (\App\Services\PerhitunganNilai::BATAS_HURUF as [$huruf, $batas])
                <span class="lencana">{{ $huruf }} &ge; {{ $batas }}</span>
            @endforeach
            <span class="lencana">E &lt; {{ \Illuminate\Support\Arr::last(\App\Services\PerhitunganNilai::BATAS_HURUF)[1] }}</span>
        </div>

        @if ($errors->any() && ! $errors->hasAny(['bobot_tugas', 'bobot_uts', 'bobot_uas']))
            <div class="info bahaya" role="alert">
                <ul style="margin:0;padding-left:1.25rem">
                    @foreach (array_unique($errors->all()) as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($daftarRekap->isEmpty())
            <p class="kartu kosong">Belum ada mahasiswa di kelas ini.</p>
        @else
            <form method="POST" action="{{ route('nilai.simpan', $kelas) }}" novalidate>
                @csrf
                <div class="bungkus-tabel" style="margin-bottom:1rem">
                    <table>
                        <caption>Rekap nilai mahasiswa</caption>
                        <thead>
                            <tr>
                                <th scope="col">Mahasiswa</th>
                                <th scope="col">Tugas dan kuis</th>
                                <th scope="col">UTS</th>
                                <th scope="col">UAS</th>
                                <th scope="col">Nilai akhir</th>
                                <th scope="col">Huruf</th>
                                <th scope="col">Umpan balik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftarRekap as $baris)
                                @php($id = $baris['mahasiswa']->id)
                                <tr>
                                    <td>{{ $baris['mahasiswa']->name }}</td>
                                    <td>{{ $baris['tugas'] ?? '-' }}</td>
                                    <td><input type="number" min="0" max="100" inputmode="numeric" style="width:5.5rem"
                                               name="nilai[{{ $id }}][uts]" value="{{ old('nilai.'.$id.'.uts', $baris['uts']) }}" aria-label="UTS {{ $baris['mahasiswa']->name }}"></td>
                                    <td><input type="number" min="0" max="100" inputmode="numeric" style="width:5.5rem"
                                               name="nilai[{{ $id }}][uas]" value="{{ old('nilai.'.$id.'.uas', $baris['uas']) }}" aria-label="UAS {{ $baris['mahasiswa']->name }}"></td>
                                    <td><strong>{{ $baris['akhir'] }}</strong></td>
                                    <td><span class="lencana">{{ $baris['huruf'] }}</span></td>
                                    <td><input type="text" maxlength="1000" style="min-width:14rem"
                                               name="nilai[{{ $id }}][umpan_balik]" value="{{ old('nilai.'.$id.'.umpan_balik', $baris['umpan_balik']) }}" aria-label="Umpan balik {{ $baris['mahasiswa']->name }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="submit">Simpan UTS, UAS, dan umpan balik</button>
            </form>
        @endif
    </section>
@endsection
