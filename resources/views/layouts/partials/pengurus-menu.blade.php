<li class="nav-item menu-open">
    <a href="{{ route('pengurus.dashboard') }}"
        class="nav-link {{ request()->is('pengurus/dashboard*') ? 'active' : '' }}">
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
            Approval
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('pengurus/approval-peralatan') }}"
                class="nav-link {{ request()->is('pengurus/approval-peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('pengurus/approval-lpj') }}"
                class="nav-link {{ request()->is('pengurus/approval-lpj *') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('pengurus/klaim-pembelian') }}"
                class="nav-link {{ request()->is('pengurus/klaim-pembelian *') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('pengurus/approval-bdd') }}"
                class="nav-link {{ request()->is('pengurus/approval-bdd*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    BDD
                </p>
            </a>
        </li>

    </ul>
</li>

    <li class="nav-item">
        <a href="{{ url('/update-password') }}" class="nav-link {{ request()->is('/update-password') ? 'active' : '' }}">
            <i class="nav-icon fas fa-key"></i>
            <p>
                Ganti Password
            </p>
        </a>
    </li>
