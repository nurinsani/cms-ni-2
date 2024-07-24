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
        <i class="nav-icon fas fa-check-double"></i>
        <p>
            Approval
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('atasan-it/approval-peralatan') }}"
                class="nav-link {{ request()->is('atasan-it/approval-peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('atasan-it/approval-lpj') }}"
                class="nav-link {{ request()->is('atasab-it/approval-lpj*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('atasan-it/klaim-pembelian') }}"
                class="nav-link {{ request()->is('pengurus/klaim-pembelian *') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>

    </ul>
</li>
