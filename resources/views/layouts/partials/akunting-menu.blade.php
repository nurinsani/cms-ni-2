<li class="nav-item">
    <a href="{{ route('akunting.dashboard') }}"
        class="nav-link {{ request()->is('akunting/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('/update-password') }}" class="nav-link {{ request()->is('update-password') ? 'active' : '' }}">
        <i class="nav-icon fas fa-fingerprint"></i>
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
            <a href="{{ url('akunting/approval-peralatan') }}"
                class="nav-link {{ request()->is('akunting/approval-peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/approval-lpj') }}"
                class="nav-link {{ request()->is('akunting/approval-lpj*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    LPJ
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/klaim-pembelian') }}"
                class="nav-link {{ request()->is('akunting/klaim-pembelian*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/approval-bdd') }}"
                class="nav-link {{ request()->is('akunting/approval-bdd*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    BDD
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/approval-mutasi') }}"
                class="nav-link {{ request()->is('akunting/approval-mutasi*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Mutasi
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
            <a href="{{ url('akunting/peralatan') }}"
                class="nav-link {{ request()->is('akunting/peralatan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Peralatan
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/inventaris') }}"
                class="nav-link {{ request()->is('akunting/inventaris*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris
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
            <a href="{{ url('penyusutan') }}"
                class="nav-link {{ request()->is('penyusutan*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Proses Penyusutan
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('jurnal-kp') }}"
                class="nav-link {{ request()->is('jurnal-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembelian KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-ap') }}"
                class="nav-link {{ request()->is('jurnal-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembelian AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-kp') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-ap') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-kp') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-ap') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan AP
                </p>
            </a>
        </li>
    </ul>
</li>
{{-- end menu LPJ --}}

<li class="nav-header">Menu Master</li>
<li class="nav-item">
    <a href="{{ route('akunting.param-peralatan.index') }}"
        class="nav-link {{ request()->is('akunting/param-peralatan*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Parameter Peralatan
        </p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('akunting.jenis-peralatan.index') }}"
        class="nav-link {{ request()->is('akunting/jenis-peralatan*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Jenis Peralatan
        </p>
    </a>
</li>
