@php($bahasa = app()->getLocale())
@php($bahasaLain = $bahasa === 'id' ? 'en' : 'id')
@php($namaBahasaLain = $bahasa === 'id' ? 'English' : 'Bahasa Indonesia')

<header class="navbar-atas">
    <a class="merek" href="{{ $publik ? url('/') : route('dashboard') }}">
        <img class="merek-tanda" src="{{ asset('images/logo-telkom-lambang.png') }}" width="137" height="189" alt="">
        <span class="merek-teks">{{ config('app.name') }}</span>
    </a>

    <nav class="menu-utama" aria-label="Menu utama">
        <ul>
            @foreach ($menu as [$label, $url, $pola])
                <li>
                    <a href="{{ $url }}" @if (request()->is(...$pola)) aria-current="page" @endif>{{ $label }}</a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="alat">
        <a class="tombol-ikon" href="{{ route('bahasa.ganti', $bahasaLain) }}" hreflang="{{ $bahasaLain }}"
           aria-label="Ganti bahasa: {{ $namaBahasaLain }}">{{ strtoupper($bahasa) }}</a>

        <button type="button" class="tombol-ikon" id="ganti-tema" aria-label="Tema: Sistem">
            <span class="tema-sistem">@include('partials.ikon', ['nama' => 'desktop'])</span>
            <span class="tema-terang">@include('partials.ikon', ['nama' => 'sun'])</span>
            <span class="tema-gelap">@include('partials.ikon', ['nama' => 'moon'])</span>
        </button>

        @unless ($publik)
            <span class="tombol-ikon avatar-tombol" role="img" aria-label="{{ auth()->user()->name }}">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="tombol-keluar">Keluar</button>
            </form>
        @endunless
    </div>
</header>


<span hidden id="teks-tema-sistem">Tema: Sistem</span>
<span hidden id="teks-tema-light">Tema: Terang</span>
<span hidden id="teks-tema-dark">Tema: Gelap</span>
