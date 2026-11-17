@extends('tata-letak-publik')

@section('judul', 'Lupa kata sandi')

@section('isi')
    <div class="kartu masuk-kotak">
        <h1>Lupa kata sandi</h1>
        <p class="redup">Masukkan email akun Anda. Permintaan diteruskan ke admin.</p>

        @if (session('sukses'))
            <p class="info" role="status">{{ session('sukses') }}</p>
        @endif

        <form method="POST" action="{{ route('lupa.kirim') }}" novalidate>
            @csrf

            <div class="kolom">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       autocomplete="username" autofocus required
                       @error('email') aria-invalid="true" aria-describedby="galat-email" @enderror>
                @include('partials.galat', ['nama' => 'email'])
            </div>

            <button type="submit">Kirim permintaan</button>
        </form>

        <p class="tautan-bawah">
            <a href="{{ route('login') }}">Kembali ke halaman masuk</a>
        </p>
    </div>
@endsection
