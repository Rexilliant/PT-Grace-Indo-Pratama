@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-role', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Role</span>
            <span class="mx-1 text-gray-400">/</span>
            <span class="text-blue-600">Tambah Role</span>
        </div>
    </section>

    <section class="p-5 rounded-md shadow-sm border border-gray-400">
        <form action="{{ route('admin.store-role') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Nama Role --}}
            <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-slate-800">Nama Role</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    placeholder="Contoh: Admin / Gudang / Pemasaran"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                @error('name')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Permissions --}}
            <div x-data="{
                selected: @js(old('permissions', [])),
                all: @js($permissions->pluck('name')->values()),
                allSelected() { return this.selected.length === this.all.length && this.all.length > 0 },
                toggleAll() { this.selected = this.allSelected() ? [] : [...this.all] }
            }" class="rounded-2xl border border-slate-200 bg-white">

                <div
                    class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50/60 px-4 py-4 sm:flex-row sm:items-center sm:justify-between rounded-t-2xl">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Role Has Permissions</h3>
                        <p class="text-xs text-slate-500">Centang permission yang ingin diberikan ke role.</p>
                    </div>

                    <button type="button" @click="toggleAll()"
                        :class="allSelected() ?
                            'bg-blue-600 text-white border-blue-600 shadow-blue-600/20' :
                            'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'"
                        class="inline-flex items-center justify-center rounded-2xl border px-4 py-2 text-xs font-semibold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                        <span x-text="allSelected() ? 'Uncheck Semua' : 'Centang Semua'"></span>
                    </button>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($permissions as $permission)
                            <label
                                class="group flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 transition hover:bg-slate-100">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                    x-model="selected"
                                    class="mt-1 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600/30" />

                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">
                                        {{ $permission->name }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Guard: {{ $permission->guard_name ?? '-' }}
                                    </p>
                                </div>
                            </label>
                        @empty
                            <div
                                class="col-span-full rounded-2xl border border-slate-200 bg-white p-6 text-center text-sm text-slate-500">
                                Belum ada data permission.
                            </div>
                        @endforelse
                    </div>

                    @error('permissions')
                        <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-600/30">
                    Simpan Role
                </button>
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
