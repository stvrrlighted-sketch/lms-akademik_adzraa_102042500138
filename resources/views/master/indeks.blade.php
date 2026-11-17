@extends('tata-letak')

@section('judul', $definisi['judul'])

@section('isi')
    <div class="kepala">
        <h1>{{ $definisi['judul'] }}</h1>
        <a class="tombol" href="{{ route('master.buat', $jenis) }}">Tambah {{ strtolower($definisi['judul']) }}</a>
    </div>

    @if ($daftar->isEmpty())
        <p class="kartu kosong">Belum ada data.</p>
    @else
        <div class="bungkus-tabel">
            <table>
                <caption>Daftar {{ strtolower($definisi['judul']) }}</caption>
                <thead>
                    <tr>
                        @foreach ($definisi['kolom'] as $kolom)
                            <th scope="col">{{ $kolom['label'] }}</th>
                        @endforeach
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftar as $item)
                        <tr>
                            @foreach ($definisi['kolom'] as $kolom)
                                <td>
                                    @if ($kolom['tipe'] === 'centang')
                                        @if ($item->{$kolom['nama']}) <span class="lencana ok">Aktif</span> @else <span class="lencana">Tidak</span> @endif
                                    @elseif ($kolom['tipe'] === 'pilih')
                                        {{ $item->{$kolom['relasi']}?->nama }}
                                    @else
                                        {{ $item->{$kolom['nama']} }}
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                <div class="aksi">
                                    <a class="tombol sekunder kecil" href="{{ route('master.ubah', [$jenis, $item->id]) }}">Ubah</a>
                                    <form method="POST" action="{{ route('master.hapus', [$jenis, $item->id]) }}" data-konfirmasi="Hapus data ini?">
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
