@php
    $isApprovalOpen = request()->is(
        'atasan-it/approval-peralatan*',
        'atasan-it/approval-lpj*',
        'atasan-it/klaim-pembelian*',
        'atasan-it/klaim*',
    );
@endphp

<li class="nav-item">
    <a href="{{ route('atasan-it.dashboard') }}"
        class="nav-link {{ request()->is('atasan-it/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Dashboard
        </p>
    </a>
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
            <a href="{{ url('atasan-it/approval-peralatan') }}"
                class="nav-link {{ request()->is('atasan-it/approval-peralatan*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('atasan-it/approval-lpj') }}"
                class="nav-link {{ request()->is('atasan-it/approval-lpj*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('atasan-it/klaim-pembelian') }}"
                class="nav-link {{ request()->is('atasan-it/klaim-pembelian*', 'atasan-it/klaim*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>

    </ul>
</li>

<li class="nav-item">
    <a href="{{ url('/update-password') }}" class="nav-link {{ request()->is('update-password*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-fingerprint"></i>
        <p>
            Ganti Password
        </p>
    </a>
</li>
