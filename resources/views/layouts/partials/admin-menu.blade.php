<li class="nav-item">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ url('/update-password') }}" class="nav-link {{ request()->is('/update-password') ? 'active' : '' }}">
        <i class="fas fa-fingerprint"></i>
        <p>
            Ganti Password
        </p>
    </a>
</li>

{{-- menu pengajuan --}}
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-folder-plus"></i>
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
                    Peralatan & Inventaris
                </p>
                {{-- <span class="badge badge-info right">{{ $pengajuan_peralatan->count() }}</span> --}}
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/klaim-pembelian') }}"
                class="nav-link {{ request()->is('admin/klaim-pembelian') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
                {{-- <span class="badge badge-info right">{{ $pengajuan_peralatan->count() }}</span> --}}
            </a>
        </li>

        <li class="nav-item">
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
        </li>

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
            <a href="#" class="nav-link {{ request()->is('admin/pengajuan-hapus-buku*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Hapus Buku
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link {{ request()->is('admin/pengajuan-penjualan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Penjualan
                </p>
            </a>
        </li>

    </ul>
</li>
{{-- end menu pengajuan --}}

{{-- menu LPJ --}}
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>
            LPJ
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.lpj.index') }}"
                class="nav-link {{ request()->is('admin/lpj*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.lpj-bdd.index') }}"
                class="nav-link {{ request()->is('admin/pengajuan-lpj-bdd*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    BDD
                </p>
            </a>
        </li>
    </ul>
</li>
{{-- end menu LPJ --}}

<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-clipboard-list"></i>
        <p>
            Nominative
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
                class="nav-link {{ request()->is('admin/inventaris*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.daftar-bdd') }}"
                class="nav-link {{ request()->is('admin/daftar-bdd*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    BDD
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
            <a href="{{ url('admin/jurnal-kp') }}"
                class="nav-link {{ request()->is('admin/jurnal-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/jurnal-ap') }}"
                class="nav-link {{ request()->is('admin/jurnal-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.jurnal-pembelian.index') }}"
                class="nav-link {{ request()->is('admin/jurnal-pembelian*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Pembelian Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.jurnal-bdd.index') }}"
                class="nav-link {{ request()->is('admin/jurnal-bdd*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    BDD
                </p>
            </a>
        </li>
    </ul>
</li>


{{-- <li class="nav-header">Menu Inventaris</li> --}}

<li class="nav-item">
    <a href="{{ url('admin/rekening') }}" class="nav-link {{ request()->is('admin/rekening*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-credit-card"></i>
        <p>
            Daftar Rekening
        </p>
    </a>
</li>

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
