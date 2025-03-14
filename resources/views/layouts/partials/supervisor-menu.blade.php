<li class="nav-item menu-open">
    <a href="{{ route('admin-head.dashboard') }}"
        class="nav-link {{ request()->is('supervisor/dashboard*') ? 'active' : '' }}">
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

<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>
            Pengajuan
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">

        <li class="nav-item">
            <a href="{{ url('admin-head/pengajuan') }}"
                class="nav-link {{ request()->is('admin/pengajuan') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Uang Muka
                </p>
                {{-- <span class="badge badge-info right">{{ $pengajuan_peralatan->count() }}</span> --}}
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/pengajuan/klaim') }}"
                class="nav-link {{ request()->is('admin/pengajuan') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
                {{-- <span class="badge badge-info right">{{ $pengajuan_peralatan->count() }}</span> --}}
            </a>
        </li>

    </ul>
</li>

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
            <a href="{{ url('admin-head/lpj') }}"
                class="nav-link {{ request()->is('admin-head/lpj*') ? 'active' : '' }}">
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
        <i class="nav-icon far fa-check-circle"></i>
        <p>
            Approval
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('admin-head/pengajuan-peralatan') }}"
                class="nav-link {{ request()->is('admin-head/pengajuan-peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/approval-lpj') }}"
                class="nav-link {{ request()->is('admin-head/approval-lpj*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/klaim-pembelian') }}"
                class="nav-link {{ request()->is('admin-head/klaim-pembelian*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/pengajuan-bdd') }}"
                class="nav-link {{ request()->is('supervisor/pengajuan-bdd*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    BDD
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/approval-mutasi') }}"
                class="nav-link {{ request()->is('supervisor/pengajuan-mutasi*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Mutasi
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link {{ request()->is('supervisor/pengajuan-hapus-buku*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Hapus Buku
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link {{ request()->is('supervisor/pengajuan-penjualan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Penjualan
                </p>
            </a>
        </li>

    </ul>
</li>

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
            <a href="{{ url('admin-head/peralatan') }}"
                class="nav-link {{ request()->is('admin-head/peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/inventaris') }}"
                class="nav-link {{ request()->is('admin-head/inventaris') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/inventaris-gol-2') }}"
                class="nav-link {{ request()->is('admin-head/inventaris-gol-2') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris Gol II
                </p>
            </a>
        </li>
    </ul>
</li>

{{-- menu LPJ --}}
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>
            Transaksi
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('admin-head/jurnal-kp') }}"
                class="nav-link {{ request()->is('admin-head/jurnal-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/jurnal-ap') }}"
                class="nav-link {{ request()->is('admin-head/jurnal-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/jurnal-penyusutan-kp') }}"
                class="nav-link {{ request()->is('akunting/jurnal-penyusutan-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/jurnal-penyusutan-ap') }}"
                class="nav-link {{ request()->is('akunting/jurnal-penyusutan-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/jurnal-pembebanan-kp') }}"
                class="nav-link {{ request()->is('akunting/jurnal-pembebanan-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/jurnal-pembebanan-ap') }}"
                class="nav-link {{ request()->is('akunting/jurnal-pembebanan-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan Ap
                </p>
            </a>
        </li>
        
    </ul>
</li>
{{-- end menu LPJ --}}


{{-- <li class="nav-item">
  <a href="#" class="nav-link {{ (request()->is('supervisor/stock-opname*')) ? 'active' : '' }}">
    <i class="nav-icon fas fa-database"></i>
    <p>
      Stock Opname
    </p>
  </a>
</li> --}}
