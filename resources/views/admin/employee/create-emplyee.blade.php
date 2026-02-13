@extends('admin.layout.master')

{{-- sidebar active --}}
@section('menu-profile', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('addCss')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endsection
@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Profile</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Edit</a>
        </div>
    </section>

    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Profile Picture --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="text-sm font-semibold text-gray-800 mb-4">Profile Picture</div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                <div class="shrink-0">
                    <div class="w-20  h-20 rounded-full bg-black flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('assets/img/profile.png') }}" alt="Profile Picture"
                            class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Masukan Foto Baru</div>

                    <div class="flex w-full max-w-xl">
                        <label
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-l-md bg-slate-700 text-white text-sm font-semibold cursor-pointer hover:bg-slate-800">
                            Input
                            <input id="photo" name="photo" type="file" class="hidden" accept="image/*">
                        </label>
                        <input id="photo_name" type="text" readonly
                            class="w-full rounded-r-md border border-gray-400 bg-white px-3 py-2.5 text-sm focus:ring-0 focus:border-gray-500"
                            placeholder="">
                    </div>
                </div>
            </div>
        </section>

        {{-- Personal Information --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="text-sm font-semibold text-gray-800 mb-4">Personal Information</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap</label>
                    <input name="name" value=""
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Lahir</label>
                    <input type="date" name="bithday" value=""
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" value=""
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Nomor Telepon</label>
                    <input name="phone" value=""
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" value=""
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>
            </div>
        </section>

        {{-- Alamat --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="text-sm font-semibold text-gray-800 mb-4">Alamat</div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @php
                    $countries = \PragmaRX\Countries\Package\Countries::all()
                        ->sortBy('name.common')
                        ->pluck('name.common')
                        ->values();
                @endphp

                {{-- Negara (package) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Negara</label>
                    <select name="country" id="country"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        @foreach ($countries as $c)
                            <option value="{{ $c['countryCode'] }}">{{ $c['countryName'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Provinsi (manual) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Provinsi</label>
                    <input name="province" placeholder="Contoh: Sumatera Utara" value="{{ old('province') }}"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                {{-- Kab / Kota (manual) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Kab / Kota</label>
                    <input name="city" placeholder="Contoh: Medan" value="{{ old('city') }}"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                {{-- Kode Pos (manual) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Kode Pos</label>
                    <input name="postal_code" placeholder="Contoh: 20111" value="{{ old('postal_code') }}"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>
            </div>
        </section>


        {{-- JS dependent dropdown --}}
        <script>
            const province = document.getElementById('province_code');
            const regency = document.getElementById('regency_code');
            const postal = document.getElementById('postal_code');

            const resetSelect = (el, placeholder) => {
                el.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
                el.disabled = true;
            };

            const fillSelect = (el, items, placeholder) => {
                el.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
                items.forEach(it => {
                    const opt = document.createElement('option');
                    opt.value = it.code ?? it.value ?? it;
                    opt.textContent = it.name ?? it.label ?? it;
                    el.appendChild(opt);
                });
                el.disabled = false;
            };

            province?.addEventListener('change', async () => {
                resetSelect(regency, 'Loading kab/kota...');
                resetSelect(postal, 'Pilih kode pos');

                const provinceCode = province.value;
                const res = await fetch(`/admin/api/regions/provinces/${provinceCode}/regencies`);
                const data = await res.json();

                fillSelect(regency, data, 'Pilih kab/kota');
            });

            regency?.addEventListener('change', async () => {
                resetSelect(postal, 'Loading kode pos...');

                const regencyCode = regency.value;
                const res = await fetch(`/admin/api/regions/regencies/${regencyCode}/postal-codes`);
                const data = await res.json();

                // data = ["20111","20112",...]
                fillSelect(postal, data.map(v => ({
                    code: v,
                    name: v
                })), 'Pilih kode pos');
            });
        </script>


        {{-- Berkas Pribadi --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="text-sm font-semibold text-gray-800 mb-4">Berkas Pribadi</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Upload KTP</label>
                    <div class="flex w-full">
                        <label
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-l-md bg-slate-700 text-white text-sm font-semibold cursor-pointer hover:bg-slate-800">
                            Input
                            <input id="ktp_1" name="ktp_1" type="file" class="hidden">
                        </label>
                        <input id="ktp_1_name" type="text" readonly
                            class="w-full rounded-r-md border border-gray-400 bg-white px-3 py-2.5 text-sm focus:ring-0 focus:border-gray-500"
                            placeholder="">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Upload KK</label>
                    <div class="flex w-full">
                        <label
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-l-md bg-slate-700 text-white text-sm font-semibold cursor-pointer hover:bg-slate-800">
                            Input
                            <input id="ktp_2" name="ktp_2" type="file" class="hidden">
                        </label>
                        <input id="ktp_2_name" type="text" readonly
                            class="w-full rounded-r-md border border-gray-400 bg-white px-3 py-2.5 text-sm focus:ring-0 focus:border-gray-500"
                            placeholder="">
                    </div>
                </div>
            </div>
        </section>

        {{-- actions --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="button" onclick="openCancelModal()"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </button>


            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan
            </button>
        </div>

        <div id="cancelModal"
            class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">

            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 animate-scale-in">
                {{-- header --}}
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">
                        Batalkan Perubahan?
                    </h3>
                </div>

                {{-- body --}}
                <div class="px-6 py-4 text-sm text-gray-700 leading-relaxed">
                    Perubahan yang sudah kamu lakukan <span class="font-semibold">belum disimpan</span>.
                    Jika dibatalkan, semua perubahan akan hilang.
                </div>

                {{-- footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                    <button onclick="closeCancelModal()"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 hover:bg-gray-300">
                        Tetap di Halaman
                    </button>

                    <a href="{{ route('admin.profile') }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                        Ya, Batalkan
                    </a>
                </div>
            </div>
        </div>

    </form>

    {{-- file name preview --}}
    <script>
        const bindFileName = (fileId, textId) => {
            const f = document.getElementById(fileId);
            const t = document.getElementById(textId);
            if (!f || !t) return;
            f.addEventListener('change', () => {
                t.value = f.files?.[0]?.name || '';
            });
        };

        bindFileName('photo', 'photo_name');
        bindFileName('ktp_1', 'ktp_1_name');
        bindFileName('ktp_2', 'ktp_2_name');
    </script>

    <script>
        const modal = document.getElementById('cancelModal');

        function openCancelModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCancelModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#country').select2();
        });
    </script>
@endsection
