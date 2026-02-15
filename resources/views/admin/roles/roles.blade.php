@extends('admin.layout.master')

{{-- sidebar active (sesuaikan menu role kamu) --}}
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-role', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Role</span>
            <span class="mx-1 text-gray-400">/</span>
            <span class="text-blue-600">Daftar Role</span>
        </div>
    </section>

    <section class="p-5 rounded-2xl shadow-lg border border-gray-300">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Roles</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Menampilkan
                    <span class="font-medium text-gray-700">{{ $roles->firstItem() ?? 0 }}</span>
                    –
                    <span class="font-medium text-gray-700">{{ $roles->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-medium text-gray-700">{{ $roles->total() }}</span>
                    data • Halaman
                    <span class="font-medium text-gray-700">{{ $roles->currentPage() }}</span>
                    / {{ $roles->lastPage() }}
                </p>
            </div>

            <a href="{{ route('admin.create-role') }}"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                + Tambah Role
            </a>
        </div>

        {{-- Card Table --}}
        <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Nama Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Guard
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Dibuat
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($roles as $role)
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $role->name }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-100">
                                        {{ $role->guard_name ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ optional($role->created_at)->timezone(config('app.timezone'))->format('d F Y, H:i') }}
                                    WIB
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('edit.role', ['id' => $role->id]) }}"
                                            class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-blue-500">
                                            Edit
                                        </a>

                                        <form action="#" method="POST" onsubmit="return confirm('Hapus role ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-red-500">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Belum ada data role.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div
                class="flex flex-col gap-3 border-t border-gray-200 bg-white px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-600">
                    Total: <span class="font-medium text-gray-900">{{ $roles->total() }}</span> data
                </div>

                <div class="pagination">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('addJs')
@endsection
