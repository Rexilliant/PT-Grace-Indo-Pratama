@extends('admin.layout.master')
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-karyawan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Karyawan</span>
            <span class="mx-1 text-gray-400">/</span>
            <span class="text-blue-600">Daftar Karyawan</span>
        </div>
    </section>
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <form method="GET" class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-end">

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        NIP
                    </label>
                    <input type="text" name="nip" value="{{ request('nip') }}" placeholder="NIP"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Nama
                    </label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Nama"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Jabatan
                    </label>
                    <input type="text" name="position" value="{{ request('position') }}" placeholder="Jabatan"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Email
                    </label>
                    <input type="text" name="email" value="{{ request('email') }}" placeholder="Email"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        No. Hp
                    </label>
                    <input type="text" name="phone" value="{{ request('phone') }}" placeholder="No. Hp"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                {{-- Per Page --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Tampilkan
                    </label>
                    <select name="per_page"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none"
                        onchange="this.form.submit()">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected((int) request('per_page', 10) === $n)>
                                {{ $n }} / halaman
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="rounded-md bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-800 transition">
                    Filter
                </button>

                <a href="{{ route('employees') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="p-5 rounded-2xl shadow-lg border border-gray-300">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Employees</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Menampilkan
                    <span class="font-medium text-gray-700">{{ $employees->firstItem() ?? 0 }}</span>
                    –
                    <span class="font-medium text-gray-700">{{ $employees->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-medium text-gray-700">{{ $employees->total() }}</span>
                    data • Halaman
                    <span class="font-medium text-gray-700">{{ $employees->currentPage() }}</span>
                    / {{ $employees->lastPage() }}
                </p>
            </div>

            <a href="{{ route('admin.create-employee') }}"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                + Tambah Karyawan
            </a>
        </div>

        {{-- Card Table --}}
        <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr>
                            <th class="px-6 py-4 font-extrabold text-left">
                                NIP
                            </th>
                            <th class="px-6 py-4 font-extrabold text-left">
                                Nama
                            </th>
                            <th class="px-6 py-4 font-extrabold text-left">
                                Jabatan
                            </th>
                            <th class="px-6 py-4 font-extrabold text-left">
                                Email
                            </th>
                            <th class="px-6 py-4 font-extrabold text-left">
                                No. Hp
                            </th>
                            <th class="px-6 py-4 font-extrabold text-left">
                                Status
                            </th>
                            <th class="px-6 py-4 font-extrabold text-left">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($employees as $employee)
                            <tr class="[&>td]:border-b border-gray-400 hover:bg-gray-100">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $employee->nip }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $employee->name }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $employee->position }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $employee->email }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $employee->phone }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($employee->deleted_at == null)
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
                                        <a href="{{ route('edit.employee', $employee->id) }}"
                                            class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-blue-500">
                                            Edit
                                        </a>

                                        @if ($employee->deleted_at !== null)
                                            <form action="{{ route('restore.employee', $employee->id) }}" method="POST"
                                                onsubmit="return confirm('Kembalikan employee ini?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-green-500">
                                                    Kembalikan
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('delete.employee', $employee->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus employee ini?')">
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
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Belum ada data employee.
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
                    Total: <span class="font-medium text-gray-900">{{ $employees->total() }}</span> data
                </div>

                <div class="pagination">
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('addJs')
@endsection
