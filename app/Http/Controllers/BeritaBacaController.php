<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BeritaBacaController extends Controller
{
    public function tampil(Request $request, int $id): View
    {
        // 404 untuk berita yang belum terbit atau sudah kedaluwarsa bagi mahasiswa, agar keberadaannya tidak terungkap.
        $berita = Berita::dapatDilihatOleh($request->user())->with('pembuat')->findOrFail($id);

        return view('berita.tampil', compact('berita'));
    }
}
