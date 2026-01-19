<header class="bg-white p-5 shadow-[0_6px_9px_rgba(0,0,0,0.25)] sticky z-[90] top-0 left-0">
    <nav class="font-semibold ">
        <div class="flex justify-end items-center">
            <button class="cursor-pointer hidden lg:block" id="dropdownDefaultButton" data-dropdown-toggle="dropdown">Hi,
                <span class="text-[#F16C1B]">Administrator</span></button>
        </div>

        <div class="lg:hidden flex justify-between gap-3 items-center">
            <div class="flex gap-4 items-center">
                <img src="{{ asset('build/image/bhos-logo.png') }}" alt="logo" class="w-10">
                <span class="font-bold lg:text-lg">PT Grace Indo Pratama</span>
            </div>

            {{-- HAMBURGER --}}
            <button id="btn-menu" data-collapse-toggle="navbar-default" type="button"
                class="cursor-pointer inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-2000"
                aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>
    </nav>
</header>

{{-- SCRIPT TOGGLE (boleh taro persis di bawah ini dulu) --}}
<script>
    const btn = document.getElementById('btn-menu');
    const nav = document.getElementById('navbar-default');

    btn?.addEventListener('click', () => {
        nav?.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', nav.classList.contains('hidden') ? 'false' : 'true');
    });
</script>
