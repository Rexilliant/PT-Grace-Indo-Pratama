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
            <div class="flex flex-col items-center justify-center gap-3">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200 border-t-[#53BF6A]"></div>
                <span class="text-[#53BF6A] font-semibold text-sm animate-pulse">Memuat...</span>
            </div>
        </div>

    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('submit', function(e) {
                // Only act on forms
                if (e.target && e.target.tagName === 'FORM') {
                    // Tampilkan loading overlay
                    const overlay = document.getElementById('loading-overlay');
                    if (overlay) {
                        overlay.style.display = 'flex';
                    }

                    // Disable tombol submit agar tidak terjadi double click
                    const buttons = e.target.querySelectorAll('button[type="submit"], input[type="submit"]');
                    buttons.forEach(btn => {
                        // Tambahkan class agar terlihat disabled secara visual (jika menggunakan tailwind)
                        btn.classList.add('opacity-50', 'cursor-not-allowed');
                        
                        // Gunakan setTimeout agar form tetap bisa tersubmit (karena jika langsung disabled kadang form tidak terkirim di beberapa browser)
                        setTimeout(() => {
                            btn.disabled = true;
                        }, 10);
                    });
                }
            });
        });
    </script>

    @yield('addJs')
</body>

</html>
