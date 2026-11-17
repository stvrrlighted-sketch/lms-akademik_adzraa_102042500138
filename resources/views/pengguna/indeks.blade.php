@extends('tata-letak')

@section('judul', 'Manajemen Pengguna')

@section('isi')
    <div class="kepala">
        <h1>Manajemen Pengguna</h1>
        <a class="tombol" href="{{ route('pengguna.buat') }}">Tambah akun</a>
    </div>

    @if (session('hasilAkun'))
        <section aria-labelledby="judul-hasil">
            <h2 id="judul-hasil">Hasil pembuatan akun</h2>
            <p class="info bahaya" role="status">Catat kata sandi di bawah sekarang. Kata sandi tidak dapat ditampilkan lagi setelah halaman ini ditutup.</p>
            <div class="bungkus-tabel">
                <table>
                    <caption>Akun yang dibuat</caption>
                    <thead>
                        <tr><th scope="col">Nama</th><th scope="col">Email</th><th scope="col">Kata sandi</th><th scope="col">Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach (session('hasilAkun') as $hasil)
                            <tr>
                                <td>{{ $hasil['nama'] }}</td>
                                <td>{{ $hasil['email'] }}</td>
                                <td>{{ $hasil['password'] }}</td>
                                <td>
                                    <span class="lencana {{ $hasil['status'] === 'Dibuat' ? 'ok' : 'galat' }}">{{ $hasil['status'] }}</span>
                                    {{ $hasil['alasan'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($permintaanReset->isNotEmpty())
        <section aria-labelledby="judul-reset">
            <h2 id="judul-reset">Permintaan reset kata sandi</h2>
            <div class="bungkus-tabel">
                <table>
                    <caption>Permintaan yang menunggu</caption>
                    <thead>
                        <tr><th scope="col">Pengguna</th><th scope="col">Email</th><th scope="col">Diminta</th><th scope="col">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($permintaanReset as $permintaan)
                            <tr>
                                <td>{{ $permintaan->user->name }}</td>
                                <td>{{ $permintaan->user->email }}</td>
                                <td>{{ $permintaan->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="aksi">
                                        <a class="tombol kecil" href="{{ route('pengguna.ubah', $permintaan->user) }}">Atur kata sandi</a>
                                        <form method="POST" action="{{ route('reset.abaikan', $permintaan) }}">
                                            @csrf
                                            <button type="submit" class="sekunder kecil">Abaikan</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <section aria-labelledby="judul-akun">
        <h2 id="judul-akun">Semua akun</h2>

        <form class="aksi" method="GET" action="{{ route('pengguna.kelola') }}" role="search" style="margin-bottom:1rem">
            <label class="sr-saja" for="q" style="position:absolute;left:-9999px">Cari nama atau email</label>
            <input id="q" name="q" type="search" value="{{ $kata }}" placeholder="Cari nama atau email" style="max-width:18rem">
            <label class="sr-saja" for="filter-peran" style="position:absolute;left:-9999px">Filter peran</label>
            <select id="filter-peran" name="peran" style="max-width:11rem">
                <option value="">Semua peran</option>
                @foreach (\App\Models\User::DAFTAR_PERAN as $peran)
                    <option value="{{ $peran }}" @selected($peranDipilih === $peran)>{{ $peran }}</option>
                @endforeach
            </select>
            <button type="submit">Cari</button>
        </form>

        <div class="bungkus-tabel">
            <table>
                <caption>Daftar akun</caption>
                <thead>
                    <tr><th scope="col">Nama</th><th scope="col">Email</th><th scope="col">Peran</th><th scope="col">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($daftarPengguna as $pengguna)
                        <tr>
                            <td>{{ $pengguna->name }}</td>
                            <td>{{ $pengguna->email }}</td>
                            <td>
                                @if ($pengguna->is(auth()->user()))
                                    <span class="lencana">{{ $pengguna->role }} (Anda)</span>
                                @else
                                    <form class="aksi" method="POST" action="{{ route('pengguna.peran', $pengguna) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" aria-label="Peran untuk {{ $pengguna->name }}" style="min-width:8.5rem">
                                            @foreach (\App\Models\User::DAFTAR_PERAN as $peran)
                                                <option value="{{ $peran }}" @selected($pengguna->role === $peran)>{{ $peran }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="sekunder kecil">Terapkan</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <div class="aksi">
                                    <a class="tombol sekunder kecil" href="{{ route('pengguna.ubah', $pengguna) }}">Ubah / reset kata sandi</a>
                                    @unless ($pengguna->is(auth()->user()))
                                        <form method="POST" action="{{ route('pengguna.hapus', $pengguna) }}" data-konfirmasi="Hapus akun {{ $pengguna->name }}? Data terkait akun ini ikut terhapus.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bahaya kecil">Hapus</button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="kosong">Tidak ada akun yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
