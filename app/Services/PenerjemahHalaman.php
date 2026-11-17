<?php

namespace App\Services;

class PenerjemahHalaman
{
    private const ATRIBUT = 'aria-label|title|placeholder|alt|data-konfirmasi|data-tooltip';

    private array $statis = [];

    private array $pola = [];

    public function __construct()
    {
        $jalur = lang_path('en.json');
        $kamus = is_file($jalur) ? json_decode((string) file_get_contents($jalur), true) : [];

        foreach (is_array($kamus) ? $kamus : [] as $kunci => $nilai) {
            $kunci = $this->rapikan((string) $kunci);

            if (preg_match('/:[a-z_]+/', $kunci)) {
                $this->pola[] = [$this->susunRegex($kunci), (string) $nilai, strlen($kunci)];
            } else {
                $this->statis[$kunci] = (string) $nilai;
            }
        }

        usort($this->pola, fn ($a, $b) => $b[2] <=> $a[2]);
    }

    public function terjemahkan(string $html): string
    {
        // Blok skrip, gaya, kolom teks, dan komentar tidak disentuh; isinya bukan teks antarmuka.
        $bagian = preg_split('/(<script\b.*?<\/script>|<style\b.*?<\/style>|<textarea\b.*?<\/textarea>|<!--.*?-->)/is', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($bagian === false) {
            return $html;
        }

        foreach ($bagian as $indeks => $isi) {
            if ($indeks % 2 === 0) {
                $bagian[$indeks] = $this->prosesBagian($isi);
            }
        }

        return implode('', $bagian);
    }

    private function prosesBagian(string $isi): string
    {
        $potongan = preg_split('/(<[^>]*>)/', $isi, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($potongan === false) {
            return $isi;
        }

        foreach ($potongan as $indeks => $potong) {
            $potongan[$indeks] = $indeks % 2 === 1 ? $this->prosesTag($potong) : $this->terjemahTeks($potong);
        }

        return implode('', $potongan);
    }

    private function prosesTag(string $tag): string
    {
        return preg_replace_callback(
            '/(?<![\w-])('.self::ATRIBUT.')="([^"]*)"/',
            fn (array $cocok) => $cocok[1].'="'.$this->terjemahTeks($cocok[2]).'"',
            $tag,
        ) ?? $tag;
    }

    private function terjemahTeks(string $teks): string
    {
        $dekode = html_entity_decode($teks, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $inti = $this->rapikan($dekode);

        if ($inti === '') {
            return $teks;
        }

        $hasil = $this->cari($inti);

        // Judul halaman berbentuk "Judul | Nama aplikasi": hanya bagian judul yang diterjemahkan.
        if ($hasil === null && str_contains($inti, ' | ')) {
            $posisi = strrpos($inti, ' | ');
            $kiri = substr($inti, 0, $posisi);
            $terjemah = $this->cari('@judul '.$kiri) ?? $this->cari($kiri);
            $hasil = $terjemah === null ? null : $terjemah.substr($inti, $posisi);
        }

        if ($hasil === null) {
            return $teks;
        }

        $awal = substr($dekode, 0, strlen($dekode) - strlen(ltrim($dekode)));
        $akhir = substr($dekode, strlen(rtrim($dekode)));

        return $awal.htmlspecialchars($hasil, ENT_QUOTES, 'UTF-8', false).$akhir;
    }

    private function cari(string $teks): ?string
    {
        if (isset($this->statis[$teks])) {
            return $this->statis[$teks];
        }

        foreach ($this->pola as [$regex, $nilai]) {
            if (preg_match($regex, $teks, $cocok)) {
                $hasil = $nilai;

                foreach ($cocok as $nama => $isi) {
                    if (is_string($nama)) {
                        $hasil = str_replace(':'.$nama, $isi, $hasil);
                    }
                }

                return $hasil;
            }
        }

        return null;
    }

    private function susunRegex(string $kunci): string
    {
        $bagian = preg_split('/(:[a-z_]+)/', $kunci, -1, PREG_SPLIT_DELIM_CAPTURE);
        $regex = '';

        foreach ($bagian as $potong) {
            if (preg_match('/^:([a-z_]+)$/', $potong, $nama)) {
                // Token berawalan n_ hanya cocok dengan angka; token lain cocok dengan teks apa pun.
                $regex .= '(?P<'.$nama[1].'>'.(str_starts_with($nama[1], 'n_') ? '\d+' : '.+?').')';
            } else {
                $regex .= preg_quote($potong, '/');
            }
        }

        return '/^'.$regex.'$/su';
    }

    private function rapikan(string $teks): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $teks));
    }
}
