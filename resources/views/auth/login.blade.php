<x-guest-layout>
    <style>
        /* Sembunyikan logo Laravel */
        .min-h-screen>div:first-child {
            display: none !important;
        }

        /* Buat container Breeze full & center sempurna */
        .min-h-screen {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 16px !important;
            /* jarak dari tepi di HP */
            background: #f1f5f9 !important;
        }

        .min-h-screen>div:last-child {
            width: 100% !important;
            max-width: 400px !important;
            /* ukuran ideal card */
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
    </style>

    <div class="w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-300/40 border border-slate-100 overflow-hidden">

            <!-- Header -->
            <div
                class="bg-gradient-to-br from-[#0d5c34] via-[#127a45] to-[#1a8a52] px-6 pt-9 pb-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-[0.07]"
                    style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;">
                </div>

                <div class="relative z-10">
                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('image/bhos-logo.png') }}" alt="BHOS Technology"
                            class="h-14 sm:h-[60px] w-auto drop-shadow-md">
                    </div>
                    <h1 class="text-white text-[17px] sm:text-xl font-bold tracking-wide leading-snug">
                        PT Grace Borneo Indonesia
                    </h1>
                    <p class="text-emerald-100/80 text-[12px] sm:text-sm mt-1 font-medium tracking-wide">
                        Sistem Informasi Manajemen
                    </p>
                </div>
            </div>

            <!-- Form -->
            <div class="px-6 pt-7 pb-6">
                <x-auth-session-status class="mb-5" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email')"
                            class="text-slate-700 font-semibold text-[13px]" />
                        <x-text-input id="email"
                            class="block mt-1.5 w-full rounded-xl border-slate-200 bg-slate-50/70 focus:bg-white focus:border-[#127a45] focus:ring-2 focus:ring-[#127a45]/25 shadow-sm transition-all duration-200 text-sm placeholder:text-slate-400"
                            type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                            placeholder="Masukkan email Anda" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')"
                            class="text-slate-700 font-semibold text-[13px]" />
                        <x-text-input id="password"
                            class="block mt-1.5 w-full rounded-xl border-slate-200 bg-slate-50/70 focus:bg-white focus:border-[#127a45] focus:ring-2 focus:ring-[#127a45]/25 shadow-sm transition-all duration-200 text-sm placeholder:text-slate-400"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="Masukkan password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-slate-300 text-[#127a45] shadow-sm focus:ring-[#127a45] focus:ring-offset-0 w-4 h-4"
                                name="remember">
                            <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-medium text-[#127a45] hover:text-[#0d5c34] transition-colors"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Button -->
                    <div class="pt-3">
                        <x-primary-button
                            class="w-full justify-center py-[13px] rounded-xl bg-[#127a45] hover:bg-[#0f6a3b] focus:bg-[#0f6a3b] active:bg-[#0d5c34] text-white font-semibold tracking-wide shadow-lg shadow-emerald-900/15 hover:shadow-emerald-900/25 transition-all duration-200 text-[14px]">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-7 pt-5 border-t border-slate-100 text-center">
                    <p class="text-[11px] text-slate-400">
                        &copy; {{ date('Y') }} PT Grace Borneo Indonesia
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
