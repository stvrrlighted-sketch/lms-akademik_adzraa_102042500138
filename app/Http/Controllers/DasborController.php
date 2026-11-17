<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\PermintaanResetKataSandi;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DasborController extends Controller
{
    private function daftarBerita(User $pengguna)
    {
        return Berita::dapatDilihatOleh($pengguna)->orderByDesc('tanggal_terbit')->orderByDesc('id')->take(12)->get();
    }

    public function admin(Request $request): View
    {
        return view('dasbor.admin', [
            'daftarBerita' => $this->daftarBerita($request->user()),
            'jumlahPerPeran' => User::selectRaw('role, count(*) as jumlah')->groupBy('role')->pluck('jumlah', 'role'),
            'jumlahKelas' => Kelas::count(),
            'jumlahMataKuliah' => MataKuliah::count(),
            'jumlahPermintaan' => PermintaanResetKataSandi::where('status', 'menunggu')->count(),
        ]);
    }

    public function dosen(Request $request): View
    {
        $tahunAktif = TahunAjaran::where('aktif', true)->first();

        $daftarKelas = Kelas::dapatDikelolaOleh($request->user())
            ->with(['mataKuliah', 'tahunAjaran'])
            ->withCount('mahasiswa')
            ->when($tahunAktif, fn ($kueri) => $kueri->where('tahun_ajaran_id', $tahunAktif->id))
            ->get();

        return view('dasbor.dosen', [
            'daftarBerita' => $this->daftarBerita($request->user()),
            'daftarKelas' => $daftarKelas,
            'tahunAktif' => $tahunAktif,
        ]);
    }

    public function mahasiswa(Request $request): View
    {
        $pengguna = $request->user();
        $daftarKelas = $pengguna->kelasDiikuti()->with(['mataKuliah', 'tahunAjaran', 'dosen'])->get();

        $tugasMendatang = Tugas::whereIn('kelas_id', $daftarKelas->pluck('id'))
            ->where('tenggat', '>=', now())
            ->whereDoesntHave('pengumpulan', fn ($kueri) => $kueri->where('mahasiswa_id', $pengguna->id))
            ->with('kelas.mataKuliah')
            ->orderBy('tenggat')
            ->take(8)
            ->get();

        return view('dasbor.mahasiswa', [
            'daftarBerita' => $this->daftarBerita($request->user()),
            'daftarKelas' => $daftarKelas,
            'tugasMendatang' => $tugasMendatang,
        ]);
    }
}
