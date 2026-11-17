@extends('tata-letak-publik')

@section('judul', 'Masuk')
@section('kelas-body', 'latar-masuk')

@push('gaya')
        body.latar-masuk { background: transparent; }
        body.latar-masuk::before {
            content: ""; position: fixed; inset: -1rem; z-index: -2;
            background: url('{{ asset('images/gedung-telkom.webp') }}') center / cover no-repeat;
            filter: blur(3px) saturate(1.05);
        }
        body.latar-masuk::after {
            content: ""; position: fixed; inset: 0; z-index: -1;
            background: linear-gradient(180deg, color-mix(in srgb, var(--dasar) 28%, transparent), color-mix(in srgb, var(--dasar) 58%, transparent));
        }
        .latar-masuk .kaki-situs { color: var(--teks); }

        .masuk-kotak.masuk-kaca {
            width: min(22rem, 100%); padding: 1.75rem 1.75rem 1.5rem; overflow: visible;
            background: color-mix(in srgb, var(--dasar) 50%, transparent);
            border: 1px solid color-mix(in srgb, #ffffff 55%, transparent);
            border-radius: 1.25rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55), 0 28px 60px -24px rgba(0, 0, 0, 0.55);
            -webkit-backdrop-filter: blur(22px) saturate(160%); backdrop-filter: blur(22px) saturate(160%);
        }
        .masuk-kaca h1 { margin: 0 0 1.25rem; font-size: 1.375rem; }
        .masuk-kaca .medan { position: relative; margin-bottom: 1rem; }
        .masuk-kaca .sr-saja { position: absolute; width: 1px; height: 1px; overflow: hidden; clip-path: inset(50%); white-space: nowrap; }
        .masuk-kaca input {
            min-height: 2.75rem; padding: 0.5rem 2.5rem 0.5rem 0;
            border: 0; border-bottom: 0.125rem solid var(--teks); border-radius: 0; background: transparent;
        }
        .masuk-kaca input::placeholder { color: var(--teks-redup); opacity: 1; }
        .masuk-kaca input:focus-visible { border-bottom-color: var(--merah); box-shadow: 0 0.125rem 0 0 var(--merah); }
        .masuk-kaca input[aria-invalid="true"] { border-bottom-color: var(--merah); }
        .masuk-kaca .ikon-medan, .masuk-kaca button.mata {
            position: absolute; right: 0; top: 0.25rem; display: grid; place-items: center; width: 2.25rem; height: 2.25rem;
            color: var(--teks);
        }
        .masuk-kaca .ikon-medan { pointer-events: none; }
        .masuk-kaca .ikon-medan .ikon, .masuk-kaca button.mata .ikon { width: 1.25rem; height: 1.25rem; }
        .masuk-kaca button.mata {
            min-height: 0; padding: 0; border: 0; border-radius: 50%; background: transparent; background-image: none; box-shadow: none;
        }
        .masuk-kaca button.mata:hover { background: var(--hover); transform: none; }
        .masuk-kaca .mata-tutup { display: none; }
        .masuk-kaca button.mata[aria-pressed="true"] .mata-buka { display: none; }
        .masuk-kaca button.mata[aria-pressed="true"] .mata-tutup { display: grid; }
        .masuk-kaca .lupa { display: inline-block; margin-top: 0.25rem; color: var(--teks); font-size: 0.9375rem; text-decoration: underline; text-underline-offset: 0.2em; }
        .masuk-kotak.masuk-kaca button[type="submit"] { display: flex; width: auto; min-width: 9rem; margin: 1.25rem auto 0; }
        @media (prefers-reduced-transparency: reduce) {
            .masuk-kotak.masuk-kaca { background: var(--kaca-kuat); -webkit-backdrop-filter: none; backdrop-filter: none; }
        }
@endpush

@section('isi')
    <div class="masuk-kotak masuk-kaca">
        <h1>Masuk</h1>

        <form method="POST" action="{{ route('login.proses') }}" novalidate>
            @csrf

            <div class="medan">
                <label class="sr-saja" for="email">Email</label>
                <input id="email" name="email" type="email" placeholder="Email" value="{{ old('email') }}"
                       autocomplete="username" autofocus required
                       @error('email') aria-invalid="true" aria-describedby="galat-email" @enderror>
                <span class="ikon-medan">@include('partials.ikon', ['nama' => 'envelope-simple'])</span>
                @include('partials.galat', ['nama' => 'email'])
            </div>

            <div class="medan">
                <label class="sr-saja" for="password">Kata sandi</label>
                <input id="password" name="password" type="password" placeholder="Kata sandi"
                       autocomplete="current-password" required
                       @error('password') aria-invalid="true" aria-describedby="galat-password" @enderror>
                <button type="button" class="mata" id="tampil-sandi" aria-pressed="false" aria-label="Tampilkan kata sandi">
                    <span class="mata-buka">@include('partials.ikon', ['nama' => 'eye'])</span>
                    <span class="mata-tutup">@include('partials.ikon', ['nama' => 'eye-slash'])</span>
                </button>
                @include('partials.galat', ['nama' => 'password'])
            </div>

            <a class="lupa" href="{{ route('lupa.form') }}">Lupa kata sandi?</a>

            <button type="submit">Masuk</button>
        </form>
    </div>
@endsection

@push('skrip')
    <script>
        (function () {
            var tombol = document.getElementById('tampil-sandi');
            var isian = document.getElementById('password');
            if (!tombol || !isian) { return; }
            tombol.addEventListener('click', function () {
                var tampil = isian.type === 'password';
                isian.type = tampil ? 'text' : 'password';
                tombol.setAttribute('aria-pressed', tampil ? 'true' : 'false');
                tombol.setAttribute('aria-label', tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        })();
    </script>
@endpush
