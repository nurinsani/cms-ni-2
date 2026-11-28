<li class="nav-item">
    <a href="{{ route('admin-head.dashboard') }}"
        class="nav-link {{ request()->is('admin-head/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>

<li class="nav-item has-treeview {{ request()->is('admin-head/pengajuan*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('admin-head/pengajuan*') ? 'active' : '' }}">
        <i class="fas fa-file-signature nav-icon"></i>
        <p>
            Pengajuan
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('admin-head/pengajuan') }}"
                class="nav-link {{ request()->is('admin-head/pengajuan') || (request()->is('admin-head/pengajuan/*') && !request()->is('admin-head/pengajuan/klaim*')) ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Uang Muka
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/pengajuan/klaim') }}"
                class="nav-link {{ request()->is('admin-head/pengajuan/klaim*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>
    </ul>
</li>

{{-- menu LPJ --}}
<li class="nav-item has-treeview {{ request()->is('admin-head/lpj*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('admin-head/lpj*') ? 'active' : '' }}">
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
    </ul>
</li>
{{-- end menu LPJ --}}

@php
    $isAdminHeadApprovalActive = request()->is('admin-head/pengajuan-peralatan*') ||
        request()->is('admin-head/approval-lpj*') ||
        request()->is('admin-head/klaim-pembelian*') ||
        request()->is('admin-head/approval-mutasi*') ||
        request()->is('admin-head/approval-hapus-buku*');
@endphp
<li class="nav-item has-treeview {{ $isAdminHeadApprovalActive ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $isAdminHeadApprovalActive ? 'active' : '' }}">
        <i class="nav-icon fas fa-check-double"></i>
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
            <a href="{{ url('admin-head/approval-mutasi') }}"
                class="nav-link {{ request()->is('admin-head/approval-mutasi') || (request()->is('admin-head/approval-mutasi/*') && !request()->is('admin-head/approval-mutasi-gol-2*')) ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Mutasi
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/approval-mutasi-gol-2') }}"
                class="nav-link {{ request()->is('admin-head/approval-mutasi-gol-2*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Mutasi Gol 2
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin-head.approval-hapus-buku.index') }}"
                class="nav-link {{ request()->is('admin-head/approval-hapus-buku') || (request()->is('admin-head/approval-hapus-buku/*') && !request()->is('admin-head/approval-hapus-buku-gol-2*')) ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Hapus Buku
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin-head.approval-hapus-buku-gol-2.index') }}"
                class="nav-link {{ request()->is('admin-head/approval-hapus-buku-gol-2*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Hapus Buku Gol 2
                </p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item has-treeview {{ request()->is('admin-head/peralatan*') || request()->is('admin-head/inventaris*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('admin-head/peralatan*') || request()->is('admin-head/inventaris*') ? 'active' : '' }}">
        <i class="fas fa-list-alt nav-icon"></i>
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
                class="nav-link {{ request()->is('admin-head/inventaris') || (request()->is('admin-head/inventaris/*') && !request()->is('admin-head/inventaris-gol-2*')) ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/inventaris-gol-2') }}"
                class="nav-link {{ request()->is('admin-head/inventaris-gol-2*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris Gol II
                </p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item has-treeview {{ request()->is('admin-head/jurnal*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('admin-head/jurnal*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-invoice"></i>
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
            <a href="{{ url('admin-head/jurnal-penyusutan-kp') }}"
                class="nav-link {{ request()->is('admin-head/jurnal-penyusutan-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/jurnal-penyusutan-ap') }}"
                class="nav-link {{ request()->is('admin-head/jurnal-penyusutan-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/jurnal-pembebanan-kp') }}"
                class="nav-link {{ request()->is('admin-head/jurnal-pembebanan-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin-head/jurnal-pembebanan-ap') }}"
                class="nav-link {{ request()->is('admin-head/jurnal-pembebanan-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan AP
                </p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ url('/update-password') }}"
        class="nav-link {{ request()->is('update-password*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-lock"></i>
        <p>
            Ganti Password
        </p>
    </a>
</li>
