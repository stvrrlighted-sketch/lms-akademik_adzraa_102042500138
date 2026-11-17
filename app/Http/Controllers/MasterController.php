<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MasterController extends Controller
{
    public function indeks(string $jenis): View
    {
        $definisi = $this->definisi($jenis);

        return view('master.indeks', [
            'jenis' => $jenis,
            'definisi' => $definisi,
            'daftar' => $definisi['model']::with($definisi['dengan'] ?? [])->orderBy('id')->get(),
        ]);
    }

    public function buat(string $jenis): View
    {
        $definisi = $this->definisi($jenis);

        return view('master.form', ['jenis' => $jenis, 'definisi' => $definisi, 'item' => new $definisi['model']]);
    }

    public function simpan(Request $request, string $jenis): RedirectResponse
    {
        $definisi = $this->definisi($jenis);
        $data = $request->validate($this->aturan($definisi));

        $item = $definisi['model']::create($this->lengkapi($request, $definisi, $data));
        $this->jagaSatuAktif($item);

        return redirect()->route('master.indeks', $jenis)->with('sukses', $definisi['judul'].' ditambahkan.');
    }

    public function ubah(string $jenis, int $id): View
    {
        $definisi = $this->definisi($jenis);

        return view('master.form', ['jenis' => $jenis, 'definisi' => $definisi, 'item' => $definisi['model']::findOrFail($id)]);
    }

    public function perbarui(Request $request, string $jenis, int $id): RedirectResponse
    {
        $definisi = $this->definisi($jenis);
        $item = $definisi['model']::findOrFail($id);
        $data = $request->validate($this->aturan($definisi, $item->id));

        $item->update($this->lengkapi($request, $definisi, $data));
        $this->jagaSatuAktif($item);

        return redirect()->route('master.indeks', $jenis)->with('sukses', $definisi['judul'].' diperbarui.');
    }

    public function hapus(string $jenis, int $id): RedirectResponse
    {
        $definisi = $this->definisi($jenis);
        $item = $definisi['model']::findOrFail($id);

        try {
            $item->delete();
        } catch (QueryException) {
            return back()->with('galat', 'Data ini masih dipakai (misalnya oleh kelas) dan tidak dapat dihapus.');
        }

        return redirect()->route('master.indeks', $jenis)->with('sukses', $definisi['judul'].' dihapus.');
    }

    private function definisi(string $jenis): array
    {
        return match ($jenis) {
            'tahun-ajaran' => [
                'model' => TahunAjaran::class,
                'judul' => 'Tahun ajaran',
                'kolom' => [
                    ['nama' => 'nama', 'label' => 'Nama', 'tipe' => 'teks', 'petunjuk' => 'Contoh: 2026/2027 Ganjil', 'aturan' => ['required', 'string', 'max:100'], 'unik' => true],
                    ['nama' => 'aktif', 'label' => 'Semester aktif', 'tipe' => 'centang', 'petunjuk' => 'Hanya satu tahun ajaran yang dapat aktif. Menandai yang ini menonaktifkan yang lain.'],
                ],
            ],
            'program-studi' => [
                'model' => ProgramStudi::class,
                'judul' => 'Program studi',
                'kolom' => [
                    ['nama' => 'kode', 'label' => 'Kode', 'tipe' => 'teks', 'aturan' => ['required', 'string', 'max:20'], 'unik' => true],
                    ['nama' => 'nama', 'label' => 'Nama', 'tipe' => 'teks', 'aturan' => ['required', 'string', 'max:255']],
                ],
            ],
            'mata-kuliah' => [
                'model' => MataKuliah::class,
                'judul' => 'Mata kuliah',
                'dengan' => ['programStudi'],
                'kolom' => [
                    ['nama' => 'kode', 'label' => 'Kode mata kuliah', 'tipe' => 'teks', 'aturan' => ['required', 'string', 'max:20'], 'unik' => true],
                    ['nama' => 'nama', 'label' => 'Nama mata kuliah', 'tipe' => 'teks', 'aturan' => ['required', 'string', 'max:255']],
                    ['nama' => 'sks', 'label' => 'SKS', 'tipe' => 'angka', 'aturan' => ['required', 'integer', 'between:1,6']],
                    ['nama' => 'program_studi_id', 'label' => 'Program studi', 'tipe' => 'pilih', 'relasi' => 'programStudi', 'tabel' => 'program_studi', 'pilihan' => ProgramStudi::orderBy('nama')->pluck('nama', 'id')->all()],
                ],
            ],
        };
    }

    private function aturan(array $definisi, ?int $idAbaikan = null): array
    {
        $tabel = (new $definisi['model'])->getTable();
        $aturan = [];

        foreach ($definisi['kolom'] as $kolom) {
            $nama = $kolom['nama'];

            if ($kolom['tipe'] === 'centang') {
                continue;
            }

            if ($kolom['tipe'] === 'pilih') {
                $aturan[$nama] = ['nullable', Rule::exists($kolom['tabel'], 'id')];

                continue;
            }

            $aturan[$nama] = $kolom['aturan'];

            if (! empty($kolom['unik'])) {
                $aturan[$nama][] = Rule::unique($tabel, $nama)->ignore($idAbaikan);
            }
        }

        return $aturan;
    }

    private function lengkapi(Request $request, array $definisi, array $data): array
    {
        foreach ($definisi['kolom'] as $kolom) {
            if ($kolom['tipe'] === 'centang') {
                $data[$kolom['nama']] = $request->boolean($kolom['nama']);
            }
        }

        return $data;
    }

    private function jagaSatuAktif(mixed $item): void
    {
        if ($item instanceof TahunAjaran && $item->aktif) {
            TahunAjaran::whereKeyNot($item->id)->update(['aktif' => false]);
        }
    }
}
