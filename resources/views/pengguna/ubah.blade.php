@extends('tata-letak')

@section('judul', 'Ubah Pengguna')

@section('isi')
    <div class="kepala"><h1>Ubah Pengguna</h1></div>

    <form class="formulir" method="POST" action="{{ route('pengguna.perbarui', $pengguna) }}" novalidate>
        @csrf
        @method('PUT')

        <p><strong>Email:</strong> {{ $pengguna->email }}</p>
        <p class="petunjuk">Mengubah peran ikut mengubah domain email (username tetap).</p>

        <div class="kolom">
            <label for="name">Nama</label>
            <input id="name" name="name" type="text" value="{{ old('name', $pengguna->name) }}" required
                   @error('name') aria-invalid="true" aria-describedby="galat-name" @enderror>
            @include('partials.galat', ['nama' => 'name'])
        </div>

        <div class="kolom">
            <label for="role">Peran</label>
            <select id="role" name="role" required
                    @error('role') aria-invalid="true" aria-describedby="galat-role" @enderror>
                @foreach (\App\Models\User::DAFTAR_PERAN as $peran)
                    <option value="{{ $peran }}" @selected(old('role', $pengguna->role) === $peran)>{{ $peran }}</option>
                @endforeach
            </select>
            @include('partials.galat', ['nama' => 'role'])
        </div>

        <div class="kolom">
            <label for="password">Kata sandi baru (reset)</label>
            <input id="password" name="password" type="text" autocomplete="off"
                   @error('password') aria-invalid="true" aria-describedby="galat-password" @enderror>
            <p class="petunjuk">Kosongkan jika tidak ingin mengubah kata sandi. Minimal 8 karakter. Sampaikan kata sandi baru kepada pemilik akun.</p>
            @include('partials.galat', ['nama' => 'password'])
        </div>

        <div class="aksi">
            <button type="submit">Simpan</button>
            <a class="tombol sekunder" href="{{ route('pengguna.kelola') }}">Batal</a>
        </div>
    </form>
@endsection
