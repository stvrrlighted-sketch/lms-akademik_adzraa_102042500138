@extends('tata-letak')

@section('judul', $tugas->judul)

@section('isi')
    <div class="kepala">
        <div>
            <h1>{{ $tugas->judul }}</h1>
            <p>{{ $kelas->label() }}. Tenggat {{ $tugas->tenggat->format('d/m/Y H:i') }}</p>
        </div>
        <a class="tombol sekunder" href="{{ route('kelassaya.tampil', $kelas) }}#tugas">Kembali ke kelas</a>
    </div>

    <section class="kartu" aria-labelledby="judul-instruksi">
        <h2 id="judul-instruksi">Instruksi</h2>
        <p class="isi-teks">{{ $tugas->instruksi }}</p>
    </section>

    <section class="kartu" aria-labelledby="judul-status">
        <h2 id="judul-status">Pengumpulan Anda</h2>

        @if ($pengumpulan)
            <p>
                <span class="lencana {{ $pengumpulan->status === 'Terlambat' ? 'peringatan' : 'ok' }}">{{ $pengumpulan->status }}</span>
                pada {{ $pengumpulan->dikumpulkan_pada->format('d/m/Y H:i') }}
            </p>
            <p>Berkas: <a href="{{ route('unduh.pengumpulan', $pengumpulan) }}">{{ $pengumpulan->nama_asli }}</a></p>
            @if ($pengumpulan->skor !== null)
                <p><strong>Skor: {{ $pengumpulan->skor }}</strong></p>
            @endif
            @if ($pengumpulan->umpan_balik)
                <p>Umpan balik dosen:</p>
                <p class="isi-teks">{{ $pengumpulan->umpan_balik }}</p>
            @endif
        @else
            <p>Anda belum mengumpulkan tugas ini.
                @if ($tugas->tenggat->isPast())
                    <span class="lencana peringatan">Tenggat sudah lewat, pengumpulan akan ditandai Terlambat</span>
                @endif
            </p>
        @endif
    </section>

    @if (! $pengumpulan || $pengumpulan->skor === null)
        <form class="formulir" method="POST" action="{{ route('kelassaya.kumpul', $tugas) }}" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="kolom">
                <label for="berkas">{{ $pengumpulan ? 'Ganti berkas' : 'Unggah berkas' }}</label>
                <input id="berkas" name="berkas" type="file" required accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.txt"
                       @error('berkas') aria-invalid="true" aria-describedby="galat-berkas" @enderror>
                <p class="petunjuk">PDF, DOC, DOCX, PPT, PPTX, ZIP, atau TXT. Maksimal 10 MB. Mengunggah lagi menggantikan berkas sebelumnya sampai tugas dinilai.</p>
                @include('partials.galat', ['nama' => 'berkas'])
            </div>
            <button type="submit">Kumpulkan</button>
        </form>
    @endif
@endsection
