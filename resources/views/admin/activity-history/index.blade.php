@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-history-activity', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- Breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive Team</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Riwayat Aktivitas</a>
        </div>
    </section>

    {{-- Filter --}}
    <section class="mb-4">
        <form method="GET" action="{{ route('activity-history') }}" class="flex flex-wrap gap-3 items-end">
            {{-- Search causer --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-600">Pengguna</label>
                <input
                    type="text"
                    name="causer"
                    value="{{ request('causer') }}"
                    placeholder="Nama pengguna..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#53BF6A]/50 w-48"
                >
            </div>

            {{-- Filter event --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-600">Event</label>
                <select name="event" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#53BF6A]/50 w-36">
                    <option value="">Semua</option>
                    <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>

            {{-- Filter model --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-600">Model</label>
                <input
                    type="text"
                    name="subject_type"
                    value="{{ request('subject_type') }}"
                    placeholder="Nama model..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#53BF6A]/50 w-48"
                >
            </div>

            {{-- Filter tanggal --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-600">Dari Tanggal</label>
                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#53BF6A]/50"
                >
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-600">Sampai Tanggal</label>
                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#53BF6A]/50"
                >
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="bg-[#53BF6A] hover:bg-[#3da055] text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('activity-history') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Reset
                </a>
            </div>
        </form>
    </section>

    {{-- Table --}}
    <section class="mb-5 rounded-lg border border-gray-300 bg-white p-5 shadow overflow-hidden">
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="w-full overflow-x-auto">
                <table class="w-full min-w-[900px] table-fixed text-left text-sm text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="w-[5%] px-4 py-4 text-left font-extrabold">#</th>
                            <th scope="col" class="w-[15%] px-4 py-4 text-left font-extrabold">Pengguna</th>
                            <th scope="col" class="w-[10%] px-4 py-4 text-left font-extrabold">Event</th>
                            <th scope="col" class="w-[20%] px-4 py-4 text-left font-extrabold">Model</th>
                            <th scope="col" class="w-[10%] px-4 py-4 text-left font-extrabold">ID Record</th>
                            <th scope="col" class="w-[25%] px-4 py-4 text-left font-extrabold">Deskripsi</th>
                            <th scope="col" class="w-[10%] px-4 py-4 text-left font-extrabold">Waktu</th>
                            <th scope="col" class="w-[5%] px-4 py-4 text-center font-extrabold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-500 bg-gray-200">
                        @forelse ($activities as $activity)
                            <tr class="[&>td]:border-b [&>td]:border-gray-400 hover:bg-gray-100">
                                <td class="px-4 py-3 align-top text-gray-500">
                                    {{ $loop->iteration + ($activities->currentPage() - 1) * $activities->perPage() }}
                                </td>
                                <td class="px-4 py-3 align-top break-words whitespace-normal">
                                    @if ($activity->causer)
                                        <div class="font-semibold text-gray-800">{{ $activity->causer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $activity->causer->email }}</div>
                                    @else
                                        <span class="text-gray-400 italic">Sistem</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @php
                                        $eventColors = [
                                            'created' => 'bg-green-100 text-green-700',
                                            'updated' => 'bg-blue-100 text-blue-700',
                                            'deleted' => 'bg-red-100 text-red-700',
                                        ];
                                        $color = $eventColors[$activity->event ?? ''] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                        {{ ucfirst($activity->event ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-top break-words whitespace-normal">
                                    <span class="text-xs font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-700">
                                        {{ class_basename($activity->subject_type ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-top text-gray-600">
                                    {{ $activity->subject_id ?? '-' }}
                                </td>
                                <td class="px-4 py-3 align-top break-words whitespace-normal text-gray-700">
                                    {{ $activity->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 align-top text-xs text-gray-600 whitespace-nowrap">
                                    <div>{{ $activity->created_at->format('d/m/Y') }}</div>
                                    <div class="text-gray-400">{{ $activity->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-4 py-3 align-top text-center">
                                    <a href="{{ route('activity-history.show', $activity->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 transition"
                                        title="Lihat Detail">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 4C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 12.5a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-gray-300">
                                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Tidak ada data aktivitas</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="flex flex-col gap-2 border-t border-gray-400 bg-gray-200 px-3 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-4 sm:py-4 md:px-5">
                <div class="text-xs font-semibold text-gray-800 sm:text-sm">
                    Showing {{ $activities->firstItem() ?? 0 }}–{{ $activities->lastItem() ?? 0 }} of
                    {{ $activities->total() }}
                </div>

                <div class="w-full overflow-x-auto sm:w-auto">
                    <div class="pagination">
                        {{ $activities->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
