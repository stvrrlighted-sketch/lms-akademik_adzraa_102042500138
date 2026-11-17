@extends('tata-letak')

@section('judul', $kuis->judul)

@section('isi')
    <div class="kepala">
        <div>
            <h1>{{ $kuis->judul }}</h1>
            <p>{{ $kelas->label() }}. {{ $kuis->tenggat ? 'Tenggat '.$kuis->tenggat->format('d/m/Y H:i') : 'Tanpa tenggat' }}</p>
        </div>
        <a class="tombol sekunder" href="{{ route('kuis.indeks', $kelas) }}">Kembali ke daftar kuis</a>
    </div>

    @if ($errors->any())
        <div class="info bahaya" role="alert">
            <ul style="margin:0;padding-left:1.25rem">
                @foreach (array_unique($errors->all()) as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section aria-labelledby="judul-soal">
        <h2 id="judul-soal">Soal ({{ $daftarSoal->count() }})</h2>

        @if ($daftarSoal->isEmpty())
            <p class="kartu kosong">Belum ada soal.</p>
        @else
            <ol style="list-style:none;margin:0 0 1rem;padding:0;display:grid;gap:.75rem">
                @foreach ($daftarSoal as $soal)
                    <li class="kartu">
                        <p><strong>{{ $loop->iteration }}.</strong> {{ $soal->pertanyaan }}
                            <span class="lencana">{{ $soal->jenis === 'pilihan_ganda' ? 'Pilihan ganda' : 'Esai singkat' }}</span></p>
                        @if ($soal->jenis === 'pilihan_ganda')
                            <ul style="margin:0 0 .75rem;padding-left:1.25rem">
                                @foreach ($soal->opsi as $huruf => $teks)
                                    <li>{{ $huruf }}. {{ $teks }} @if ($huruf === $soal->kunci) <span class="lencana ok">Kunci</span> @endif</li>
                                @endforeach
                            </ul>
                        @endif
                        <form method="POST" action="{{ route('kuis.soal.hapus', $soal) }}" data-konfirmasi="Hapus soal ini?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bahaya kecil">Hapus soal</button>
                        </form>
                    </li>
                @endforeach
            </ol>
        @endif

        <form class="formulir" method="POST" action="{{ route('kuis.soal.tambah', $kuis) }}" novalidate>
            @csrf
            <h3>Tambah soal</h3>
            <div class="kolom">
                <label for="jenis">Jenis soal</label>
                <select id="jenis" name="jenis">
                    <option value="pilihan_ganda" @selected(old('jenis', 'pilihan_ganda') === 'pilihan_ganda')>Pilihan ganda</option>
                    <option value="esai" @selected(old('jenis') === 'esai')>Esai singkat</option>
                </select>
            </div>
            <div class="kolom">
                <label for="pertanyaan">Pertanyaan</label>
                <textarea id="pertanyaan" name="pertanyaan" style="min-height:5rem" required>{{ old('pertanyaan') }}</textarea>
            </div>
            <p class="petunjuk">Untuk pilihan ganda, isi keempat opsi dan pilih kunci. Untuk esai, kosongkan opsi.</p>
            <div class="dua-kolom">
                @foreach (['A', 'B', 'C', 'D'] as $huruf)
                    <div class="kolom">
                        <label for="opsi-{{ $huruf }}">Opsi {{ $huruf }}</label>
                        <input id="opsi-{{ $huruf }}" name="opsi[{{ $huruf }}]" type="text" maxlength="255" value="{{ old('opsi.'.$huruf) }}">
                    </div>
                @endforeach
            </div>
            <div class="kolom" style="max-width:12rem">
                <label for="kunci">Kunci jawaban</label>
                <select id="kunci" name="kunci">
                    @foreach (['A', 'B', 'C', 'D'] as $huruf)
                        <option value="{{ $huruf }}" @selected(old('kunci') === $huruf)>{{ $huruf }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit">Tambah soal</button>
        </form>
    </section>

    <section aria-labelledby="judul-hasil">
        <h2 id="judul-hasil">Hasil mahasiswa ({{ $daftarPercobaan->count() }})</h2>
        <p class="petunjuk">Kuis tanpa soal esai dinilai otomatis. Jika ada soal esai, skor diisi manual. Skor yang Anda isi di sini menggantikan skor otomatis.</p>

        @if ($daftarPercobaan->isEmpty())
            <p class="kartu kosong">Belum ada yang mengerjakan.</p>
        @else
            <form method="POST" action="{{ route('kuis.nilai', $kuis) }}" novalidate>
                @csrf
                <div style="display:grid;gap:.75rem;margin-bottom:1rem">
                    @foreach ($daftarPercobaan as $percobaan)
                        <div class="kartu">
                            <h3>{{ $percobaan->mahasiswa->name }}
                                <span class="lencana {{ $percobaan->skor === null ? 'peringatan' : 'ok' }}">{{ $percobaan->skor === null ? 'Menunggu nilai' : 'Skor '.$percobaan->skor }}</span>
                            </h3>
                            <details class="akordeon" style="margin-bottom:.75rem">
                                <summary>Lihat jawaban</summary>
                                <ol>
                                    @foreach ($daftarSoal as $soal)
                                        @php($jawab = $percobaan->jawaban[$soal->id] ?? null)
                                        <li>
                                            <p>{{ $soal->pertanyaan }}</p>
                                            @if ($soal->jenis === 'pilihan_ganda')
                                                <p>Jawaban: {{ $jawab ?? 'kosong' }} (kunci {{ $soal->kunci }}) {{ $jawab === $soal->kunci ? 'benar' : 'salah' }}</p>
                                            @else
                                                <p class="isi-teks">{{ $jawab ?: 'kosong' }}</p>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </details>
                            <div class="dua-kolom">
                                <div class="kolom">
                                    <label for="skor-{{ $percobaan->id }}">Skor (0-100)</label>
                                    <input id="skor-{{ $percobaan->id }}" type="number" min="0" max="100" inputmode="numeric"
                                           name="percobaan[{{ $percobaan->id }}][skor]" value="{{ old('percobaan.'.$percobaan->id.'.skor', $percobaan->skor) }}">
                                </div>
                                <div class="kolom">
                                    <label for="umpan-{{ $percobaan->id }}">Umpan balik</label>
                                    <input id="umpan-{{ $percobaan->id }}" type="text" maxlength="1000"
                                           name="percobaan[{{ $percobaan->id }}][umpan_balik]" value="{{ old('percobaan.'.$percobaan->id.'.umpan_balik', $percobaan->umpan_balik) }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="submit">Simpan nilai kuis</button>
            </form>
        @endif
    </section>
@endsection
