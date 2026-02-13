@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-bahan-baku', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Permission</span>
            <span class="mx-1 text-gray-400"></span>
            <span class="text-blue-600">Tambah Permission</span>
        </div>
    </section>
    <section class="p-5 rounded-md shadow-sm border border-gray-400">
        <form action="{{ route('store-permission') }}" method="post">
            @csrf
            @method('POST')
            <div class="mb-5">
                <label for="name" class="block mb-2.5 text-sm font-medium text-heading">Nama Permission</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    placeholder="Enter your name"
                    class="w-full px-4 py-3 text-sm text-gray-800 bg-gray-50 border border-gray-400 rounded-xl shadow-sm placeholder:text-gray-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-all duration-200" />
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

            </div>
            <button type="submit"
                class="px-4 py-3 text-sm text-white bg-blue-600 rounded-xl shadow-sm hover:bg-blue-700 transition-all duration-200">Simpan</button>
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
