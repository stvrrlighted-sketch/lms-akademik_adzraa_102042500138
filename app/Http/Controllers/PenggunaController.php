<?php

namespace App\Http\Controllers;

use App\Models\PermintaanResetKataSandi;
use App\Models\User;
use App\Services\PembuatAkun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(Request $request): View
    {
        $kata = trim((string) $request->query('q'));
        $peran = $request->query('peran');

        $daftarPengguna = User::query()
            ->when($kata !== '', fn ($kueri) => $kueri->where(
                fn ($sub) => $sub->where('name', 'like', "%{$kata}%")->orWhere('email', 'like', "%{$kata}%")
            ))
            ->when(in_array($peran, User::DAFTAR_PERAN, true), fn ($kueri) => $kueri->where('role', $peran))
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('pengguna.indeks', [
            'daftarPengguna' => $daftarPengguna,
            'kata' => $kata,
            'peranDipilih' => $peran,
            'permintaanReset' => PermintaanResetKataSandi::with('user')->where('status', 'menunggu')->latest()->get(),
        ]);
    }

    public function buat(): View
    {
        return view('pengguna.buat');
    }

    public function simpan(Request $request, PembuatAkun $pembuat): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(User::DAFTAR_PERAN)],
            'baris' => ['required', 'array', 'max:200'],
            'baris.*.nama' => ['nullable', 'string', 'max:255'],
            'baris.*.username' => ['nullable', 'string', 'max:60', 'regex:/^[A-Za-z0-9._-]+$/'],
            'baris.*.password' => ['nullable', 'string', 'min:8', 'max:100'],
        ], [
            'baris.*.username.regex' => __('Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.'),
        ], [
            'baris.*.nama' => __('Nama'),
            'baris.*.username' => __('Username'),
            'baris.*.password' => __('Kata sandi'),
        ]);

        $hasil = $pembuat->buatBanyak($data['baris'], $data['role']);

        if ($hasil === []) {
            return back()->withErrors(['baris' => 'Isi minimal satu nama.'])->withInput();
        }

        $jumlahDibuat = count(array_filter($hasil, fn ($baris) => $baris['status'] === 'Dibuat'));

        return redirect()->route('pengguna.kelola')
            ->with('hasilAkun', $hasil)
            ->with('sukses', 'Akun dibuat: '.$jumlahDibuat);
    }

    public function ubah(User $pengguna): View
    {
        return view('pengguna.ubah', compact('pengguna'));
    }

    public function perbarui(Request $request, User $pengguna): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(User::DAFTAR_PERAN)],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
        ]);

        $galat = $this->terapkanPeran($request, $pengguna, $data['role']);

        if ($galat !== null) {
            return back()->withErrors(['role' => $galat])->withInput();
        }

        $pengguna->name = $data['name'];

        if (! empty($data['password'])) {
            $pengguna->password = $data['password'];
        }

        $pengguna->save();

        if (! empty($data['password'])) {
            PermintaanResetKataSandi::where('user_id', $pengguna->id)->where('status', 'menunggu')->update(['status' => 'selesai']);
        }

        return redirect()->route('pengguna.kelola')->with('sukses', 'Pengguna diperbarui. Email: '.$pengguna->email);
    }

    public function ubahPeran(Request $request, User $pengguna): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', Rule::in(User::DAFTAR_PERAN)]]);

        $galat = $this->terapkanPeran($request, $pengguna, $data['role']);

        if ($galat !== null) {
            return back()->with('galat', $galat);
        }

        $pengguna->save();

        return back()->with('sukses', 'Peran diubah. Email sekarang: '.$pengguna->email);
    }

    public function hapus(Request $request, User $pengguna): RedirectResponse
    {
        // Akun yang sedang dipakai tidak boleh dihapus, supaya admin tidak mengunci dirinya sendiri.
        if ($pengguna->is($request->user())) {
            return back()->with('galat', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($pengguna->kelasDiampu()->exists()) {
            return back()->with('galat', 'Dosen yang masih mengampu kelas tidak dapat dihapus.');
        }

        $pengguna->delete();

        return redirect()->route('pengguna.kelola')->with('sukses', 'Pengguna dihapus.');
    }

    public function abaikanPermintaan(PermintaanResetKataSandi $permintaan): RedirectResponse
    {
        $permintaan->update(['status' => 'diabaikan']);

        return back()->with('sukses', 'Permintaan diabaikan.');
    }

    private function terapkanPeran(Request $request, User $pengguna, string $peranBaru): ?string
    {
        if ($peranBaru === $pengguna->role) {
            return null;
        }

        // Admin tidak boleh mengubah peran akunnya sendiri agar sistem tidak kehilangan admin.
        if ($pengguna->is($request->user())) {
            return 'Anda tidak dapat mengubah peran akun Anda sendiri.';
        }

        if ($pengguna->role === 'dosen' && $pengguna->kelasDiampu()->exists()) {
            return 'Dosen yang masih mengampu kelas tidak dapat diubah perannya.';
        }

        $emailBaru = User::buatEmail($pengguna->username(), $peranBaru);

        if (User::where('email', $emailBaru)->whereKeyNot($pengguna->id)->exists()) {
            return 'Email '.$emailBaru.' sudah dipakai akun lain.';
        }

        $pengguna->role = $peranBaru;
        $pengguna->email = $emailBaru;

        return null;
    }
}
