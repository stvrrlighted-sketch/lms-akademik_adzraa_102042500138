<nav aria-label="Menu kelas">
    <ul class="sub-menu">
        <li><a href="{{ route('kelola.tampil', $kelas) }}" @if ($aktif === 'ringkasan') aria-current="page" @endif>Ringkasan</a></li>
        <li><a href="{{ route('materi.indeks', $kelas) }}" @if ($aktif === 'materi') aria-current="page" @endif>Materi</a></li>
        <li><a href="{{ route('tugas.indeks', $kelas) }}" @if ($aktif === 'tugas') aria-current="page" @endif>Tugas</a></li>
        <li><a href="{{ route('kuis.indeks', $kelas) }}" @if ($aktif === 'kuis') aria-current="page" @endif>Kuis</a></li>
        <li><a href="{{ route('presensi.indeks', $kelas) }}" @if ($aktif === 'presensi') aria-current="page" @endif>Presensi</a></li>
        <li><a href="{{ route('nilai.indeks', $kelas) }}" @if ($aktif === 'nilai') aria-current="page" @endif>Nilai</a></li>
    </ul>
</nav>
