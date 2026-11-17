<script data-tetap>
    (function () {
        var html = document.documentElement;

        /* ---------- Bagian yang berjalan sekali per dokumen ---------- */
        if (!window.__dasarSiap) {
            window.__dasarSiap = true;

            // dialog konfirmasi: satu pendengar untuk seluruh dokumen, dialog dicari saat dibutuhkan
            var formTertunda = null;
            document.addEventListener('submit', function (e) {
                var form = e.target;
                var pesan = form.getAttribute && form.getAttribute('data-konfirmasi');
                if (!pesan || form.getAttribute('data-sudah') === '1') { return; }
                var dialog = document.getElementById('dialog-konfirmasi');
                if (!dialog) { return; }
                e.preventDefault();
                if (typeof dialog.showModal !== 'function') {
                    if (window.confirm(pesan)) { form.setAttribute('data-sudah', '1'); form.submit(); }
                    return;
                }
                formTertunda = form;
                document.getElementById('teks-dialog').textContent = pesan;
                dialog.showModal();
            });
            document.addEventListener('close', function (e) {
                var dialog = e.target;
                if (!dialog || dialog.id !== 'dialog-konfirmasi') { return; }
                if (dialog.returnValue === 'ya' && formTertunda) {
                    formTertunda.setAttribute('data-sudah', '1');
                    formTertunda.submit();
                }
                formTertunda = null;
                dialog.returnValue = '';
            }, true);

            // pindah section lewat navbar: tukar isi halaman tanpa memuat ulang dokumen (tanpa kedipan)
            var nomor = 0;
            var tukar = function (doc) {
                var h0 = document.querySelector('header.navbar-atas');
                var h1 = doc.querySelector('header.navbar-atas');
                var s0 = document.head.querySelector('style');
                var s1 = doc.head.querySelector('style');
                if (!h0 || !h1 || !s0 || !s1 || !doc.body) { return false; }
                if (html.classList.contains('publik') !== doc.documentElement.classList.contains('publik')) { return false; }

                var l0 = h0.querySelectorAll('.menu-utama a'), l1 = h1.querySelectorAll('.menu-utama a');
                if (l0.length !== l1.length) { return false; }
                for (var i = 0; i < l0.length; i++) {
                    if (l0[i].getAttribute('href') !== l1[i].getAttribute('href')) { return false; }
                }

                var tetap = function (n) {
                    return n === h1 || (n.nodeType === 1 && n.tagName === 'SPAN' && /^teks-tema-/.test(n.id));
                };
                var simpan = function (n) {
                    return n === h0 || (n.nodeType === 1 && n.tagName === 'SPAN' && /^teks-tema-/.test(n.id));
                };

                // semua pemeriksaan lolos: mulai menukar
                if (s0.textContent !== s1.textContent) { s0.textContent = s1.textContent; }
                document.title = doc.title;
                for (var j = 0; j < l0.length; j++) {
                    if (l1[j].hasAttribute('aria-current')) { l0[j].setAttribute('aria-current', 'page'); }
                    else { l0[j].removeAttribute('aria-current'); }
                }
                document.body.className = doc.body.className;

                Array.prototype.slice.call(document.body.childNodes).forEach(function (n) {
                    if (!simpan(n)) { document.body.removeChild(n); }
                });
                var sebelumHeader = true;
                Array.prototype.slice.call(doc.body.childNodes).forEach(function (n) {
                    if (n === h1) { sebelumHeader = false; return; }
                    if (tetap(n)) { return; }
                    var salin = document.importNode(n, true);
                    if (sebelumHeader) { document.body.insertBefore(salin, h0); } else { document.body.appendChild(salin); }
                });

                // skrip hasil DOMParser tidak berjalan sendiri: buat ulang agar skrip halaman aktif
                Array.prototype.slice.call(document.body.querySelectorAll('script')).forEach(function (lama) {
                    if (lama.hasAttribute('data-tetap')) { return; }
                    var baru = document.createElement('script');
                    for (var k = 0; k < lama.attributes.length; k++) { baru.setAttribute(lama.attributes[k].name, lama.attributes[k].value); }
                    baru.text = lama.textContent;
                    lama.parentNode.replaceChild(baru, lama);
                });

                if (typeof window.mulaiHalaman === 'function') { window.mulaiHalaman(); }
                return true;
            };

            var pindah = function (url, dorong) {
                var saya = ++nomor;
                fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'text/html' } })
                    .then(function (r) {
                        var jenis = r.headers.get('content-type') || '';
                        if (!r.ok || jenis.indexOf('text/html') < 0) { throw new Error('bukan halaman'); }
                        return r.text().then(function (teks) { return { teks: teks, url: r.url }; });
                    })
                    .then(function (hasil) {
                        if (saya !== nomor) { return; }
                        var doc = new DOMParser().parseFromString(hasil.teks, 'text/html');
                        if (!tukar(doc)) { throw new Error('tata letak berbeda'); }
                        if (dorong) { history.pushState({ spa: 1 }, '', hasil.url); window.scrollTo(0, 0); }
                    })
                    .catch(function () { if (saya === nomor) { window.location.href = url; } });
            };

            document.addEventListener('click', function (e) {
                if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
                var a = e.target.closest ? e.target.closest('header.navbar-atas .menu-utama a, header.navbar-atas a.merek') : null;
                if (!a || a.target || a.hasAttribute('download') || typeof window.fetch !== 'function' || typeof DOMParser === 'undefined') { return; }
                var u = new URL(a.href, window.location.href);
                if (u.origin !== window.location.origin) { return; }
                if (u.href === window.location.href) { e.preventDefault(); return; }
                e.preventDefault();
                pindah(u.href, true);
            });
            window.addEventListener('popstate', function () { pindah(window.location.href, false); });
        }

        /* ---------- Bagian yang dijalankan setiap halaman tampil ---------- */
        window.mulaiHalaman = function () {
            // blur navbar baru dinyalakan setelah cat pertama (khusus mode gelap)
            if (!html.classList.contains('kaca-siap')) {
                window.requestAnimationFrame(function () {
                    window.requestAnimationFrame(function () { html.classList.add('kaca-siap'); });
                });
            }

            var tombolTema = document.getElementById('ganti-tema');
            if (tombolTema && !tombolTema.__terikat) {
                tombolTema.__terikat = true;
                var urutan = ['sistem', 'light', 'dark'];
                var modeSekarang = function () {
                    var t = html.getAttribute('data-tema');
                    return t === 'light' || t === 'dark' ? t : 'sistem';
                };
                var segarkan = function () {
                    var el = document.getElementById('teks-tema-' + modeSekarang());
                    tombolTema.setAttribute('aria-label', el ? el.textContent.trim() : '');
                };
                var simpan = function (nilai) {
                    var meta = document.querySelector('meta[name="color-scheme"]');
                    // matikan semua transisi selama pergantian agar warna tidak memudar lewat abu-abu atau gelap
                    html.classList.add('ganti-tema');
                    if (nilai === 'sistem') {
                        html.removeAttribute('data-tema');
                        try { localStorage.removeItem('tema'); } catch (e) {}
                        try { document.cookie = 'tema=; path=/; max-age=0; SameSite=Lax'; } catch (e) {}
                    } else {
                        html.setAttribute('data-tema', nilai);
                        try { localStorage.setItem('tema', nilai); } catch (e) {}
                        try { document.cookie = 'tema=' + nilai + '; path=/; max-age=31536000; SameSite=Lax'; } catch (e) {}
                    }
                    if (meta) { meta.setAttribute('content', nilai === 'sistem' ? 'light dark' : nilai); }
                    window.requestAnimationFrame(function () {
                        window.requestAnimationFrame(function () { html.classList.remove('ganti-tema'); });
                    });
                };
                tombolTema.addEventListener('click', function () {
                    simpan(urutan[(urutan.indexOf(modeSekarang()) + 1) % urutan.length]);
                    segarkan();
                });
                segarkan();
            }

            var areaToast = document.getElementById('area-toast');
            if (areaToast) {
                var tutupToast = function (toast) {
                    if (toast.getAttribute('data-tutup') === '1') { return; }
                    toast.setAttribute('data-tutup', '1');
                    toast.classList.add('keluar');
                    setTimeout(function () { toast.remove(); }, 220);
                };
                Array.prototype.forEach.call(areaToast.querySelectorAll('.toast'), function (toast) {
                    var tombol = toast.querySelector('.toast-tutup');
                    if (tombol) { tombol.addEventListener('click', function () { tutupToast(toast); }); }
                    setTimeout(function () { tutupToast(toast); }, 5000);
                });
            }
        };

        window.mulaiHalaman();
    })();
</script>
