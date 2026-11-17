@extends('tata-letak')

@php($mengubah = $item->exists)

@section('judul', ($mengubah ? 'Ubah ' : 'Tambah ').strtolower($definisi['judul']))

@section('isi')
    <div class="kepala"><h1>{{ $mengubah ? 'Ubah' : 'Tambah' }} {{ strtolower($definisi['judul']) }}</h1></div>

    <form class="formulir" method="POST" novalidate
          action="{{ $mengubah ? route('master.perbarui', [$jenis, $item->id]) : route('master.simpan', $jenis) }}">
        @csrf
        @if ($mengubah)
            @method('PUT')
        @endif

        @foreach ($definisi['kolom'] as $kolom)
            @php($nama = $kolom['nama'])
            <div class="kolom">
                @if ($kolom['tipe'] === 'centang')
                    <label class="sebaris" for="{{ $nama }}">
                        <input id="{{ $nama }}" name="{{ $nama }}" type="checkbox" value="1" @checked(old($nama, $item->{$nama}))>
                        {{ $kolom['label'] }}
                    </label>
                @elseif ($kolom['tipe'] === 'pilih')
                    <label for="{{ $nama }}">{{ $kolom['label'] }}</label>
                    <select id="{{ $nama }}" name="{{ $nama }}"
                            @error($nama) aria-invalid="true" aria-describedby="galat-{{ $nama }}" @enderror>
                        <option value="">(tidak dipilih)</option>
                        @foreach ($kolom['pilihan'] as $nilai => $teks)
                            <option value="{{ $nilai }}" @selected((string) old($nama, $item->{$nama}) === (string) $nilai)>{{ $teks }}</option>
                        @endforeach
                    </select>
                @else
                    <label for="{{ $nama }}">{{ $kolom['label'] }}</label>
                    <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $kolom['tipe'] === 'angka' ? 'number' : 'text' }}"
                           value="{{ old($nama, $item->{$nama}) }}" required
                           @if ($kolom['tipe'] === 'angka') min="1" max="6" inputmode="numeric" @endif
                           @error($nama) aria-invalid="true" aria-describedby="galat-{{ $nama }}" @enderror>
                @endif

                @if (! empty($kolom['petunjuk']))
                    <p class="petunjuk">{{ $kolom['petunjuk'] }}</p>
                @endif
                @include('partials.galat', ['nama' => $nama])
            </div>
        @endforeach

        <div class="aksi">
            <button type="submit">Simpan</button>
            <a class="tombol sekunder" href="{{ route('master.indeks', $jenis) }}">Batal</a>
        </div>
    </form>
@endsection
