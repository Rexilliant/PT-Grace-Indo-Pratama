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
            <a href="{{ route('activity-history') }}" class="text-blue-600 hover:underline">Riwayat Aktivitas</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-gray-500">Detail #{{ $activity->id }}</span>
        </div>
    </section>

    <section class="mb-5 max-w-4xl">

        {{-- Header Card --}}
        <div class="rounded-lg border border-gray-300 bg-white shadow overflow-hidden mb-4">
            <div class="bg-[#5aba6f]/70 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- Event Badge --}}
                    @php
                        $eventColors = [
                            'created' => 'bg-green-500',
                            'updated' => 'bg-blue-500',
                            'deleted' => 'bg-red-500',
                        ];
                        $eventColor = $eventColors[$activity->event ?? ''] ?? 'bg-gray-500';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold text-white {{ $eventColor }}">
                        {{ strtoupper($activity->event ?? 'UNKNOWN') }}
                    </span>
                    <span class="text-gray-800 font-semibold text-lg">
                        Detail Aktivitas
                    </span>
                </div>
                <a href="{{ route('activity-history') }}"
                    class="flex items-center gap-1 text-sm text-gray-700 hover:text-gray-900 font-medium bg-white/60 hover:bg-white/90 px-3 py-1.5 rounded-lg transition">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- Info Utama --}}
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Pengguna --}}
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pengguna</span>
                    @if ($activity->causer)
                        <div class="flex items-center gap-2">
                            <span class="h-8 w-8 rounded-full bg-[#53BF6A]/20 grid place-items-center text-[#275931] font-bold text-sm">
                                {{ strtoupper(substr($activity->causer->name, 0, 1)) }}
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800 text-sm">{{ $activity->causer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $activity->causer->email }}</div>
                            </div>
                        </div>
                    @else
                        <span class="text-gray-400 italic text-sm">Sistem / Tidak diketahui</span>
                    @endif
                </div>

                {{-- Model --}}
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Model</span>
                    <div>
                        <span class="font-mono text-sm bg-gray-100 px-2 py-0.5 rounded text-gray-700">
                            {{ class_basename($activity->subject_type ?? '-') }}
                        </span>
                        <span class="text-xs text-gray-400 ml-2">(ID: {{ $activity->subject_id ?? '-' }})</span>
                    </div>
                    @if ($activity->subject_type)
                        <div class="text-xs text-gray-400 font-mono">{{ $activity->subject_type }}</div>
                    @endif
                </div>

                {{-- Deskripsi --}}
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Deskripsi</span>
                    <span class="text-sm text-gray-700">{{ $activity->description ?? '-' }}</span>
                </div>

                {{-- Waktu --}}
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu</span>
                    <span class="text-sm text-gray-700">{{ $activity->created_at->format('d M Y, H:i:s') }}</span>
                    <span class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                </div>

                {{-- Log Name --}}
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Log Name</span>
                    <span class="text-sm text-gray-700">{{ $activity->log_name ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Properties --}}
        @php
            $changesRaw = $activity->attribute_changes;
            $changesArr = is_string($changesRaw) ? json_decode($changesRaw, true) : (is_array($changesRaw) ? $changesRaw : (is_object($changesRaw) && method_exists($changesRaw, 'toArray') ? $changesRaw->toArray() : (array) $changesRaw));
            
            if (empty($changesArr) && $activity->properties) {
                $changesArr = is_string($activity->properties) ? json_decode($activity->properties, true) : (is_object($activity->properties) && method_exists($activity->properties, 'toArray') ? $activity->properties->toArray() : (array) $activity->properties);
            }
        @endphp

        @if (!empty($changesArr))
            <div class="rounded-lg border border-gray-300 bg-white shadow overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Perubahan Data</h3>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Perubahan Data (Combined) --}}
                    @php
                        $oldData = $changesArr['old'] ?? [];
                        if (!is_array($oldData)) $oldData = [];
                        
                        $newData = $changesArr['attributes'] ?? $changesArr['new'] ?? [];
                        if (!is_array($newData)) $newData = [];
                        
                        $allKeys = collect(array_keys($oldData))->merge(array_keys($newData))->unique()->values();
                    @endphp

                    @if ($allKeys->isNotEmpty())
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                                <h4 class="text-sm font-bold text-blue-600">Detail Perubahan</h4>
                            </div>
                            <div class="rounded-lg border border-blue-100 bg-white overflow-hidden shadow-sm">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-blue-50 border-b border-blue-100">
                                            <th class="px-4 py-3 text-left font-semibold text-blue-800 w-1/4">Field</th>
                                            <th class="px-4 py-3 text-left font-semibold text-blue-800 w-3/8 border-l border-blue-100">Nilai Sebelum</th>
                                            <th class="px-4 py-3 text-left font-semibold text-blue-800 w-3/8 border-l border-blue-100">Nilai Sesudah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($allKeys as $key)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3 font-mono text-xs text-gray-700 font-semibold align-top">{{ $key }}</td>
                                                
                                                {{-- Nilai Sebelum --}}
                                                <td class="px-4 py-3 text-gray-700 break-all align-top border-l border-gray-100 {{ array_key_exists($key, $oldData) && array_key_exists($key, $newData) && $oldData[$key] != $newData[$key] ? 'bg-red-50/30' : '' }}">
                                                    @if (!array_key_exists($key, $oldData))
                                                        <span class="italic text-gray-400">-</span>
                                                    @elseif (is_null($oldData[$key]))
                                                        <span class="italic text-gray-400">null</span>
                                                    @elseif (is_array($oldData[$key]))
                                                        <pre class="text-xs bg-red-100/50 text-red-800 rounded p-2 overflow-x-auto">{{ json_encode($oldData[$key], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    @else
                                                        <span class="text-red-700">{{ $oldData[$key] }}</span>
                                                    @endif
                                                </td>

                                                {{-- Nilai Sesudah --}}
                                                <td class="px-4 py-3 text-gray-700 break-all align-top border-l border-gray-100 {{ array_key_exists($key, $oldData) && array_key_exists($key, $newData) && $oldData[$key] != $newData[$key] ? 'bg-green-50/30' : '' }}">
                                                    @if (!array_key_exists($key, $newData))
                                                        <span class="italic text-gray-400">-</span>
                                                    @elseif (is_null($newData[$key]))
                                                        <span class="italic text-gray-400">null</span>
                                                    @elseif (is_array($newData[$key]))
                                                        <pre class="text-xs bg-green-100/50 text-green-800 rounded p-2 overflow-x-auto">{{ json_encode($newData[$key], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    @else
                                                        <span class="text-green-700 font-medium">{{ $newData[$key] }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Raw Properties jika tidak ada old/attributes --}}
                    @if (empty($oldData) && empty($newData))
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                <h4 class="text-sm font-bold text-gray-600">Properties</h4>
                            </div>
                            <pre class="text-xs bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-x-auto text-gray-700">{{ json_encode($changesArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif

                </div>
            </div>
        @else
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-center text-gray-400 text-sm">
                Tidak ada detail perubahan data untuk aktivitas ini.
            </div>
        @endif

    </section>
@endsection
