@extends('tata-letak')

@section('judul', 'Materi '.$kelas->mataKuliah->kode)

@section('isi')
    <div class="kepala">
        <div>
            <h1>Materi</h1>
            <p>{{ $kelas->label() }}</p>
        </div>
    </div>

    @include('partials.menu-kelas', ['kelas' => $kelas, 'aktif' => 'materi'])

    @if ($errors->any())
        <div class="info bahaya" role="alert">
            <ul style="margin:0;padding-left:1.25rem">
                @foreach (array_unique($errors->all()) as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @foreach ($daftarPertemuan as $pertemuan)
        <details class="kartu akordeon" @if ($pertemuan->materi->isNotEmpty()) open @endif>
            <summary>
                Pertemuan {{ $pertemuan->nomor }}: {{ $pertemuan->topik ?: 'Belum ada topik' }}
                <span class="lencana">{{ $pertemuan->materi->count() }} materi</span>
            </summary>

            <form method="POST" action="{{ route('pertemuan.perbarui', $pertemuan) }}" class="kolom" style="margin-top:1rem" novalidate>
                @csrf
                @method('PUT')
                <label for="topik-{{ $pertemuan->id }}">Topik pertemuan {{ $pertemuan->nomor }}</label>
                <div class="aksi">
                    <input id="topik-{{ $pertemuan->id }}" name="topik" type="text" value="{{ $pertemuan->topik }}" maxlength="200" style="max-width:28rem">
                    <button type="submit" class="sekunder kecil">Simpan topik</button>
                </div>
            </form>

            @if ($pertemuan->materi->isNotEmpty())
                <ul style="list-style:none;margin:0 0 1rem;padding:0;display:grid;gap:.5rem">
                    @foreach ($pertemuan->materi as $materi)
                        <li class="aksi">
                            <span class="lencana">{{ $materi->jenis === 'berkas' ? 'Berkas' : 'Tautan' }}</span>
                            <strong>{{ $materi->judul }}</strong>
                            @if ($materi->jenis === 'berkas')
                                <a href="{{ route('unduh.materi', $materi) }}">{{ $materi->nama_asli }}</a>
                            @else
                                <a href="{{ $materi->url }}" target="_blank" rel="noopener noreferrer">{{ $materi->url }}</a>
                            @endif
                            <form method="POST" action="{{ route('materi.hapus', $materi) }}" data-konfirmasi="Hapus materi {{ $materi->judul }}?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bahaya kecil">Hapus</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('materi.tambah', $pertemuan) }}" enctype="multipart/form-data" novalidate>
                @csrf
                <h3>Tambah materi</h3>
                <div class="dua-kolom">
                    <div class="kolom">
                        <label for="judul-{{ $pertemuan->id }}">Judul materi</label>
                        <input id="judul-{{ $pertemuan->id }}" name="judul" type="text" maxlength="150" required>
                    </div>
                    <div class="kolom">
                        <label for="berkas-{{ $pertemuan->id }}">Berkas (PDF, PPTX, DOCX)</label>
                        <input id="berkas-{{ $pertemuan->id }}" name="berkas" type="file" accept=".pdf,.pptx,.docx">
                    </div>
                    <div class="kolom">
                        <label for="url-{{ $pertemuan->id }}">Tautan referensi</label>
                        <input id="url-{{ $pertemuan->id }}" name="url" type="url" placeholder="https://" maxlength="500">
                    </div>
                </div>
                <p class="petunjuk">Isi berkas, tautan, atau keduanya. Ukuran berkas maksimal 10 MB.</p>
                <button type="submit">Tambah materi</button>
            </form>
        </details>
    @endforeach
@endsection
