@extends('admin.layout.master')

{{-- sidebar active --}}
@section('menu-profile', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <a href="#" class="text-blue-600 hover:underline">Profile</a>
        </div>
    </section>

    {{-- Profile Picture --}}
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <div class="text-sm font-semibold text-gray-800 mb-4">Profile Picture</div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-6">
            <div class="shrink-0">
                <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('assets/img/profile.png') }}" alt="Profile Picture"
                        class="w-full h-full object-cover">
                </div>
            </div>


            <div class="flex-1">
                <div class="font-bold text-gray-900">{{ auth()->user()->name }}</div>
                <div class="text-sm font-semibold text-gray-600">{{ auth()->user()->roles->first()?->name ?? 'No Role' }}
                </div>
                <div class="text-sm font-semibold text-gray-600">{{ auth()->user()->employee?->province ?? 'No Province' }}
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.edit-profile') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Personal Information --}}
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <div class="text-sm font-semibold text-gray-800 mb-4">Personal Information</div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <div class="text-xs font-bold text-gray-700">Nama Lengkap</div>
                <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->employee?->name ?? 'No Name' }}</div>
            </div>
            <div>
                <div class="text-xs font-bold text-gray-700">Tanggal Lahir</div>
                <div class="text-sm font-semibold text-gray-900">
                    {{ auth()->user()->employee?->birth_date ?? 'No Birth Date' }}</div>
            </div>

            <div>
                <div class="text-xs font-bold text-gray-700">Email Address</div>
                <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->email }}</div>
            </div>

            <div>
                <div class="text-xs font-bold text-gray-700">Phone Number</div>
                <div class="text-sm font-semibold text-gray-900">
                    {{ auth()->user()->employee?->phone ?? 'No Phone Number' }}</div>
            </div>

            <div>
                <div class="text-xs font-bold text-gray-700">User Role</div>
                <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->roles->first()?->name  ?? 'No Role' }}
                </div>
            </div>
        </div>
    </section>

    {{-- Alamat --}}
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <div class="text-sm font-semibold text-gray-800 mb-4">Alamat</div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <div class="text-xs font-bold text-gray-700">Negara</div>
                <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->employee?->country ?? 'No Country' }}
                </div>
            </div>

            <div>
                <div class="text-xs font-bold text-gray-700">Kab/Kota,Provinsi</div>
                <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->employee?->city ?? 'No City' }},
                    {{ auth()->user()->employee?->province ?? 'No Province' }}</div>
            </div>

            <div>
                <div class="text-xs font-bold text-gray-700">Kode Pos</div>
                <div class="text-sm font-semibold text-gray-900">
                    {{ auth()->user()->employee?->postal_code ?? 'No Postal Code' }}</div>
            </div>
        </div>
    </section>

    {{-- Berkas Pribadi --}}
    {{-- <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <div class="text-sm font-semibold text-gray-800 mb-4">Berkas Pribadi</div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-xs font-bold text-gray-700 mb-2">Upload KTP</div>
                <div class="flex w-full">
                    <label
                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-l-md bg-slate-700 text-white text-sm font-semibold cursor-pointer hover:bg-slate-800">
                        Input
                        <input type="file" class="hidden">
                    </label>
                    <input type="text" readonly
                        class="w-full rounded-r-md border border-gray-400 bg-white px-3 py-2.5 text-sm focus:ring-0 focus:border-gray-500"
                        placeholder="">
                </div>
            </div>

            <div>
                <div class="text-xs font-bold text-gray-700 mb-2">Upload KK</div>
                <div class="flex w-full">
                    <label
                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-l-md bg-slate-700 text-white text-sm font-semibold cursor-pointer hover:bg-slate-800">
                        Input
                        <input type="file" class="hidden">
                    </label>
                    <input type="text" readonly
                        class="w-full rounded-r-md border border-gray-400 bg-white px-3 py-2.5 text-sm focus:ring-0 focus:border-gray-500"
                        placeholder="">
                </div>
            </div>
        </div>
    </section> --}}
@endsection
