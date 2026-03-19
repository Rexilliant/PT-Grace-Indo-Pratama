@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-warehouse', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

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
    </style>
@endsection

@section('content')
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Edit Gudang</span>
        </div>
    </section>

    <form action="{{ route('update-warehouse', $warehouse->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- yang disimpan ke database: nama provinsi --}}
        <input type="hidden" name="province" id="provinceNameInput" value="{{ old('province', $warehouse->province) }}">

        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">
                        Nama Gudang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $warehouse->name) }}"
                        placeholder="Masukkan nama gudang"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">
                        Penanggung Jawab
                    </label>
                    <select name="responsible_id" id="responsibleSelect"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900">
                        <option value="">-- Pilih Penanggung Jawab --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('responsible_id', $warehouse->responsible_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('responsible_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">
                        Provinsi <span class="text-red-500">*</span>
                    </label>
                    <select name="province_id" id="provinceSelect"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900">
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach ($provinces as $prov)
                            <option value="{{ $prov['province_id'] }}" data-name="{{ $prov['province_name'] }}"
                                {{ old('province_id', $selectedProvinceId) == $prov['province_id'] ? 'selected' : '' }}>
                                {{ $prov['province_name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('province')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('province_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">
                        Kota / Kabupaten <span class="text-red-500">*</span>
                    </label>
                    <select name="city" id="citySelect"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900">
                        <option value="">-- Pilih Kota / Kabupaten --</option>
                    </select>
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
                Batal
            </a>

            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Update
            </button>
        </div>
    </form>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const oldProvinceId = @json(old('province_id', $selectedProvinceId));
            const oldCity = @json(old('city', $warehouse->city));

            initResponsibleSelect();
            initProvinceSelect();
            initCitySelect();

            if (oldProvinceId) {
                syncProvinceName();
                loadCities(oldProvinceId, oldCity);
            }

            $('#provinceSelect').on('change', function() {
                const provinceId = $(this).val();

                syncProvinceName();
                loadCities(provinceId, '');
            });
        });

        function initResponsibleSelect() {
            $('#responsibleSelect').select2({
                placeholder: '-- Pilih Penanggung Jawab --',
                allowClear: true,
                width: '100%'
            });
        }

        function initProvinceSelect() {
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
