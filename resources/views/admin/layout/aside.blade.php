{{-- desktop --}}
<aside
    class="hidden lg:flex flex-col bg-white lg:w-[432px] 2xl:w-[350px] h-screen z-[100] lg:sticky left-0 top-0 shadow-[0_4px_4px_rgba(0,0,0,0.25)] p-5 overflow-hidden">

    {{-- header --}}
    <div class="flex items-center mb-6 shrink-0">
        <div class="flex items-center mr-4">
            <img src="{{ asset('build/image/bhos-logo.png') }}" alt="BHOS Technology" class="h-12 w-auto">
        </div>

        <div class="text-left leading-tight">
            <div class="text-[20px] font-black tracking-wide">PT</div>
            <div class="text-[10px] font-semibold text-slate-500">GRACE INDO</div>
            <div class="text-[10px] font-semibold text-slate-500">PRATAMA</div>
        </div>
    </div>

    {{-- menu --}}
    <div
        class="menu flex-1 overflow-y-auto space-y-2 text-slate-700
            [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
            class="cursor-pointer flex gap-3 items-center px-3 py-2 rounded-xl duration-300 ease-in-out hover:bg-slate-100 @yield('menu-dashboard')">
            <span class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center">
                <svg class="fill-slate-600" width="18" height="18" viewBox="0 0 24 24">
                    <path d="M4 4h16v4H4V4zm0 6h16v10H4V10z" />
                </svg>
            </span>
            <span class="font-semibold text-[14px]">Dashboard</span>
        </a>

        {{-- Gudang --}}
        <details class="group" @yield('open-gudang')>
            <summary
                class="list-none cursor-pointer flex items-center justify-between px-3 py-2 rounded-xl
                       duration-300 ease-in-out hover:bg-slate-100
                       group-open:bg-gradient-to-r group-open:from-[#53BF6A]/80 group-open:to-[#275931]/80
                       group-open:text-white
                       @yield('menu-gudang')">

                <div class="flex items-center gap-3">
                    <span
                        class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center transition group-open:bg-white/20">
                        <svg class="fill-slate-600 transition group-open:fill-white" width="18" height="18"
                            viewBox="0 0 24 24">
                            <path d="M3 10l9-7 9 7v11H3V10zm2 2v7h14v-7H5z" />
                        </svg>
                    </span>
                    <span class="font-semibold text-[14px]">Gudang</span>
                </div>

                <svg class="h-4 w-4 text-slate-700 transition group-open:text-white group-open:rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </summary>

            <div class="mt-2 ml-11 mr-2 rounded-2xl bg-white shadow-md p-2 space-y-1">
                <a href="{{ route('admin.gudang-pengadaan-barang') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-pengadaan')">
                    Pengadaan Barang
                </a>

                <a href="{{ route('admin.gudang-barang-masuk') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-barang-masuk')">
                    Barang Masuk
                </a>

                <a href="{{ route('admin.gudang-laporan-produksi') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-laporan-produksi')">
                    Laporan Produksi
                </a>

                <a href="{{ route('admin.gudang-permintaan-pengiriman') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-permintaan-pengiriman')">
                    Permintaan Pengiriman
                </a>

                <a href="{{ route('admin.gudang-bahan-baku') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-bahan-baku')">
                    Bahan Baku
                </a>
                <a href="{{ route('admin.gudang-stok-bahan-baku') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-stok-bahan-baku')">
                    Stok Bahan Baku
                </a>
            </div>
        </details>

        {{-- Pemasaran --}}
        <details class="group" @yield('open-pemasaran')>
            <summary
                class="list-none cursor-pointer flex items-center justify-between px-3 py-2 rounded-xl
                       duration-300 ease-in-out hover:bg-slate-100
                       group-open:bg-gradient-to-r group-open:from-[#53BF6A]/80 group-open:to-[#275931]/80
                       group-open:text-white
                       @yield('menu-pemasaran')">

                <div class="flex items-center gap-3">
                    <span
                        class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center transition group-open:bg-white/20">
                        <svg class="fill-slate-600 transition group-open:fill-white" width="18" height="18"
                            viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z" />
                        </svg>
                    </span>
                    <span class="font-semibold text-[14px]">Pemasaran</span>
                </div>

                <svg class="h-4 w-4 text-slate-700 transition group-open:text-white group-open:rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </summary>

            <div class="mt-2 ml-11 mr-2 rounded-2xl bg-white shadow-md p-2 space-y-1">
                <a href="{{ route('admin.pemasaran-permintaan-pengiriman') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-pemasaran-permintaan-pengiriman')">
                    Permintaan Pengiriman
                </a>

                <a href="{{ route('admin.pemasaran-laporan-penjualan') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-pemasaran-laporan-penjualan')">
                    Laporan Penjualan
                </a>
            </div>
        </details>

        {{-- Executive Team --}}
        <details class="group" @yield('open-executive')>
            <summary
                class="list-none cursor-pointer flex items-center justify-between px-3 py-2 rounded-xl
                       duration-300 ease-in-out hover:bg-slate-100
                       group-open:bg-gradient-to-r group-open:from-[#53BF6A]/80 group-open:to-[#275931]/80
                       group-open:text-white
                       @yield('menu-executive')">

                <div class="flex items-center gap-3">
                    <span
                        class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center transition group-open:bg-white/20">
                        <svg class="fill-slate-600 transition group-open:fill-white" width="18" height="18"
                            viewBox="0 0 24 24">
                            <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-8 10a8 8 0 0116 0H4z" />
                        </svg>
                    </span>
                    <span class="font-semibold text-[14px]">Executive Team</span>
                </div>

                <svg class="h-4 w-4 text-slate-700 transition group-open:text-white group-open:rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </summary>

            <div class="mt-2 ml-11 mr-2 rounded-2xl bg-white shadow-md p-2 space-y-1">
                <a href="{{ route('employees') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-karyawan')">
                    Karyawan
                </a>
                <a href="{{ route('users') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-user')">
                    User
                </a>

                <a href="{{ route('admin.executive-produk') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-produk')">
                    Produk
                </a>

                <a href="{{ route('admin.executive-produk-variant') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-produk-variant')">
                    Produk Varian
                </a>

                <a href="{{ route('admin.executive-pengadaan-barang') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-pengadaan-barang')">
                    Pengadaan Barang
                </a>
                <a href="{{ route('permissions') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-permission')">
                    Permissions
                </a>
                <a href="{{ route('roles') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-role')">
                    Role
                </a>
            </div>

        </details>

        {{-- Profile --}}
        <a href="{{ route('admin.profile') }}"
            class="cursor-pointer flex gap-3 items-center px-3 py-2 rounded-xl duration-300 ease-in-out hover:bg-slate-100 @yield('menu-profile')">
            <span class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center">
                <svg class="fill-slate-600" width="18" height="18" viewBox="0 0 24 24">
                    <path d="M12 2a7 7 0 00-7 7v3a7 7 0 0014 0V9a7 7 0 00-7-7zm-7 19a7 7 0 0114 0H5z" />
                </svg>
            </span>
            <span class="font-semibold text-[14px]">Profile</span>
        </a>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit"
                class="w-full text-left flex gap-3 items-center px-3 py-2 rounded-xl duration-300 ease-in-out hover:bg-slate-100">
                <span class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center">
                    <svg class="fill-slate-600" width="18" height="18" viewBox="0 0 24 24">
                        <path
                            d="M16 13v-2H7V8l-5 4 5 4v-3h9zm3-10H5c-1.1 0-2 .9-2 2v6h2V5h14v14H5v-6H3v6c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                    </svg>
                </span>
                <span class="font-semibold text-[14px]">Logout</span>
            </button>
        </form>

    </div>
</aside>


{{-- mobile --}}
<aside id="navbar-default"
    class="hidden bg-white w-[280px] sm:w-[320px] h-screen z-[100] fixed left-0 top-0 shadow-[0_4px_4px_rgba(0,0,0,0.25)] p-5 overflow-hidden flex flex-col">

    {{-- header --}}
    <div class="flex items-center justify-between mb-6 shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('build/image/bhos-logo.png') }}" alt="BHOS Technology" class="h-12 w-auto">
        </div>

        <div class="text-right leading-tight">
            <div class="text-[18px] font-black tracking-wide">PT</div>
            <div class="text-[10px] font-semibold text-slate-500">GRACE INDO</div>
            <div class="text-[10px] font-semibold text-slate-500">PRATAMA</div>
        </div>
    </div>

    {{-- menu --}}
    <div
        class="menu flex-1 overflow-y-auto space-y-2 text-slate-700
            [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
            class="cursor-pointer flex gap-3 items-center px-3 py-2 rounded-xl duration-300 ease-in-out hover:bg-slate-100 @yield('menu-dashboard')">
            <span class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center">
                <svg class="fill-slate-600" width="18" height="18" viewBox="0 0 24 24">
                    <path d="M4 4h16v4H4V4zm0 6h16v10H4V10z" />
                </svg>
            </span>
            <span class="font-semibold text-[14px]">Dashboard</span>
        </a>

        {{-- Gudang --}}
        <details class="group" @yield('open-gudang')>
            <summary
                class="list-none cursor-pointer flex items-center justify-between px-3 py-2 rounded-xl
                       duration-300 ease-in-out hover:bg-slate-100
                       group-open:bg-gradient-to-r group-open:from-[#53BF6A]/80 group-open:to-[#275931]/80
                       group-open:text-white
                       @yield('menu-gudang')">

                <div class="flex items-center gap-3">
                    <span
                        class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center transition group-open:bg-white/20">
                        <svg class="fill-slate-600 transition group-open:fill-white" width="18" height="18"
                            viewBox="0 0 24 24">
                            <path d="M3 10l9-7 9 7v11H3V10zm2 2v7h14v-7H5z" />
                        </svg>
                    </span>
                    <span class="font-semibold text-[14px]">Gudang</span>
                </div>

                <svg class="h-4 w-4 text-slate-700 transition group-open:text-white group-open:rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </summary>

            <div class="mt-2 ml-11 mr-2 rounded-2xl bg-white shadow-md p-2 space-y-1">
                <a href="{{ route('admin.gudang-pengadaan-barang') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-pengadaan')">
                    Pengadaan Barang
                </a>

                <a href="{{ route('admin.gudang-barang-masuk') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-barang-masuk')">
                    Barang Masuk
                </a>

                <a href="{{ route('admin.gudang-laporan-produksi') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-laporan-produksi')">
                    Laporan Produksi
                </a>

                <a href="{{ route('admin.gudang-permintaan-pengiriman') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-permintaan-pengiriman')">
                    Permintaan Pengiriman
                </a>

                <a href="{{ route('admin.gudang-bahan-baku') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-bahan-baku')">
                    Bahan Baku
                </a>
                <a href="{{ route('admin.gudang-stok-bahan-baku') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-gudang-stok-bahan-baku')">
                    Stok Bahan Baku
                </a>
            </div>
        </details>

        {{-- Pemasaran --}}
        <details class="group" @yield('open-pemasaran')>
            <summary
                class="list-none cursor-pointer flex items-center justify-between px-3 py-2 rounded-xl
                       duration-300 ease-in-out hover:bg-slate-100
                       group-open:bg-gradient-to-r group-open:from-[#53BF6A]/80 group-open:to-[#275931]/80
                       group-open:text-white
                       @yield('menu-pemasaran')">

                <div class="flex items-center gap-3">
                    <span
                        class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center transition group-open:bg-white/20">
                        <svg class="fill-slate-600 transition group-open:fill-white" width="18" height="18"
                            viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z" />
                        </svg>
                    </span>
                    <span class="font-semibold text-[14px]">Pemasaran</span>
                </div>

                <svg class="h-4 w-4 text-slate-700 transition group-open:text-white group-open:rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </summary>

            <div class="mt-2 ml-11 mr-2 rounded-2xl bg-white shadow-md p-2 space-y-1">
                <a href="{{ route('admin.pemasaran-permintaan-pengiriman') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-pemasaran-permintaan-pengiriman')">
                    Permintaan Pengiriman
                </a>

                <a href="{{ route('admin.pemasaran-laporan-penjualan') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-pemasaran-laporan-penjualan')">
                    Laporan Penjualan
                </a>
            </div>
        </details>

        {{-- Executive Team --}}
        <details class="group" @yield('open-executive')>
            <summary
                class="list-none cursor-pointer flex items-center justify-between px-3 py-2 rounded-xl
               duration-300 ease-in-out hover:bg-slate-100
               group-open:bg-gradient-to-r group-open:from-[#53BF6A]/80 group-open:to-[#275931]/80
               group-open:text-white
               @yield('menu-executive')">

                <div class="flex items-center gap-3">
                    <span
                        class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center transition group-open:bg-white/20">
                        <svg class="fill-slate-600 transition group-open:fill-white" width="18" height="18"
                            viewBox="0 0 24 24">
                            <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-8 10a8 8 0 0116 0H4z" />
                        </svg>
                    </span>
                    <span class="font-semibold text-[14px]">Executive Team</span>
                </div>

                <svg class="h-4 w-4 text-slate-700 transition group-open:text-white group-open:rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </summary>

            <div class="mt-2 ml-11 mr-2 rounded-2xl bg-white shadow-md p-2 space-y-1">
                <a href="{{ route('employees') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-karyawan')">
                    Karyawan
                </a>
                <a href="{{ route('users') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-user')">
                    User
                </a>
                <a href="{{ route('admin.executive-produk') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-produk')">
                    Produk
                </a>
                <a href="{{ route('admin.executive-produk-variant') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-produk-variant')">
                    Produk Varian
                </a>

                <a href="{{ route('admin.executive-pengadaan-barang') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-pengadaan-barang')">
                    Pengadaan Barang
                </a>

                <a href="{{ route('permissions') }}"
                    class="block px-3 py-2 rounded-xl text-[13px] font-medium hover:bg-slate-100 @yield('menu-executive-permission')">
                    Permission
                </a>
            </div>
        </details>


        {{-- Profile --}}
        <a href="{{ route('admin.profile') }}"
            class="cursor-pointer flex gap-3 items-center px-3 py-2 rounded-xl duration-300 ease-in-out hover:bg-slate-100 @yield('menu-profile')">
            <span class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center">
                <svg class="fill-slate-600" width="18" height="18" viewBox="0 0 24 24">
                    <path d="M12 2a7 7 0 00-7 7v3a7 7 0 0014 0V9a7 7 0 00-7-7zm-7 19a7 7 0 0114 0H5z" />
                </svg>
            </span>
            <span class="font-semibold text-[14px]">Profile</span>
        </a>

        {{-- Logout --}}
        <a href=""
            class="cursor-pointer flex gap-3 items-center px-3 py-2 rounded-xl duration-300 ease-in-out hover:bg-slate-100 @yield('menu-keluar')">
            <span class="h-8 w-8 rounded-full bg-slate-200 grid place-items-center">
                <svg class="fill-slate-600" width="18" height="18" viewBox="0 0 24 24">
                    <path d="M10 17l1.41-1.41L8.83 13H20v-2H8.83l2.58-2.59L10 7l-7 7 7 3z" />
                </svg>
            </span>
            <span class="font-semibold text-[14px]">Logout</span>
        </a>

    </div>
</aside>
