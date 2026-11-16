@php
    $isPengajuanOpen = request()->is('it/pengajuan', 'it/pengajuan/*', 'it/lpj*', 'it/klaim*', 'it/peralatan/klaim*', 'it/inventaris/klaim*');
    $isApprovalOpen = request()->is('it/pengajuan-peralatan*', 'it/pengajuan-detail*', 'it/pengajuan-bdd*');
@endphp

<li class="nav-item">
    <a href="{{ route('it.dashboard') }}" class="nav-link {{ request()->is('it/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('/update-password') }}" class="nav-link {{ request()->is('update-password*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-key"></i>
        <p>Ganti Password</p>
    </a>
</li>

<li class="nav-item {{ $isPengajuanOpen ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $isPengajuanOpen ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-invoice"></i>
        <p>
            Pengajuan
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('it/pengajuan') }}" class="nav-link {{ request()->is('it/pengajuan', 'it/pengajuan/*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Uang Muka</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('it/lpj') }}" class="nav-link {{ request()->is('it/lpj*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>LPJ</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('it/klaim') }}" class="nav-link {{ request()->is('it/klaim*', 'it/peralatan/klaim*', 'it/inventaris/klaim*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Klaim</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item {{ $isApprovalOpen ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $isApprovalOpen ? 'active' : '' }}">
        <i class="nav-icon fas fa-check-double"></i>
        <p>
            Approval
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('it.pengajuan_peralatan') }}"
                class="nav-link {{ request()->is('it/pengajuan-peralatan*', 'it/pengajuan-detail*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Peralatan</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('it.pengajuan_bdd') }}"
                class="nav-link {{ request()->is('it/pengajuan-bdd*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>BDD</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-header text-uppercase text-xs text-muted">Menu Master</li>
<li class="nav-item">
    <a href="{{ route('it.data-user.index') }}" class="nav-link {{ request()->is('it/data-user*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users-cog"></i>
        <p>Data User</p>
    </a>
</li>
