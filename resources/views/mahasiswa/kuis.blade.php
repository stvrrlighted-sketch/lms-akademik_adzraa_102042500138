@extends('tata-letak')

@section('judul', $kuis->judul)

@section('isi')
    <div class="kepala">
        <div>
            <h1>{{ $kuis->judul }}</h1>
            <p>{{ $kelas->label() }}. {{ $kuis->tenggat ? 'Tenggat '.$kuis->tenggat->format('d/m/Y H:i') : 'Tanpa tenggat' }}</p>
        </div>
        <a class="tombol sekunder" href="{{ route('kelassaya.tampil', $kelas) }}#kuis">Kembali ke kelas</a>
    </div>

    @if ($percobaan)
        <section class="kartu" aria-labelledby="judul-hasil">
            <h2 id="judul-hasil">Hasil Anda</h2>
            <p>Dikirim {{ $percobaan->selesai_pada->format('d/m/Y H:i') }}.</p>
            @if ($percobaan->skor === null)
                <p><span class="lencana peringatan">Menunggu penilaian dosen</span></p>
            @else
                <p><strong>Skor: {{ $percobaan->skor }}</strong></p>
            @endif
            @if ($percobaan->umpan_balik)
                <p>Umpan balik dosen:</p>
                <p class="isi-teks">{{ $percobaan->umpan_balik }}</p>
            @endif
        </section>
    @elseif ($ditutup)
        <p class="info bahaya" role="status">Kuis ini sudah ditutup dan Anda belum mengerjakannya.</p>
    @elseif ($daftarSoal->isEmpty())
        <p class="kartu kosong">Kuis ini belum memiliki soal.</p>
    @else
        <form class="formulir" method="POST" action="{{ route('kelassaya.kerjakan', $kuis) }}" novalidate
              data-konfirmasi="Kirim jawaban sekarang? Kuis hanya dapat dikerjakan satu kali.">
            @csrf

            @foreach ($daftarSoal as $soal)
                @if ($soal->jenis === 'pilihan_ganda')
                    <fieldset>
                        <legend>{{ $loop->iteration }}. {{ $soal->pertanyaan }}</legend>
                        @foreach ($soal->opsi as $huruf => $teks)
                            <label class="sebaris">
                                <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $huruf }}">
                                {{ $huruf }}. {{ $teks }}
                            </label>
                        @endforeach
                    </fieldset>
                @else
                    <div class="kolom">
                        <label for="jawaban-{{ $soal->id }}">{{ $loop->iteration }}. {{ $soal->pertanyaan }}</label>
                        <textarea id="jawaban-{{ $soal->id }}" name="jawaban[{{ $soal->id }}]" maxlength="2000"></textarea>
                    </div>
                @endif
            @endforeach

            <button type="submit">Kirim jawaban</button>
        </form>
    @endif
@endsection
