<li class="nav-item menu-open">
<li class="nav-item menu-open">
    <a href="{{ route('finance.dashboard') }}" class="nav-link {{ request()->is('finance/dashboard*') ? 'active' : '' }}">
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
            <a href="{{ url('atasan-akunting/pengajuan/peralatan') }}"
                class="nav-link {{ request()->is('admin/pengajuan') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
                {{-- <span class="badge badge-info right">{{ $pengajuan_peralatan->count() }}</span> --}}
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('atasan-akunting/pengajuan/inventaris') }}"
                class="nav-link {{ request()->is('admin/pengajuan') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris
                </p>
                {{-- <span class="badge badge-info right">{{ $pengajuan_peralatan->count() }}</span> --}}
            </a>
        </li>

    </ul>
</li>

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
            <a href="{{ url('atasan-akunting/approval-peralatan') }}"
                class="nav-link {{ request()->is('atasan-akunting/approval-peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('atasan-akunting/approval-lpj') }}"
                class="nav-link {{ request()->is('atasab-akunting/approval-lpj*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
            </a>
        </li>
    </ul>
</li>
