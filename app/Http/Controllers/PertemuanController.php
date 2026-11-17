<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MemeriksaAksesKelas;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Pertemuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PertemuanController extends Controller
{
    use MemeriksaAksesKelas;

    public function indeks(Request $request, Kelas $kelas): View
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        foreach (range(1, 14) as $nomor) {
            Pertemuan::firstOrCreate(['kelas_id' => $kelas->id, 'nomor' => $nomor]);
        }

        return view('kelola.materi', [
            'kelas' => $kelas,
            'daftarPertemuan' => $kelas->pertemuan()->with('materi')->orderBy('nomor')->get(),
        ]);
    }

    public function perbarui(Request $request, Pertemuan $pertemuan): RedirectResponse
    {
        $this->kelasKelola($request, $pertemuan->kelas_id);

        $data = $request->validate(['topik' => ['nullable', 'string', 'max:200']]);
        $pertemuan->update($data);

        return back()->with('sukses', 'Topik pertemuan '.$pertemuan->nomor.' disimpan.');
    }

    public function tambahMateri(Request $request, Pertemuan $pertemuan): RedirectResponse
    {
        $this->kelasKelola($request, $pertemuan->kelas_id);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'berkas' => ['nullable', 'required_without:url', 'file', 'max:10240', 'extensions:pdf,pptx,docx'],
            // Hanya skema http dan https yang diterima agar tautan javascript: tidak bisa disisipkan.
            'url' => ['nullable', 'required_without:berkas', 'url:http,https', 'max:500'],
        ]);

        if ($request->hasFile('berkas')) {
            $berkas = $request->file('berkas');

            $pertemuan->materi()->create([
                'jenis' => 'berkas',
                'judul' => $data['judul'],
                'path' => Storage::disk('local')->putFile('materi', $berkas),
                'nama_asli' => $berkas->getClientOriginalName(),
            ]);
        }

        if (! empty($data['url'])) {
            $pertemuan->materi()->create([
                'jenis' => 'tautan',
                'judul' => $data['judul'],
                'url' => $data['url'],
            ]);
        }

        return back()->with('sukses', 'Materi ditambahkan ke pertemuan '.$pertemuan->nomor.'.');
    }

    public function hapusMateri(Request $request, Materi $materi): RedirectResponse
    {
        $this->kelasKelola($request, $materi->pertemuan->kelas_id);

        if ($materi->path) {
            Storage::disk('local')->delete($materi->path);
        }

        $materi->delete();

        return back()->with('sukses', 'Materi dihapus.');
    }
}
