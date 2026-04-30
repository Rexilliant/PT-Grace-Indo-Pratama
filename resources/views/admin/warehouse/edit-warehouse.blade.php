@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@php
    $canEditWarehouse = auth()->user()->can('edit gudang');
    $canEditEmployee = auth()->user()->can('edit karyawan');
@endphp

@section('addCss')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-radius: 0.375rem !important;
            border: 1px solid #9CA3AF !important;
            display: flex !important;
            align-items: center !important;
            padding-left: 10px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            padding-left: 0 !important;
            font-size: 0.875rem;
            font-weight: 600;
            color: #111827;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        .select2-dropdown {
            border-radius: 0.375rem !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .readonly-style {
            background-color: #F9FAFB !important;
            cursor: not-allowed !important;
        }
    </style>
@endsection

@section('content')
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Edit Gudang</span>
        </div>

        @unless ($canEditWarehouse)
            <div class="rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm font-medium text-yellow-800">
                Anda hanya memiliki akses baca. Data gudang tidak dapat diubah.
            </div>
        @endunless
    </section>

    <form action="{{ route('update-warehouse', $warehouse->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <input type="hidden" name="province" id="provinceNameInput" value="{{ old('province', $warehouse->province) }}">

        <section class="rounded-lg border border-gray-300 bg-white p-5 shadow">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">
                        Nama Gudang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $warehouse->name) }}"
                        placeholder="Masukkan nama gudang"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-gray-500 focus:ring-0 {{ $canEditWarehouse ? 'bg-white' : 'readonly-style' }}"
                        {{ $canEditWarehouse ? '' : 'readonly' }} />
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">
                        Type Gudang <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="typeSelect"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 {{ $canEditWarehouse ? '' : 'readonly-style' }}"
                        {{ $canEditWarehouse ? '' : 'disabled' }}>
                        <option value="">-- Pilih Type --</option>
                        <option value="pemasaran" {{ old('type', $warehouse->type) == 'pemasaran' ? 'selected' : '' }}>
                            Pemasaran
                        </option>
                        <option value="produksi" {{ old('type', $warehouse->type) == 'produksi' ? 'selected' : '' }}>
                            Produksi
                        </option>
                    </select>

                    @unless ($canEditWarehouse)
                        <input type="hidden" name="type" value="{{ old('type', $warehouse->type) }}">
                    @endunless

                    @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">
                        Provinsi <span class="text-red-500">*</span>
                    </label>
                    <select name="province_id" id="provinceSelect"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 {{ $canEditWarehouse ? '' : 'readonly-style' }}"
                        {{ $canEditWarehouse ? '' : 'disabled' }}>
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach ($provinces as $prov)
                            <option value="{{ $prov['province_id'] }}" data-name="{{ $prov['province_name'] }}"
                                {{ old('province_id', $selectedProvinceId) == $prov['province_id'] ? 'selected' : '' }}>
                                {{ $prov['province_name'] }}
                            </option>
                        @endforeach
                    </select>

                    @unless ($canEditWarehouse)
                        <input type="hidden" name="province_id" value="{{ old('province_id', $selectedProvinceId) }}">
                    @endunless

                    @error('province')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('province_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">
                        Kota / Kabupaten <span class="text-red-500">*</span>
                    </label>
                    <select name="city" id="citySelect"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 {{ $canEditWarehouse ? '' : 'readonly-style' }}"
                        {{ $canEditWarehouse ? '' : 'disabled' }}>
                        <option value="">-- Pilih Kota / Kabupaten --</option>
                    </select>

                    @unless ($canEditWarehouse)
                        <input type="hidden" name="city" value="{{ old('city', $warehouse->city) }}">
                    @endunless

                    <p id="cityLoadingText" class="mt-1 hidden text-xs text-gray-500">
                        Memuat kota / kabupaten...
                    </p>

                    @error('city')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('warehouses') }}"
                class="inline-flex items-center justify-center rounded-lg bg-gray-500 px-10 py-3 text-sm font-bold text-white hover:bg-gray-600">
                Kembali
            </a>

            @if ($canEditWarehouse)
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                    Update
                </button>
            @endif
        </div>
    </form>

    <section class="mt-6 rounded-lg border border-gray-300 bg-white p-5 shadow">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800">Data Karyawan Gudang Ini</h2>
            <p class="text-sm text-gray-500">
                Daftar karyawan yang terhubung ke gudang <span class="font-semibold">{{ $warehouse->name }}</span>
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-200 px-4 py-3 text-left font-bold text-gray-700">No</th>
                        <th class="border border-gray-200 px-4 py-3 text-left font-bold text-gray-700">NIP</th>
                        <th class="border border-gray-200 px-4 py-3 text-left font-bold text-gray-700">Nama</th>
                        <th class="border border-gray-200 px-4 py-3 text-left font-bold text-gray-700">Email</th>
                        <th class="border border-gray-200 px-4 py-3 text-left font-bold text-gray-700">No. HP</th>
                        <th class="border border-gray-200 px-4 py-3 text-left font-bold text-gray-700">Jabatan</th>
                        <th class="border border-gray-200 px-4 py-3 text-left font-bold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-200 px-4 py-3">{{ $loop->iteration }}</td>
                            <td class="border border-gray-200 px-4 py-3 text-gray-700">
                                {{ $employee->nip ?? '-' }}
                            </td>
                            <td class="border border-gray-200 px-4 py-3 font-semibold text-gray-800">
                                {{ $employee->name }}
                            </td>
                            <td class="border border-gray-200 px-4 py-3 text-gray-700">
                                {{ $employee->email ?? '-' }}
                            </td>
                            <td class="border border-gray-200 px-4 py-3 text-gray-700">
                                {{ $employee->phone ?? '-' }}
                            </td>
                            <td class="border border-gray-200 px-4 py-3 text-gray-700">
                                {{ $employee->position ?? '-' }}
                            </td>
                            <td class="border border-gray-200 px-4 py-3">
                                @if ($canEditEmployee)
                                    <a href="{{ route('edit.employee', $employee->id) }}"
                                        class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-xs font-bold text-white hover:bg-blue-700">
                                        Edit
                                    </a>
                                @else
                                    <span
                                        class="inline-flex cursor-not-allowed items-center justify-center rounded-md bg-gray-300 px-3 py-2 text-xs font-bold text-gray-600">
                                        Lihat Saja
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
                                Belum ada karyawan yang terdaftar di gudang ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const canEditWarehouse = @json($canEditWarehouse);
            const oldProvinceId = @json(old('province_id', $selectedProvinceId));
            const oldCity = @json(old('city', $warehouse->city));

            initTypeSelect();
            initResponsibleSelect();
            initProvinceSelect();
            initCitySelect();

            if (oldProvinceId) {
                syncProvinceName();
                loadCities(oldProvinceId, oldCity);
            }

            if (canEditWarehouse) {
                $('#provinceSelect').on('change', function() {
                    const provinceId = $(this).val();

                    syncProvinceName();
                    loadCities(provinceId, '');
                });
            }
        });

        function initTypeSelect() {
            if ($('#typeSelect').hasClass('select2-hidden-accessible')) {
                $('#typeSelect').select2('destroy');
            }

            $('#typeSelect').select2({
                placeholder: '-- Pilih Type --',
                allowClear: true,
                width: '100%'
            });
        }

        function initResponsibleSelect() {
            if ($('#responsibleSelect').length) {
                $('#responsibleSelect').select2({
                    placeholder: '-- Pilih Penanggung Jawab --',
                    allowClear: true,
                    width: '100%'
                });
            }
        }

        function initProvinceSelect() {
            if ($('#provinceSelect').hasClass('select2-hidden-accessible')) {
                $('#provinceSelect').select2('destroy');
            }

            $('#provinceSelect').select2({
                placeholder: '-- Pilih Provinsi --',
                allowClear: true,
                width: '100%'
            });
        }

        function initCitySelect() {
            if ($('#citySelect').hasClass('select2-hidden-accessible')) {
                $('#citySelect').select2('destroy');
            }

            $('#citySelect').select2({
                placeholder: '-- Pilih Kota / Kabupaten --',
                allowClear: true,
                width: '100%'
            });
        }

        function syncProvinceName() {
            const selectedOption = $('#provinceSelect').find(':selected');
            const provinceName = selectedOption.data('name') || '';
            $('#provinceNameInput').val(provinceName);
        }

        async function loadCities(provinceId, selectedCity = '') {
            $('#cityLoadingText').removeClass('hidden');

            if (!provinceId) {
                renderCityOptions([], '');
                $('#cityLoadingText').addClass('hidden');
                return;
            }

            try {
                const response = await fetch(`/admin/warehouses/cities/${provinceId}`);
                const cities = await response.json();

                renderCityOptions(Array.isArray(cities) ? cities : [], selectedCity);
            } catch (error) {
                console.error(error);

                renderCityOptions([], '');

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Data kota / kabupaten gagal dimuat',
                    confirmButtonColor: '#dc2626'
                });
            } finally {
                $('#cityLoadingText').addClass('hidden');
            }
        }

        function renderCityOptions(cities, selectedCity = '') {
            const $city = $('#citySelect');

            $city.empty();
            $city.append('<option value="">-- Pilih Kota / Kabupaten --</option>');

            cities.forEach(function(city) {
                const isSelected = city.name === selectedCity ? 'selected' : '';
                $city.append(`<option value="${city.name}" ${isSelected}>${city.name}</option>`);
            });

            initCitySelect();

            if (selectedCity) {
                $city.val(selectedCity).trigger('change');
            }
        }
    </script>
@endsection
