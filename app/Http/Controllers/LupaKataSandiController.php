<?php

namespace App\Http\Controllers;

use App\Models\PermintaanResetKataSandi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LupaKataSandiController extends Controller
{
    public function form(): View
    {
        return view('lupa-kata-sandi');
    }

    public function kirim(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);

        $pengguna = User::where('email', $data['email'])->first();

        if ($pengguna && ! PermintaanResetKataSandi::where('user_id', $pengguna->id)->where('status', 'menunggu')->exists()) {
            PermintaanResetKataSandi::create(['user_id' => $pengguna->id]);
        }

        // Jawaban selalu sama, ada atau tidaknya akun, agar daftar email terdaftar tidak bisa ditebak.
        return redirect()->route('lupa.form')->with('sukses', 'Jika email terdaftar, permintaan Anda sudah diteruskan ke admin. Admin akan mengatur ulang kata sandi Anda.');
    }
}
