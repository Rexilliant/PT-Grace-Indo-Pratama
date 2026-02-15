@extends('admin.layout.master')
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-user', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">User</span>
            <span class="mx-1 text-gray-400">/</span>
            <span class="text-blue-600">Daftar User</span>
        </div>
    </section>

    <section class="p-5 rounded-2xl shadow-lg border border-gray-300">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Users</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Menampilkan
                    <span class="font-medium text-gray-700">{{ $users->firstItem() ?? 0 }}</span>
                    –
                    <span class="font-medium text-gray-700">{{ $users->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-medium text-gray-700">{{ $users->total() }}</span>
                    data • Halaman
                    <span class="font-medium text-gray-700">{{ $users->currentPage() }}</span>
                    / {{ $users->lastPage() }}
                </p>
            </div>

            <a href="{{ route('create-user') }}"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                + Tambah User
            </a>
        </div>

        {{-- Card Table --}}
        <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Nama
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Karyawan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50/70">

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $user->name }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $user->email }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if ($user->employee)
                                        {{ $user->employee->name }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @foreach ($user->roles as $role)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if ($user->deleted_at == null)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('edit-user', $user->id) }}"
                                            class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-blue-500">
                                            Edit
                                        </a>

                                        @if ($user->deleted_at !== null)
                                            <form action="{{ route('restore-user', $user->id) }}" method="POST"
                                                onsubmit="return confirm('Kembalikan user ini?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-green-500">
                                                    Restore
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('delete-user', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
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
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Belum ada data user.
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
                    Total: <span class="font-medium text-gray-900">{{ $users->total() }}</span> data
                </div>

                <div class="pagination">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
