@extends('admin.layout.master')


@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pengadaan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('content')

    {{-- breadcrumb --}}
    <section class="mb-5">
        {{-- breadcrumb --}}
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Warehouse</a>
        </div>
    </section>
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <form method="GET" class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 items-end">

                {{-- Search --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Nama
                    </label>
                    <input type="text" name="name" value="{{ request('search') }}" placeholder="Nama"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Province
                    </label>
                    <input type="text" name="province" value="{{ request('search') }}" placeholder="Provinsi"
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

                <a href="{{ route('warehouses') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </section>
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <div class="mb-5 flex items-center gap-5">
            <a href="{{ route('procurements.export', request()->query()) }}"
                class="inline-flex items-center
                gap-2 rounded-lg bg-[#2E7E3F] px-5 py-2 text-sm font-semibold text-white hover:bg-green-800
                focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 0 0014.9 3" />
                </svg>
                Export .xlsx
            </a>

            @can('create-procurements')
                <a href="{{ route('create-warehouse') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-6 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span class="text-lg leading-none">+</span>
                    Tambah Baru
                </a>
            @endcan
        </div>
        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Id Gudang</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Nama</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Provinsi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Kota</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Penanggung Jawab</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($warehouses as $warehouse)
                            <tr class="[&>td]:border-b [&>td]:border-gray-400 hover:bg-gray-100">

                                <td class="px-6 py-4">{{ $warehouse->id }}</td>
                                <td class="px-6 py-4">{{ $warehouse->name }}</td>
                                <td class="px-6 py-4">{{ $warehouse->province }}</td>
                                <td class="px-6 py-4">{{ $warehouse->city }}</td>
                                <td class="px-6 py-4">{{ $warehouse->responsible->name }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('edit-warehouse', ['id' => $warehouse->id]) }}"
                                        class="text-blue-600 hover:underline">
                                        Sunting
                                    </a>
                                    |

                                    <form action="{{ route('delete-warehouse', ['id' => $warehouse->id]) }}" method="POST"
                                        class="inline-block form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Data Tidak Ada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- footer / pagination (mobile + ipad aman) --}}
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between
                       bg-gray-200 px-3 sm:px-4 md:px-5 py-3 sm:py-4 border-t border-gray-400">

                <div class="text-xs sm:text-sm font-semibold text-gray-800">
                    Showing {{ $warehouses->firstItem() ?? 0 }}–{{ $warehouses->lastItem() ?? 0 }} of
                    {{ $warehouses->total() }}
                </div>

                <div class="w-full sm:w-auto overflow-x-auto">
                    <div class="pagination">
                        {{ $warehouses->links() }}
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#16a34a'
            });
        @endif

        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Anda yakin?',
                    text: 'Data gudang yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
