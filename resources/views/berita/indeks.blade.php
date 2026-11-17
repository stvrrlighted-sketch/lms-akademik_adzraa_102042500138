@extends('tata-letak')

@section('judul', 'Berita Kampus')

@section('isi')
    <div class="kepala">
        <h1>Berita dan Pengumuman</h1>
        <a class="tombol" href="{{ route('berita.buat') }}">Tulis berita</a>
    </div>

    @if ($daftarBerita->isEmpty())
        <p class="kartu kosong">Belum ada berita.</p>
    @else
        <div class="bungkus-tabel">
            <table>
                <caption>Daftar berita</caption>
                <thead>
                    <tr>
                        <th scope="col">Judul</th>
                        <th scope="col">Tanggal terbit</th>
                        <th scope="col">Kedaluwarsa</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarBerita as $berita)
                        <tr>
                            <td>
                                <div class="aksi">
                                    @if ($berita->adaBanner())
                                        <img src="{{ $berita->urlBanner() }}" alt="" width="64" height="36" style="object-fit:cover;border-radius:.5rem">
                                    @endif
                                    <a href="{{ route('berita.tampil', $berita->id) }}">{{ $berita->judul }}</a>
                                </div>
                            </td>
                            <td>{{ $berita->tanggal_terbit->format('d/m/Y') }}</td>
                            <td>{{ $berita->kedaluwarsa_pada?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                @if ($berita->kedaluwarsa())
                                    <span class="lencana galat">Kedaluwarsa</span>
                                @elseif ($berita->terjadwal())
                                    <span class="lencana">Terjadwal</span>
                                @else
                                    <span class="lencana">Tayang</span>
                                @endif
                            </td>
                            <td>
                                <div class="aksi">
                                    <a class="tombol sekunder kecil" href="{{ route('berita.ubah', $berita) }}">Ubah</a>
                                    <form method="POST" action="{{ route('berita.hapus', $berita) }}" data-konfirmasi="Hapus berita {{ $berita->judul }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bahaya kecil">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
