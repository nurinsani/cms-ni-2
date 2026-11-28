<li class="nav-item">
    <a href="{{ route('finance.dashboard') }}" class="nav-link {{ request()->is('finance/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>

<li class="nav-item has-treeview {{ request()->is('atasan-akunting/pengajuan*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('atasan-akunting/pengajuan*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>
            Pengajuan
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('atasan-akunting/pengajuan/peralatan') }}"
                class="nav-link {{ request()->is('atasan-akunting/pengajuan/peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('atasan-akunting/pengajuan/inventaris') }}"
                class="nav-link {{ request()->is('atasan-akunting/pengajuan/inventaris*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris
                </p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item has-treeview {{ request()->is('atasan-akunting/approval*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('atasan-akunting/approval*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-check-double"></i>
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
                class="nav-link {{ request()->is('atasan-akunting/approval-lpj*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ url('/update-password') }}" class="nav-link {{ request()->is('update-password*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-lock"></i>
        <p>
            Ganti Password
        </p>
    </a>
</li>
