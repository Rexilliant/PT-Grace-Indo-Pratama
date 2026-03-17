@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-log', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Pengadaan Barang</a>
        </div>
    </section>

    <section class="mb-5 rounded-lg border border-gray-300 bg-white p-5 shadow overflow-hidden">
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="w-full overflow-x-auto">
                <table class="w-full min-w-[900px] table-fixed text-left text-sm text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="w-[30%] px-6 py-4 text-left font-extrabold">Message</th>
                            <th scope="col" class="w-[30%] px-6 py-4 text-left font-extrabold">File</th>
                            <th scope="col" class="w-[10%] px-6 py-4 text-left font-extrabold">Line</th>
                            <th scope="col" class="w-[15%] px-6 py-4 text-left font-extrabold">User</th>
                            <th scope="col" class="w-[15%] px-6 py-4 text-left font-extrabold">Tanggal</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-500 bg-gray-200">
                        @forelse ($logerrors as $error)
                            <tr class="[&>td]:border-b [&>td]:border-gray-400 hover:bg-gray-100">
                                <td class="px-6 py-4 align-top break-words whitespace-normal">
                                    {{ $error->message }}
                                </td>
                                <td class="px-6 py-4 align-top break-all whitespace-normal">
                                    {{ $error->file }}
                                </td>
                                <td class="px-6 py-4 align-top">
                                    {{ $error->line }}
                                </td>
                                <td class="px-6 py-4 align-top break-words whitespace-normal">
                                    {{ $error->user }}
                                </td>
                                <td class="px-6 py-4 align-top break-words whitespace-normal">
                                    {{ $error->created_at }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Data Tidak Ada
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="flex flex-col gap-2 border-t border-gray-400 bg-gray-200 px-3 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-4 sm:py-4 md:px-5">
                <div class="text-xs font-semibold text-gray-800 sm:text-sm">
                    Showing {{ $logerrors->firstItem() ?? 0 }}–{{ $logerrors->lastItem() ?? 0 }} of
                    {{ $logerrors->total() }}
                </div>

                <div class="w-full overflow-x-auto sm:w-auto">
                    <div class="pagination">
                        {{ $logerrors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
