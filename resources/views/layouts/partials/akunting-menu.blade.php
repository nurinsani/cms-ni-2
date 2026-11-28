<li class="nav-item">
    <a href="{{ route('akunting.dashboard') }}"
        class="nav-link {{ request()->is('akunting/dashboard*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>

<li class="nav-item has-treeview {{ request()->is('akunting/approval*') || request()->is('akunting/klaim*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('akunting/approval*') || request()->is('akunting/klaim*') ? 'active' : '' }}">
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
                class="nav-link {{ request()->is('akunting/klaim*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Klaim
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/approval-mutasi') }}"
                class="nav-link {{ request()->is('akunting/approval-mutasi') || (request()->is('akunting/approval-mutasi/*') && !request()->is('akunting/approval-mutasi-gol-2*')) ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Mutasi
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/approval-mutasi-gol-2') }}"
                class="nav-link {{ request()->is('akunting/approval-mutasi-gol-2*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Mutasi Gol 2
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('akunting.approval-hapus-buku.index') }}"
                class="nav-link {{ request()->is('akunting/approval-hapus-buku') || (request()->is('akunting/approval-hapus-buku/*') && !request()->is('akunting/approval-hapus-buku-gol-2*')) ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Hapus Buku
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('akunting.approval-hapus-buku-gol-2.index') }}"
                class="nav-link {{ request()->is('akunting/approval-hapus-buku-gol-2*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Hapus Buku Gol 2
                </p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item has-treeview {{ request()->is('akunting/peralatan*') || request()->is('akunting/inventaris*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('akunting/peralatan*') || request()->is('akunting/inventaris*') ? 'active' : '' }}">
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
                class="nav-link {{ request()->is('akunting/inventaris') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/inventaris-gol-2') }}"
                class="nav-link {{ request()->is('akunting/inventaris-gol-2') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Inventaris Gol II
                </p>
            </a>
        </li>
    </ul>
</li>

{{-- menu Transaksi --}}
<li class="nav-item has-treeview {{ request()->is('penyusutan*') || request()->is('jurnal*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('penyusutan*') || request()->is('jurnal*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>
            Transaksi
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('penyusutan') }}" class="nav-link {{ request()->is('penyusutan') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Proses Penyusutan
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('penyusutan-gol-2') }}"
                class="nav-link {{ request()->is('penyusutan-gol-2') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Proses Penyusutan Gol II
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('jurnal-kp') }}" class="nav-link {{ request()->is('jurnal-kp*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembelian KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-ap') }}" class="nav-link {{ request()->is('jurnal-ap*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembelian AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-kp') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-kp') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-ap') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-ap') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-kp-gol-2') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-kp-gol-2') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan KP Gol 2
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-penyusutan-ap-gol-2') }}"
                class="nav-link {{ request()->is('jurnal-penyusutan-ap-gol-2') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Penyusutan AP Gol 2
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-kp') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-kp') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan KP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-ap') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-ap') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan AP
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-kp-gol-2') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-kp-gol-2') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan KP Gol 2
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('jurnal-pembebanan-ap-gol-2') }}"
                class="nav-link {{ request()->is('jurnal-pembebanan-ap-gol-2') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle nav-icon"></i>
                <p>
                    Jurnal Pembebanan AP Gol 2
                </p>
            </a>
        </li>
    </ul>
</li>
{{-- end menu Transaksi --}}

<li
    class="nav-item has-treeview {{ request()->is('akunting/param-peralatan*') || request()->is('akunting/jenis-peralatan*') || request()->is('akunting/param-jabatan*') ? 'menu-open' : '' }}">
    <a href="#"
        class="nav-link {{ request()->is('akunting/param-peralatan*') || request()->is('akunting/jenis-peralatan*') || request()->is('akunting/param-jabatan*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tools"></i>
        <p>
            Master Data
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('akunting.param-peralatan.index') }}"
                class="nav-link {{ request()->is('akunting/param-peralatan*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Parameter Peralatan</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('akunting.jenis-peralatan.index') }}"
                class="nav-link {{ request()->is('akunting/jenis-peralatan*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Jenis Peralatan</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('akunting/param-jabatan') }}"
                class="nav-link {{ request()->is('akunting/param-jabatan*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Parameter Jabatan</p>
            </a>
        </li>
    </ul>
</li>

<li
    class="nav-item has-treeview {{ request()->is('akunting/log-penghapusan-barang*') || request()->is('log-penghapusan-barang*') ? 'menu-open' : '' }}">
    <a href="#"
        class="nav-link {{ request()->is('akunting/log-penghapusan-barang*') || request()->is('log-penghapusan-barang*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-trash-alt"></i>
        <p>
            Log Hapus Buku
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('akunting.log-penghapusan-barang.index', ['gol' => '1']) }}"
                class="nav-link {{ (request()->is('akunting/log-penghapusan-barang*') || request()->is('log-penghapusan-barang*')) && request('gol', '1') == '1' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>
                    Inventaris Gol 1
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('akunting.log-penghapusan-barang.index', ['gol' => '2']) }}"
                class="nav-link {{ (request()->is('akunting/log-penghapusan-barang*') || request()->is('log-penghapusan-barang*')) && request('gol') == '2' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>
                    Inventaris Gol 2
                </p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ url('/update-password') }}" class="nav-link {{ request()->is('update-password') ? 'active' : '' }}">
        <i class="nav-icon fas fa-fingerprint"></i>
        <p>
            Ganti Password
        </p>
    </a>
</li>
