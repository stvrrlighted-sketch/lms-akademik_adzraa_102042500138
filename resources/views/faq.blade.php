@extends('tata-letak-publik')

@section('judul', 'FAQ')

@section('isi')
    <div class="kartu masuk-kotak kotak-lebar">
        <h1>FAQ</h1>

        <div class="faq">
            <details name="faq">
                <summary>Bagaimana cara mendapatkan akun?</summary>
                <p>Akun dibuat oleh admin. Hubungi admin untuk mendapatkan email dan kata sandi awal Anda.</p>
            </details>
            <details name="faq">
                <summary>Saya lupa kata sandi. Apa yang harus dilakukan?</summary>
                <p>Buka halaman Lupa kata sandi dan masukkan email akun Anda. Admin akan mengatur ulang kata sandi Anda.</p>
            </details>
            <details name="faq">
                <summary>Bagaimana mengganti bahasa atau tema?</summary>
                <p>Gunakan ikon bahasa dan ikon tema di bilah navigasi. Pilihan Anda tersimpan di browser ini.</p>
            </details>
            <details name="faq">
                <summary>Mengapa muncul pesan "Anda tidak memiliki hak akses!"?</summary>
                <p>Peran akun Anda tidak diizinkan membuka halaman tersebut. Kembali ke dasbor dan pakai menu yang tersedia.</p>
            </details>
            <details name="faq">
                <summary>Saya masih punya pertanyaan lain.</summary>
                <p>Silakan chat admin.</p>
            </details>
        </div>
    </div>
@endsection
