<!DOCTYPE html>
<html lang="en">
@include('admin.layout.head')

<body class="text-slate-700 font-plus-jakarta-sans md:flex bg-[#F8FAFC]">
    @include('admin.layout.aside')
    <section class="w-full ">
        @include('admin.layout.nav')
        <main class="p-5">
            @yield('content')
        </main>
        <div id="loading-overlay"
            style="
    display:none;
    position:fixed;
    top:0; left:0; right:0; bottom:0;
    background:rgba(255,255,255,0.7);
    z-index:9999;
    justify-content:center;
    align-items:center;
">
            <div class="loader"></div>
        </div>

    </section>
    {{-- <script src="{{ asset('assets/js/flowbite.min.js') }}"></script> --}}

    @yield('addJs')
</body>

</html>
