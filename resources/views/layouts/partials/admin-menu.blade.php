<li class="nav-item">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>

{{-- menu pengajuan --}}
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="fas fa-file-signature nav-icon"></i>

        <p>
            Pengajuan
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">

        @php
            $pengajuan_peralatan = App\Models\Admin\Pengajuan::all();
            $pengajuan_bdd = App\Models\Admin\PengajuanBdd::all();
        @endphp

        <li class="nav-item">
            <a href="{{ route('admin.pengajuan.index') }}"
                class="nav-link {{ request()->is('admin/pengajuan') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Uang Muka
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/klaim-pembelian') }}"
                class="nav-link {{ request()->is('admin/klaim-pembelian') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.lpj.index') }}" class="nav-link {{ request()->is('admin/lpj*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
            </a>
        </li>

        {{-- <li class="nav-item">
            <a href="{{ route('admin.pengajuan-bdd.index') }}" class="nav-link">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    BDD
                </p>
                <span class="badge badge-info right">{{ $pengajuan_bdd->count() }}</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/pemutusan-bdd') }}"
                class="nav-link {{ request()->is('admin/pemutusan-bdd*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Pemutusan BDD
                </p>
            </a>
        </li> --}}

        <li class="nav-item">
            <a href="{{ route('admin.pengajuan-mutasi.index') }}"
                class="nav-link {{ request()->is('admin/mutasi*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Mutasi
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.pengajuan-mutasi-gol-2.index') }}"
                class="nav-link {{ request()->is('admin/mutasi-gol-2*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Mutasi Gol 2
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.pengajuan-hapus-buku.index') }}" class="nav-link {{ request()->is('admin/pengajuan-hapus-buku') || (request()->is('admin/pengajuan-hapus-buku/*') && !request()->is('admin/pengajuan-hapus-buku-gol-2*')) ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Hapus Buku
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.pengajuan-hapus-buku-gol-2.index') }}" class="nav-link {{ request()->is('admin/pengajuan-hapus-buku-gol-2*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Hapus Buku Gol 2
                </p>
            </a>
        </li>

        {{-- <li class="nav-item">
            <a href="#" class="nav-link {{ request()->is('admin/pengajuan-penjualan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Penjualan
                </p>
            </a>
        </li> --}}

    </ul>
</li>
{{-- end menu pengajuan --}}

<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="fas fa-list-alt nav-icon"></i>
        <p>
            Nominatif
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.peralatan.index') }}"
                class="nav-link {{ request()->is('admin/peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/inventaris') }}"
                class="nav-link {{ request()->is('admin/inventaris') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/inventaris-gol-2') }}"
                class="nav-link {{ request()->is('admin/inventaris-gol-2') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris Gol II
                </p>
            </a>
        </li>
    </ul>
</li>


<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-file-invoice"></i>
        <p>
            Jurnal
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">

        <li class="nav-item">
            <a href="{{ url('jurnal-kp') }}" class="nav-link {{ request()->is('jurnal-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembelian KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-ap') }}" class="nav-link {{ request()->is('jurnal-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembelian AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-kp') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-ap') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-kp') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-ap') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan AP
                </p>
            </a>
        </li>

    </ul>
</li>

<li class="nav-item">
    <a href="#" class="nav-link">
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
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Daftar Rekening
                </p>
            </a>
        </li>
    </ul>
</li>

{{-- <li class="nav-header">Menu Inventaris</li> --}}


<li class="nav-item">
    <a href="#" class="nav-link {{ request()->is('admin/stock-opname*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Stock Opname
        </p>
    </a>
</li>

<li class="nav-item">
    <a href="#" class="nav-link {{ request()->is('admin/data-existing*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Data Exsisting
        </p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('/update-password') }}"
        class="nav-link {{ request()->is('/update-password') ? 'active' : '' }}">
        <i class="nav-icon fas fa-lock"></i>
        <p>
            Ganti Password
        </p>
    </a>
</li>
