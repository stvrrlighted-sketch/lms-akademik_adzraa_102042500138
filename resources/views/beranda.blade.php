@extends('tata-letak-publik')

@section('judul', 'Beranda')

@section('isi')
    <div class="beranda">
        <section class="hero" aria-labelledby="judul-hero">
            <div class="hero-teks">
                <small>Learning Management System</small>
                <h1 id="judul-hero">{{ config('app.name') }}</h1>
            </div>

            <img class="hero-logo" src="{{ asset('images/logo-telkom.png') }}" width="512" height="184" alt="" aria-hidden="true">
        </section>

        <a class="tombol garis" href="{{ route('login') }}">
            @include('partials.ikon', ['nama' => 'lock-simple'])
            Masuk ke LMS
        </a>

        <p class="beranda-catatan">Gunakan akun akademik Anda. Mengalami kendala pada akun? Hubungi admin.</p>
    </div>
@endsection
