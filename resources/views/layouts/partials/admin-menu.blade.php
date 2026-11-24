@php
    $isPengajuanOpen = request()->is(
        'admin/pengajuan*',
        'admin/klaim-pembelian*',
        'admin/lpj*',
        'admin/mutasi*',
        'admin/pengajuan-mutasi*',
        'admin/pengajuan-hapus-buku*'
    );
    $isNominatifOpen = request()->is(
        'admin/peralatan*',
        'admin/inventaris*'
    );
    $isJurnalOpen = request()->is('jurnal*');
    $isMasterOpen = request()->is('admin/rekening*') || request()->is('admin/param-jabatan*');
@endphp

<li class="nav-item">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

{{-- Menu Pengajuan --}}
<li class="nav-item {{ $isPengajuanOpen ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $isPengajuanOpen ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-invoice"></i>
        <p>
            Pengajuan
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.pengajuan.index') }}"
                class="nav-link {{ request()->is('admin/pengajuan') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Uang Muka</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/klaim-pembelian') }}"
                class="nav-link {{ request()->is('admin/klaim-pembelian*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Klaim</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.lpj.index') }}" class="nav-link {{ request()->is('admin/lpj*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>LPJ</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.pengajuan-mutasi.index') }}"
                class="nav-link {{ request()->is('admin/mutasi*') || request()->is('admin/pengajuan-mutasi') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Mutasi</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.pengajuan-mutasi-gol-2.index') }}"
                class="nav-link {{ request()->is('admin/mutasi-gol-2*') || request()->is('admin/pengajuan-mutasi-gol-2*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Mutasi Gol 2</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.pengajuan-hapus-buku.index') }}"
                class="nav-link {{ request()->is('admin/pengajuan-hapus-buku') || (request()->is('admin/pengajuan-hapus-buku/*') && !request()->is('admin/pengajuan-hapus-buku-gol-2*')) ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Hapus Buku</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.pengajuan-hapus-buku-gol-2.index') }}"
                class="nav-link {{ request()->is('admin/pengajuan-hapus-buku-gol-2*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Hapus Buku Gol 2</p>
            </a>
        </li>
    </ul>
</li>

{{-- Menu Nominatif --}}
<li class="nav-item {{ $isNominatifOpen ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $isNominatifOpen ? 'active' : '' }}">
        <i class="nav-icon fas fa-boxes"></i>
        <p>
            Nominatif
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.peralatan.index') }}"
                class="nav-link {{ request()->is('admin/peralatan*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Peralatan</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/inventaris') }}"
                class="nav-link {{ request()->is('admin/inventaris') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Inventaris</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/inventaris-gol-2') }}"
                class="nav-link {{ request()->is('admin/inventaris-gol-2*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Inventaris Gol II</p>
            </a>
        </li>
    </ul>
</li>

{{-- Menu Jurnal --}}
<li class="nav-item {{ $isJurnalOpen ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $isJurnalOpen ? 'active' : '' }}">
        <i class="nav-icon fas fa-book"></i>
        <p>
            Jurnal
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('jurnal-kp') }}" class="nav-link {{ request()->is('jurnal-kp*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Jurnal Pembelian KP</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-ap') }}" class="nav-link {{ request()->is('jurnal-ap*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Jurnal Pembelian AP</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-kp') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-kp*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Jurnal Penyusutan KP</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-ap') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-ap*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Jurnal Penyusutan AP</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-kp') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-kp*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Jurnal Pembebanan KP</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-ap') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-ap*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Jurnal Pembebanan AP</p>
            </a>
        </li>
    </ul>
</li>

{{-- Menu Data Master --}}
<li class="nav-item {{ $isMasterOpen ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $isMasterOpen ? 'active' : '' }}">
        <i class="nav-icon fas fa-database"></i>
        <p>
            Data Master
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('admin/rekening') }}"
                class="nav-link {{ request()->is('admin/rekening*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Daftar Rekening</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('admin/param-jabatan') }}"
                class="nav-link {{ request()->is('admin/param-jabatan*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Parameter Jabatan</p>
            </a>
        </li>
    </ul>
</li>

{{-- Menu Tambahan --}}
<li class="nav-item">
    <a href="#" class="nav-link {{ request()->is('admin/stock-opname*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-clipboard-check"></i>
        <p>Stock Opname</p>
    </a>
</li>

<li class="nav-item">
    <a href="#" class="nav-link {{ request()->is('admin/data-existing*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-archive"></i>
        <p>Data Existing</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('/update-password') }}"
        class="nav-link {{ request()->is('update-password*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-key"></i>
        <p>Ganti Password</p>
    </a>
</li>
