<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BeritaController extends Controller
{
    private const FOLDER = 'uploads/berita';

    public function indeks(): View
    {
        return view('berita.indeks', [
            'daftarBerita' => Berita::orderByDesc('tanggal_terbit')->orderByDesc('id')->get(),
        ]);
    }

    public function buat(): View
    {
        return view('berita.form', ['berita' => new Berita(['tanggal_terbit' => today()])]);
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturan());
        $data['banner'] = $this->simpanBanner($request);
        $data['pembuat_id'] = $request->user()->id;

        Berita::create($data);

        return redirect()->route('berita.indeks')->with('sukses', 'Berita diterbitkan.');
    }

    public function ubah(Berita $berita): View
    {
        return view('berita.form', compact('berita'));
    }

    public function perbarui(Request $request, Berita $berita): RedirectResponse
    {
        $data = $request->validate($this->aturan());

        if ($request->hasFile('banner')) {
            $this->hapusBanner($berita);
            $data['banner'] = $this->simpanBanner($request);
        } elseif ($request->boolean('hapus_banner')) {
            $this->hapusBanner($berita);
            $data['banner'] = null;
        } else {
            unset($data['banner']);
        }

        $berita->update($data);

        return redirect()->route('berita.indeks')->with('sukses', 'Berita diperbarui.');
    }

    public function hapus(Berita $berita): RedirectResponse
    {
        $this->hapusBanner($berita);
        $berita->delete();

        return redirect()->route('berita.indeks')->with('sukses', 'Berita dihapus.');
    }

    private function aturan(): array
    {
        return [
            'judul' => ['required', 'string', 'max:150'],
            'isi' => ['required', 'string', 'max:20000'],
            'tanggal_terbit' => ['required', 'date'],
            'kedaluwarsa_pada' => ['nullable', 'date', 'after_or_equal:tanggal_terbit'],
            // Batas resolusi diperiksa lagi di server; alat potong di browser hanya kemudahan, bukan pengamanan.
            'banner' => [
                'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120',
                'dimensions:max_width='.Berita::BANNER_LEBAR_MAKS.',max_height='.Berita::BANNER_TINGGI_MAKS,
            ],
        ];
    }

    private function simpanBanner(Request $request): ?string
    {
        if (! $request->hasFile('banner')) {
            return null;
        }

        $berkas = $request->file('banner');

        // Nama acak dan ekstensi dari isi berkas, bukan dari nama asli kiriman pengguna.
        $nama = Str::random(40).'.'.$berkas->extension();

        File::ensureDirectoryExists(public_path(self::FOLDER));
        $berkas->move(public_path(self::FOLDER), $nama);

        return $nama;
    }

    private function hapusBanner(Berita $berita): void
    {
        if ($berita->banner) {
            File::delete(public_path(self::FOLDER.'/'.$berita->banner));
        }
    }
}
