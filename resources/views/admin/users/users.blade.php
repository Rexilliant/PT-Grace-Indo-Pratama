@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-user', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Akun</span>
        </div>
    </section>

    {{-- FILTER --}}
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <form method="GET" action="{{ route('users') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 items-end">

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ request('name') }}"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f]" />
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input type="text" name="email" value="{{ request('email') }}"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f]" />
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">Role</label>
                    <select name="role"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f]">
                        <option value="">Semua Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(request('role') == $role->name)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f]">
                        <option value="">Semua</option>
                        <option value="aktif" @selected(request('status') == 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(request('status') == 'nonaktif')>Nonaktif</option>
                    </select>
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">Tampilkan</label>
                    <select name="per_page" onchange="this.form.submit()"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected(request('per_page', 10) == $n)>
                                {{ $n }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="rounded-md bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-800">
                    Filter
                </button>

                <a href="{{ route('users') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white text-center">
                    Reset
                </a>
            </div>
        </form>
    </section>

    {{-- TABLE --}}
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">

        <div class="mb-5 flex items-center gap-5">
            @can('tambah akun')
                <a href="{{ route('create-user') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-6 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                    + Tambah User
                </a>
            @endcan
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70">
                        <tr>
                            <th class="px-6 py-4 font-extrabold">Nama</th>
                            <th class="px-6 py-4 font-extrabold">Email</th>
                            <th class="px-6 py-4 font-extrabold">Karyawan</th>
                            <th class="px-6 py-4 font-extrabold">Role</th>
                            <th class="px-6 py-4 font-extrabold">Status</th>
                            <th class="px-6 py-4 font-extrabold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-100">

                                <td class="px-6 py-4 font-semibold">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">{{ $user->employee->name ?? '-' }}</td>

                                <td class="px-6 py-4">
                                    @foreach ($user->roles as $role)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>

                                <td class="px-6 py-4">
                                    @if ($user->deleted_at == null)
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @canany(['edit akun', 'baca akun'])
                                        <a href="{{ route('edit-user', $user->id) }}" class="text-blue-600 hover:underline">
                                            Edit
                                        </a>
                                    @endcanany

                                    @can('hapus akun')
                                        |
                                        <form action="{{ route('delete-user', $user->id) }}" method="POST"
                                            class="inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    @endcan
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-gray-500">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between bg-gray-200 px-5 py-3 border-t">
                <div>
                    Showing {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }}
                </div>
                {{ $users->links() }}
            </div>
        </div>
    </section>
@endsection
