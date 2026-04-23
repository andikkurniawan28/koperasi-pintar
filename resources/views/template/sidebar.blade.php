<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="/fms/public/sneat/assets/img/fathania.png" alt="Logo"width="50">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">KC</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    @php
        $role = auth()->user()->role->name ?? null;
    @endphp

    <ul class="menu-inner py-1">

        <!-- Home -->
        <li class="menu-item @yield('home_active')">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Home</div>
            </a>
        </li>

        <!-- Toko -->
        <li class="menu-item @yield('transaksi_toko_active')">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div>Toko</div>
            </a>
            <ul class="menu-sub">

                @if (in_array($role, ['Admin', 'Kasir']))
                    <li class="menu-item @yield('sales_active')">
                        <a href="{{ route('sales.index') }}" class="menu-link">
                            <div>Penjualan</div>
                        </a>
                    </li>
                @endif

            </ul>
        </li>

        <!-- Simpan Pinjam -->
        <li class="menu-item @yield('transaksi_simpan_pinjam_active')">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div>Simpan Pinjam</div>
            </a>
            <ul class="menu-sub">

            </ul>
        </li>

        <!-- Jasa -->
        <li class="menu-item @yield('transaksi_jasa_active')">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-wrench"></i>
                <div>Jasa</div>
            </a>
            <ul class="menu-sub">

            </ul>
        </li>

        <li class="menu-item @yield('laporan_active')">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div>Laporan</div>
            </a>
            <ul class="menu-sub">

                @if (in_array($role, ['Admin']))
                    <li class="menu-item @yield('ledger_active')">
                        <a href="{{ route('ledger.index') }}" class="menu-link">
                            <div>Buku Besar</div>
                        </a>
                    </li>
                @endif

                @if (in_array($role, ['Admin']))
                    <li class="menu-item @yield('stock_ledger_active')">
                        <a href="{{ route('stock_ledger.index') }}" class="menu-link">
                            <div>Kartu Stok</div>
                        </a>
                    </li>
                @endif

                @if (in_array($role, ['Admin']))
                    <li class="menu-item @yield('cash_flow_active')">
                        <a href="{{ route('cash_flow.index') }}" class="menu-link">
                            <div>Arus Kas</div>
                        </a>
                    </li>
                @endif

                @if (in_array($role, ['Admin']))
                    <li class="menu-item @yield('profit_loss_active')">
                        <a href="{{ route('profit_loss.index') }}" class="menu-link">
                            <div>Laba Rugi</div>
                        </a>
                    </li>
                @endif

                @if (in_array($role, ['Admin']))
                    <li class="menu-item @yield('balance_sheet_active')">
                        <a href="{{ route('balance_sheet.index') }}" class="menu-link">
                            <div>Neraca</div>
                        </a>
                    </li>
                @endif

            </ul>
        </li>

        <!-- Master -->
        <li class="menu-item @yield('master_active')">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-folder"></i>
                <div>Master</div>
            </a>
            <ul class="menu-sub">

                @if ($role === 'Admin')
                    <li class="menu-item @yield('user_active')">
                        <a href="{{ route('user.index') }}" class="menu-link">
                            <div>User</div>
                        </a>
                    </li>
                @endif

                @if ($role === 'Admin')
                    <li class="menu-item @yield('member_active')">
                        <a href="{{ route('member.index') }}" class="menu-link">
                            <div>Anggota</div>
                        </a>
                    </li>
                @endif

                @if ($role === 'Admin')
                    <li class="menu-item @yield('customer_active')">
                        <a href="{{ route('customer.index') }}" class="menu-link">
                            <div>Customer</div>
                        </a>
                    </li>
                @endif

                @if ($role === 'Admin')
                    <li class="menu-item @yield('supplier_active')">
                        <a href="{{ route('supplier.index') }}" class="menu-link">
                            <div>Supplier</div>
                        </a>
                    </li>
                @endif

                @if ($role === 'Admin')
                    <li class="menu-item @yield('account_active')">
                        <a href="{{ route('account.index') }}" class="menu-link">
                            <div>Akun</div>
                        </a>
                    </li>
                @endif

                @if ($role === 'Admin')
                    <li class="menu-item @yield('saving_type_active')">
                        <a href="{{ route('saving_type.index') }}" class="menu-link">
                            <div>Jenis Simpanan</div>
                        </a>
                    </li>
                @endif

                @if ($role === 'Admin')
                    <li class="menu-item @yield('product_active')">
                        <a href="{{ route('product.index') }}" class="menu-link">
                            <div>Produk</div>
                        </a>
                    </li>
                @endif

            </ul>
        </li>

    </ul>
</aside>
