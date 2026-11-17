{{-- Dimuat PERTAMA di <head> pada semua tata letak: tema ditetapkan sebelum cat pertama, jadi tidak ada kilatan gelap. --}}
<meta name="color-scheme" content="light dark">
<script>
    (function () {
        var d = document.documentElement, t = null, c;
        try { t = localStorage.getItem('tema'); } catch (e) {}
        if (t !== 'light' && t !== 'dark') {
            c = document.cookie.match(/(?:^|; )tema=(light|dark)(?:;|$)/);
            t = c ? c[1] : null;
        }
        if (t === 'light' || t === 'dark') {
            d.setAttribute('data-tema', t);
            // cadangan ganda: localStorage dan cookie selalu searah, sehingga satu yang gagal tidak mereset tema
            try { localStorage.setItem('tema', t); } catch (e) {}
            try { document.cookie = 'tema=' + t + '; path=/; max-age=31536000; SameSite=Lax'; } catch (e) {}
        }
        var m = document.querySelector('meta[name="color-scheme"]');
        if (m) { m.setAttribute('content', t === 'light' || t === 'dark' ? t : 'light dark'); }
    })();
</script>
<style>
    /* kanvas dan latar akar ditetapkan sebelum stylesheet lain: putih murni di mode terang, tanpa transisi */
    html { background-color: #ffffff; transition: none; }
    html[data-tema="dark"] { background-color: #000000; }
    @media (prefers-color-scheme: dark) { html:not([data-tema="light"]) { background-color: #000000; } }
</style>
