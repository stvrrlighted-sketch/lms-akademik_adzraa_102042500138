:root {
            color-scheme: light;
            --dasar: #ffffff;
            --teks: #111111;
            --teks-redup: #595959;
            --garis: #767676;
            --garis-halus: rgba(0, 0, 0, 0.1);
            --merah: #d6141c;
            --tombol: #d6141c;
            --tombol-hover: #b9101a;
            --tombol-teks: #ffffff;
            --bar: rgba(255, 255, 255, 0.66);
            --kaca: linear-gradient(155deg, rgba(255, 255, 255, 0.94) 0%, rgba(255, 255, 255, 0.72) 48%, rgba(255, 255, 255, 0.86) 100%);
            --kaca-kuat: rgba(255, 255, 255, 0.96);
            --kaca-garis: rgba(15, 23, 42, 0.11);
            --kilau: inset 0 1px 0 rgba(255, 255, 255, 1), inset 0 0 0 1px rgba(255, 255, 255, 0.7), inset 0 -1px 0 rgba(15, 23, 42, 0.05);
            --bayangan: 0 0 0 0.5px rgba(15, 23, 42, 0.06), 0 1px 1px rgba(15, 23, 42, 0.04), 0 2px 4px rgba(15, 23, 42, 0.04), 0 8px 16px -8px rgba(15, 23, 42, 0.08), 0 16px 32px -20px rgba(15, 23, 42, 0.10);
            --hover: rgba(15, 23, 42, 0.06);
            --input-latar: rgba(255, 255, 255, 0.92);
            --scrim: rgba(15, 23, 42, 0.22);
            --blur: 28px;
            --jenuh: 200%;
            --filter-kartu: blur(var(--blur)) saturate(var(--jenuh));
            --tinggi-bar: 4.75rem;
            --tinggi-bawah: 4.75rem;
        }
        @media (prefers-color-scheme: dark) {
            :root:not([data-tema="light"]) {
                color-scheme: dark;
            --dasar: #000000;
            --teks: #f5f5f5;
            --teks-redup: #a6a6a6;
            --garis: #8a8a8a;
            --garis-halus: rgba(255, 255, 255, 0.14);
            --merah: #ff5a61;
            --tombol: #d6141c;
            --tombol-hover: #e5242c;
            --tombol-teks: #ffffff;
            --bar: rgba(12, 12, 12, 0.66);
            --kaca: linear-gradient(180deg, rgba(255, 255, 255, 0.11), rgba(255, 255, 255, 0.04));
            --kaca-kuat: rgba(28, 28, 28, 0.92);
            --kaca-garis: rgba(255, 255, 255, 0.14);
            --kilau: inset 0 1px 0 rgba(255, 255, 255, 0.22);
            --bayangan: 0 1px 2px rgba(0, 0, 0, 0.6), 0 16px 38px -14px rgba(0, 0, 0, 0.85);
            --hover: rgba(255, 255, 255, 0.09);
            --input-latar: rgba(255, 255, 255, 0.06);
            --scrim: rgba(0, 0, 0, 0.4);
            --blur: 20px;
            --jenuh: 160%;
            --filter-kartu: blur(20px) saturate(160%);
            }
        }
        :root[data-tema="dark"] {
            color-scheme: dark;
            --dasar: #000000;
            --teks: #f5f5f5;
            --teks-redup: #a6a6a6;
            --garis: #8a8a8a;
            --garis-halus: rgba(255, 255, 255, 0.14);
            --merah: #ff5a61;
            --tombol: #d6141c;
            --tombol-hover: #e5242c;
            --tombol-teks: #ffffff;
            --bar: rgba(12, 12, 12, 0.66);
            --kaca: linear-gradient(180deg, rgba(255, 255, 255, 0.11), rgba(255, 255, 255, 0.04));
            --kaca-kuat: rgba(28, 28, 28, 0.92);
            --kaca-garis: rgba(255, 255, 255, 0.14);
            --kilau: inset 0 1px 0 rgba(255, 255, 255, 0.22);
            --bayangan: 0 1px 2px rgba(0, 0, 0, 0.6), 0 16px 38px -14px rgba(0, 0, 0, 0.85);
            --hover: rgba(255, 255, 255, 0.09);
            --input-latar: rgba(255, 255, 255, 0.06);
            --scrim: rgba(0, 0, 0, 0.4);
            --blur: 20px;
            --jenuh: 160%;
            --filter-kartu: blur(20px) saturate(160%);
        }
        * { box-sizing: border-box; }
        html { --filter-bar: none; background-color: var(--dasar); -webkit-text-size-adjust: 100%; scrollbar-gutter: stable; }
        html.kaca-siap { --filter-bar: blur(calc(var(--blur) + 4px)) saturate(var(--jenuh)); }
        html.ganti-tema *, html.ganti-tema *::before, html.ganti-tema *::after { transition: none !important; }
        body { margin: 0; background-color: var(--dasar); color: var(--teks); font: 1rem/1.55 -apple-system, BlinkMacSystemFont, "SF Pro Text", system-ui, "Segoe UI", Roboto, sans-serif; }
        a { color: var(--merah); }
        :focus-visible { outline: 0.1875rem solid var(--merah); outline-offset: 0.125rem; }
        h1 { margin: 0; font-size: clamp(1.5rem, 3vw, 2rem); line-height: 1.2; letter-spacing: -0.02em; }
        h2 { margin: 0 0 0.75rem; font-size: 1.25rem; letter-spacing: -0.01em; }
        h3 { margin: 0 0 0.25rem; font-size: 1.0625rem; }
        p { margin: 0 0 0.75rem; }
        .redup { color: var(--teks-redup); }
        .teks-kecil { font-size: 0.875rem; }
        .isi-teks { white-space: pre-line; }
        .ikon { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
        .lompat { position: absolute; left: -999px; }
        .lompat:focus { left: 1rem; top: 1rem; z-index: 500; padding: 0.5rem 1rem; border-radius: 999px; background: var(--kaca-kuat); }

        /* permukaan kaca: transparansi, blur, tepi tipis, kilau atas, bayangan lembut */
        .kaca, .kartu, .bungkus-tabel, .formulir, .info {
            position: relative;
            background: var(--kaca);
            border: 1px solid var(--kaca-garis);
            border-radius: 1.25rem;
            box-shadow: var(--kilau), var(--bayangan);
            -webkit-backdrop-filter: var(--filter-kartu);
            backdrop-filter: var(--filter-kartu);
        }
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
            .kaca, .kartu, .bungkus-tabel, .formulir, .info { background: var(--kaca-kuat); }
        }
        @media (prefers-reduced-transparency: reduce) {
            .kaca, .kartu, .bungkus-tabel, .formulir, .info { background: var(--kaca-kuat); -webkit-backdrop-filter: none; backdrop-filter: none; }
        }
        .kartu { padding: 1.25rem; }
        .kartu.interaktif { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .kartu.interaktif:hover { transform: translateY(-3px); }
        .kosong { color: var(--teks-redup); }

        /* navigasi: kapsul kaca melayang di atas; di tablet dan ponsel menu pindah ke kapsul bawah */
        .navbar-atas {
            position: fixed; top: 0.75rem; left: 0.75rem; right: 0.75rem; z-index: 100;
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
            max-width: 74rem; height: 3.25rem; margin-inline: auto; padding: 0 0.5rem 0 0.875rem;
            border-radius: 999px; isolation: isolate;
        }
        /* latar kaca ada di pseudo-elemen: backdrop-filter pada header akan menjebak menu bawah yang position: fixed */
        .navbar-atas::before {
            content: ""; position: absolute; inset: 0; z-index: -1; border-radius: inherit;
            background: var(--bar);
            border: 1px solid var(--kaca-garis);
            box-shadow: var(--kilau), var(--bayangan);
            -webkit-backdrop-filter: var(--filter-bar);
            backdrop-filter: var(--filter-bar);
        }
        .merek { display: inline-flex; align-items: center; gap: 0.625rem; min-width: 0; color: var(--teks); font-weight: 700; text-decoration: none; }
        .merek-tanda { display: block; flex: none; width: auto; height: 2.125rem; aspect-ratio: 137 / 189; }
        .merek-teks { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .menu-utama { flex: 1 1 auto; display: flex; justify-content: center; min-width: 0; }
        .menu-utama ul { display: flex; gap: 0.125rem; max-width: 100%; margin: 0; padding: 0.25rem; list-style: none; overflow-x: auto; scrollbar-width: none; }
        .menu-utama li { flex: none; }
        .menu-utama a {
            display: block; padding: 0.5rem 0.875rem; border-radius: 999px;
            color: var(--teks-redup); font-size: 0.9375rem; font-weight: 500; line-height: 1.25; white-space: nowrap; text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }
        .menu-utama a:hover { background: var(--hover); color: var(--teks); }
        .menu-utama a[aria-current="page"] { background: var(--kaca-kuat); color: var(--teks); box-shadow: var(--kilau), 0 1px 3px rgba(0, 0, 0, 0.14); }
        .alat { display: flex; align-items: center; gap: 0.25rem; }
        .alat form { margin: 0; }
        button.tombol-ikon, a.tombol-ikon, span.tombol-ikon {
            position: relative; display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem;
            min-width: 2.25rem; height: 2.25rem; padding: 0 0.625rem;
            border: 0; border-radius: 999px; background: transparent; background-image: none; box-shadow: none;
            color: var(--teks); font: inherit; font-size: 0.875rem; font-weight: 600; text-decoration: none; cursor: pointer;
            transition: background-color 0.2s ease;
        }
        button.tombol-ikon:hover, a.tombol-ikon:hover { background: var(--hover); transform: none; }
        .tombol-ikon .ikon { width: 1.25rem; height: 1.25rem; }
        span.avatar-tombol { width: 2.25rem; padding: 0; border: 1px solid var(--kaca-garis); background: var(--kaca-kuat); cursor: default; }
        button.tombol-keluar {
            min-height: 2.25rem; padding: 0 1.125rem; border: 0; background: var(--teks); background-image: none;
            color: var(--dasar); font-size: 0.875rem; box-shadow: none;
        }
        button.tombol-keluar:hover { background: var(--teks); opacity: 0.82; transform: none; }
        .tema-sistem, .tema-terang, .tema-gelap { display: none; }
        :root:not([data-tema]) .tema-sistem, :root[data-tema="light"] .tema-terang, :root[data-tema="dark"] .tema-gelap { display: inline-flex; }

        /* tombol */
        button, a.tombol {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            min-height: 2.75rem; padding: 0.5rem 1.25rem;
            border: 1px solid var(--tombol); border-radius: 999px;
            background-color: var(--tombol); background-image: linear-gradient(180deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0) 60%);
            color: var(--tombol-teks); font: inherit; font-weight: 600; line-height: 1.4; text-decoration: none; cursor: pointer;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4), 0 8px 18px -10px rgba(214, 20, 28, 0.7);
            transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
        }
        button:hover, a.tombol:hover { background-color: var(--tombol-hover); transform: translateY(-1px); }
        button:active, a.tombol:active { transform: scale(0.98); }
        button:disabled { opacity: 0.45; cursor: not-allowed; transform: none; }
        button.sekunder, a.tombol.sekunder { background-color: var(--kaca-kuat); background-image: none; border-color: var(--kaca-garis); color: var(--teks); box-shadow: var(--kilau); }
        button.sekunder:hover, a.tombol.sekunder:hover { background-color: var(--hover); }
        button.bahaya, a.tombol.bahaya, a.tombol.garis { background: transparent; background-image: none; border-color: var(--merah); color: var(--merah); box-shadow: none; }
        button.bahaya:hover, a.tombol.bahaya:hover, a.tombol.garis:hover { background-color: var(--hover); }
        button.kecil, a.tombol.kecil { min-height: 2.25rem; padding: 0.25rem 0.875rem; font-size: 0.875rem; }
        .aksi { display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; }
        .aksi form { margin: 0; }

        /* formulir */
        .formulir { max-width: 40rem; padding: 1.5rem; }
        .formulir.lebar { max-width: none; }
        .kolom { margin-bottom: 1rem; }
        .dua-kolom { display: grid; grid-template-columns: repeat(auto-fit, minmax(12rem, 1fr)); gap: 0 1rem; }
        label { display: block; margin-bottom: 0.25rem; font-weight: 600; }
        label.sebaris { display: flex; align-items: center; gap: 0.5rem; font-weight: 500; }
        input, select, textarea {
            width: 100%; min-height: 2.75rem; padding: 0.5rem 0.875rem;
            border: 1px solid var(--garis); border-radius: 0.875rem;
            background: var(--input-latar); color: var(--teks); font: inherit;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        input[type="checkbox"], input[type="radio"] { width: 1.25rem; min-height: 1.25rem; accent-color: var(--merah); }
        input[type="file"] { padding: 0.5rem; }
        input[type="range"] { min-height: 0; padding: 0; accent-color: var(--merah); }
        textarea { min-height: 8rem; resize: vertical; }
        select[multiple] { min-height: 11rem; }
        input:focus-visible, select:focus-visible, textarea:focus-visible { outline: none; border-color: var(--merah); box-shadow: 0 0 0 0.1875rem var(--hover), 0 0 0 0.125rem var(--merah); }
        [aria-invalid="true"] { border-color: var(--merah); }
        fieldset { margin: 0 0 1.25rem; padding: 0; border: 0; }
        legend { margin-bottom: 0.5rem; padding: 0; font-weight: 600; }
        .petunjuk { margin: 0.25rem 0 0; color: var(--teks-redup); font-size: 0.875rem; }
        .galat { margin: 0.25rem 0 0; color: var(--merah); font-size: 0.9375rem; line-height: 1.3; }

        /* tabel, lencana, info */
        .bungkus-tabel { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem 1rem; text-align: left; vertical-align: top; border-bottom: 1px solid var(--garis-halus); }
        th { color: var(--teks-redup); font-size: 0.875rem; font-weight: 600; white-space: nowrap; }
        tbody tr { transition: background-color 0.2s ease; }
        tbody tr:hover { background: var(--hover); }
        tbody tr:last-child td { border-bottom: 0; }
        caption { position: absolute; left: -9999px; }
        td input, td select { min-width: 5rem; }
        td ul { margin: 0; padding: 0; list-style: none; }
        .grid-excel td { padding: 0; }
        .grid-excel th { padding: 0.5rem 0.75rem; }
        .grid-excel td:first-child { padding: 0 0.75rem; color: var(--teks-redup); text-align: center; }
        .grid-excel input { min-height: 2.5rem; border: 0; border-radius: 0; background: transparent; }
        .grid-excel input:focus-visible { box-shadow: inset 0 0 0 0.125rem var(--merah); }
        .grid-excel td + td { border-left: 1px solid var(--garis-halus); }
        .lencana { display: inline-block; padding: 0.125rem 0.625rem; border: 1px solid var(--kaca-garis); border-radius: 999px; background: var(--kaca-kuat); font-size: 0.8125rem; font-weight: 600; white-space: nowrap; }
        .lencana.galat, .lencana.peringatan { margin: 0; color: var(--merah); border-color: var(--merah); }
        .info { margin: 0; padding: 0.75rem 1rem; border-radius: 1rem; }
        .info.bahaya { border-color: var(--merah); color: var(--merah); }
        .akordeon summary { cursor: pointer; font-weight: 600; }
        details.kartu > summary { cursor: pointer; font-weight: 600; list-style-position: inside; }
        details.kartu[open] > summary { margin-bottom: 0.5rem; }

        /* notifikasi toast */
        .area-toast { position: fixed; z-index: 300; top: calc(var(--tinggi-bar) + 0.75rem); right: 1rem; display: grid; gap: 0.5rem; width: min(24rem, calc(100% - 2rem)); pointer-events: none; }
        .toast {
            position: relative; overflow: hidden; pointer-events: auto;
            display: grid; grid-template-columns: auto 1fr auto; align-items: start; gap: 0.75rem;
            padding: 0.875rem 0.75rem 1rem 1rem;
            border: 1px solid var(--kaca-garis); border-radius: 1rem;
            background: var(--kaca-kuat);
            box-shadow: var(--kilau), var(--bayangan);
            -webkit-backdrop-filter: var(--filter-bar); backdrop-filter: var(--filter-bar);
            animation: toast-masuk 0.25s ease both;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .toast .ikon { width: 1.5rem; height: 1.5rem; }
        .toast p { margin: 0.125rem 0 0; }
        .toast.galat { border-color: var(--merah); }
        .toast.galat .ikon { color: var(--merah); }
        .toast.keluar { opacity: 0; transform: translateX(1rem); }
        .toast::after { content: ""; position: absolute; left: 0; bottom: 0; width: 100%; height: 0.1875rem; background: var(--teks-redup); transform-origin: left; animation: toast-waktu 5s linear forwards; }
        .toast.galat::after { background: var(--merah); }
        button.toast-tutup { min-width: 2rem; min-height: 2rem; height: 2rem; padding: 0; border: 0; border-radius: 0.5rem; background: transparent; background-image: none; box-shadow: none; color: var(--teks); }
        button.toast-tutup:hover { background: var(--hover); transform: none; }
        @keyframes toast-masuk { from { opacity: 0; transform: translateY(-0.5rem) scale(0.98); } }
        @keyframes toast-waktu { to { transform: scaleX(0); } }

        /* dialog konfirmasi dan dialog umum */
        dialog.dialog { width: calc(100% - 2rem); max-width: 26rem; margin: auto; padding: 1.5rem; border: 1px solid var(--kaca-garis); border-radius: 1.5rem; background: var(--kaca-kuat); color: var(--teks); box-shadow: var(--kilau), var(--bayangan), 0 40px 80px -30px rgba(15, 23, 42, 0.28); -webkit-backdrop-filter: blur(calc(var(--blur) + 8px)) saturate(var(--jenuh)); backdrop-filter: blur(calc(var(--blur) + 8px)) saturate(var(--jenuh)); }
        dialog.dialog.lebar { max-width: 44rem; }
        dialog.dialog::backdrop { background: var(--scrim); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); }
        dialog.dialog form { margin: 0; }

        @media (max-width: 1023px) {
            .menu-utama::before {
                content: ""; position: absolute; inset: 0; z-index: -1; border-radius: inherit;
                background: var(--bar); border: 1px solid var(--kaca-garis); box-shadow: var(--kilau), var(--bayangan);
                -webkit-backdrop-filter: var(--filter-bar); backdrop-filter: var(--filter-bar);
            }
            .menu-utama {
                position: fixed; left: 0.75rem; right: 0.75rem; bottom: calc(0.75rem + env(safe-area-inset-bottom)); z-index: 101;
                max-width: 40rem; height: 3.25rem; margin-inline: auto; border-radius: 999px;
            }
            .menu-utama ul { width: 100%; justify-content: safe center; align-items: center; }
            .avatar-tombol { display: none !important; }
            .area-toast { top: auto; bottom: calc(var(--tinggi-bawah) + env(safe-area-inset-bottom) + 0.75rem); left: 0.75rem; right: 0.75rem; width: auto; }
        }
        @media (pointer: coarse) {
            button.kecil, a.tombol.kecil { min-height: 2.75rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition: none !important; animation: none !important; }
            .kartu.interaktif:hover, button:hover, a.tombol:hover { transform: none; }
            .toast::after { display: none; }
        }
