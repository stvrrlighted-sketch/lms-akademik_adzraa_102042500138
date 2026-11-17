<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\MataKuliah;
use App\Models\PengumpulanTugas;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function indeks(): View
    {
        return view('kelas.indeks', [
            'daftarKelas' => Kelas::with(['mataKuliah', 'tahunAjaran', 'dosen'])->withCount('mahasiswa')->orderByDesc('tahun_ajaran_id')->orderBy('id')->get(),
        ]);
    }

    public function buat(): View
    {
        return view('kelas.form', ['kelas' => new Kelas] + $this->pilihan());
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturan());

        if ($this->kembar($data)) {
            return back()->withErrors(['nama' => 'Kelas dengan nama itu sudah ada untuk mata kuliah dan tahun ajaran tersebut.'])->withInput();
        }

        $kelas = Kelas::create($data);

        foreach (range(1, 14) as $nomor) {
            $kelas->pertemuan()->create(['nomor' => $nomor]);
        }

        return redirect()->route('kelas.indeks')->with('sukses', 'Kelas dibuat dengan 14 pertemuan.');
    }

    public function ubah(Kelas $kelas): View
    {
        return view('kelas.form', compact('kelas') + $this->pilihan());
    }

    public function perbarui(Request $request, Kelas $kelas): RedirectResponse
    {
        $data = $request->validate($this->aturan());

        if ($this->kembar($data, $kelas->id)) {
            return back()->withErrors(['nama' => 'Kelas dengan nama itu sudah ada untuk mata kuliah dan tahun ajaran tersebut.'])->withInput();
        }

        $kelas->update($data);

        return redirect()->route('kelas.indeks')->with('sukses', 'Kelas diperbarui.');
    }

    public function hapus(Kelas $kelas): RedirectResponse
    {
        $jalur = Materi::whereIn('pertemuan_id', $kelas->pertemuan()->select('id'))->whereNotNull('path')->pluck('path')
            ->merge(PengumpulanTugas::whereIn('tugas_id', $kelas->tugas()->select('id'))->pluck('path'));

        $kelas->delete();
        Storage::disk('local')->delete($jalur->all());

        return redirect()->route('kelas.indeks')->with('sukses', 'Kelas dihapus beserta seluruh isinya.');
    }

    public function mahasiswa(Kelas $kelas): View
    {
        $kelas->load(['mataKuliah', 'tahunAjaran', 'dosen']);
        $terdaftar = $kelas->mahasiswa()->orderBy('name')->get();

        return view('kelas.mahasiswa', [
            'kelas' => $kelas,
            'terdaftar' => $terdaftar,
            'calon' => User::where('role', 'mahasiswa')->whereNotIn('id', $terdaftar->pluck('id'))->orderBy('name')->get(),
        ]);
    }

    public function daftarkan(Request $request, Kelas $kelas): RedirectResponse
    {
        $data = $request->validate([
            'mahasiswa_id' => ['required', 'array', 'min:1'],
            'mahasiswa_id.*' => ['integer', Rule::exists('users', 'id')->where('role', 'mahasiswa')],
        ]);

        $kelas->mahasiswa()->syncWithoutDetaching($data['mahasiswa_id']);

        return back()->with('sukses', 'Mahasiswa didaftarkan ke kelas.');
    }

    public function keluarkan(Kelas $kelas, User $mahasiswa): RedirectResponse
    {
        $kelas->mahasiswa()->detach($mahasiswa->id);

        return back()->with('sukses', 'Mahasiswa dikeluarkan dari kelas.');
    }

    private function pilihan(): array
    {
        return [
            'daftarMataKuliah' => MataKuliah::orderBy('kode')->get(),
            'daftarTahun' => TahunAjaran::orderByDesc('id')->get(),
            'daftarDosen' => User::where('role', 'dosen')->orderBy('name')->get(),
        ];
    }

    private function aturan(): array
    {
        return [
            'mata_kuliah_id' => ['required', 'integer', 'exists:mata_kuliah,id'],
            'tahun_ajaran_id' => ['required', 'integer', 'exists:tahun_ajaran,id'],
            'nama' => ['required', 'string', 'max:20'],
            'dosen_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'dosen')],
        ];
    }

    private function kembar(array $data, ?int $idAbaikan = null): bool
    {
        return Kelas::where('mata_kuliah_id', $data['mata_kuliah_id'])
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('nama', $data['nama'])
            ->when($idAbaikan, fn ($kueri) => $kueri->whereKeyNot($idAbaikan))
            ->exists();
    }
}
