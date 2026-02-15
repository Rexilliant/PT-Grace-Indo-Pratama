@extends('admin.layout.master')
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-permission', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Permission</span>
            <span class="mx-1 text-gray-400">/</span>
            <span class="text-blue-600">Edit Permission</span>
        </div>
    </section>

    <section class="p-5 rounded-2xl shadow-sm border border-slate-200 bg-white">
        {{-- Opsi A: kalau route kamu bernama update-permission dan butuh ID --}}
        <form action="{{ route('update-permission', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Opsi B: kalau route kamu pakai resource: permissions.update --}}
            {{-- <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                @csrf
                @method('PUT') --}}

            <div class="mb-5">
                <label for="name" class="block mb-2.5 text-sm font-semibold text-slate-800">
                    Nama Permission
                </label>

                <input type="text" id="name" name="name" value="{{ old('name', $permission->name) }}"
                    placeholder="Masukkan nama permission"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition
                           focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />

                @error('name')
                    <p class="text-rose-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-600/30">
                    Update
                </button>

                <a href="{{ route('permissions') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-900/5">
                    Batal
                </a>
            </div>
        </form>
    </section>
@endsection

@section('addJs')
    <script src="{{ asset('assets/js/sweetalert.js') }}"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2563eb'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc2626'
            });
        </script>
    @endif
@endsection
