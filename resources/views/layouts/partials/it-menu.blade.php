<li class="nav-item menu-open">
    <a href="{{ route('it.dashboard') }}" class="nav-link {{ request()->is('it/dashboard*') ? 'active' : '' }}">
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
            <a href="{{ url('it/pengajuan') }}" class="nav-link {{ request()->is('admin/pengajuan') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Uang Muka
                </p>
                {{-- <span class="badge badge-info right">{{ $pengajuan_peralatan->count() }}</span> --}}
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('it/lpj') }}" class="nav-link {{ request()->is('admin/pengajuan') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
                {{-- <span class="badge badge-info right">{{ $pengajuan_peralatan->count() }}</span> --}}
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('it/klaim') }}" class="nav-link {{ request()->is('admin/pengajuan') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
                {{-- <span class="badge badge-info right">{{ $pengajuan_peralatan->count() }}</span> --}}
            </a>
        </li>

    </ul>
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
            <a href="{{ route('it.pengajuan_peralatan') }}"
                class="nav-link {{ request()->is('it/pengajuan-perlatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('it.pengajuan_bdd') }}"
                class="nav-link {{ request()->is('it/pengajuan-bdd*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    BDD
                </p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-header">Menu Master</li>
<li class="nav-item">
    <a href="{{ route('it.data-user.index') }}" class="nav-link {{ request()->is('it/data-user*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user"></i>
        <p>
            User
        </p>
    </a>
</li>
