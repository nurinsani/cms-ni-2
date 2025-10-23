<li class="nav-item menu-open">
    <a href="{{ route('finance.dashboard') }}" class="nav-link {{ request()->is('finance/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>

<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon far fa-check-square"></i>
        <p>
            Approval <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('finance/approval-peralatan') }}"
                class="nav-link {{ request()->is('finance/approval-peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('finance/approval-lpj') }}"
                class="nav-link {{ request()->is('finance/approval-lpj*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('finance/klaim-pembelian') }}"
                class="nav-link {{ request()->is('finance/klaim-pembelian*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('finance/approval-bdd') }}"
                class="nav-link {{ request()->is('finance/approval-bdd*') ? 'active' : '' }}">
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
        <i class="nav-icon fas fa-print"></i>
        <p>
            Cetak <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('finance/approval-peralatan/approve') }}" class="nav-link">
                <i class="nav-icon far fa-circle"></i>
                <p>
                    FPD
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('finance/approval-lpj/approve') }}" class="nav-link">
                <i class="nav-icon far fa-circle"></i>

                <p>
                    LPJ
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('finance/approval-klaim/approve') }}" class="nav-link">
                <i class="nav-icon far fa-circle"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ url('/update-password') }}" class="nav-link {{ request()->is('/update-password*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-key"></i>
        <p>
            Ganti Password
        </p>
    </a>
</li>
