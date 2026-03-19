@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-warehouse', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
            <span class="text-blue-600">Tambah Gudang</span>
        </div>
    </section>

    <form action="{{ route('store-warehouse') }}" method="POST" class="space-y-4" x-data="warehouseForm({
        oldProvince: @js(old('province')),
        oldProvinceId: @js(old('province_id')),
        oldCity: @js(old('city'))
    })"
        x-init="init()">
        @csrf

        <input type="hidden" name="province" id="provinceNameInput" value="{{ old('province') }}">

        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">
                        Nama Gudang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama gudang"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                    @error('name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
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
                            <option value="{{ $user->id }}" {{ old('responsible_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('responsible_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
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
                                {{ old('province_id') == $prov['province_id'] ? 'selected' : '' }}>
                                {{ $prov['province_name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('province')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    @error('province_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
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

                    <p x-show="loadingCities" class="text-xs text-gray-500 mt-1">
                        Memuat kota / kabupaten...
                    </p>

                    @error('city')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function warehouseForm({
            oldProvince = '',
            oldProvinceId = '',
            oldCity = ''
        }) {
            return {
                oldProvince,
                oldProvinceId,
                oldCity,
                selectedProvince: oldProvinceId || '',
                selectedCity: oldCity || '',
                cities: [],
                loadingCities: false,

                init() {
                    this.$nextTick(() => {
                        this.initResponsibleSelect2();
                        this.initProvinceSelect2();
                        this.initCitySelect2();

                        if (this.selectedProvince) {
                            this.syncProvinceName();
                            this.fetchCities(true);
                        }
                    });
                },

                initResponsibleSelect2() {
                    if ($('#responsibleSelect').hasClass('select2-hidden-accessible')) {
                        $('#responsibleSelect').select2('destroy');
                    }

                    $('#responsibleSelect').select2({
                        placeholder: '-- Pilih Penanggung Jawab --',
                        allowClear: true,
                        width: '100%'
                    });
                },

                initProvinceSelect2() {
                    const self = this;
                    const $province = $('#provinceSelect');

                    if ($province.hasClass('select2-hidden-accessible')) {
                        $province.select2('destroy');
                    }

                    $province.select2({
                        placeholder: '-- Pilih Provinsi --',
                        allowClear: true,
                        width: '100%'
                    });

                    if (this.selectedProvince) {
                        $province.val(this.selectedProvince).trigger('change.select2');
                    }

                    $province.off('select2:select select2:clear');

                    $province.on('select2:select', function(e) {
                        self.selectedProvince = e.params.data.id || '';
                        self.syncProvinceName();
                        self.fetchCities(false);
                    });

                    $province.on('select2:clear', function() {
                        self.selectedProvince = '';
                        $('#provinceNameInput').val('');
                        self.fetchCities(false);
                    });
                },

                initCitySelect2() {
                    const self = this;
                    const $city = $('#citySelect');

                    if ($city.hasClass('select2-hidden-accessible')) {
                        $city.select2('destroy');
                    }

                    $city.select2({
                        placeholder: '-- Pilih Kota / Kabupaten --',
                        allowClear: true,
                        width: '100%'
                    });

                    if (this.selectedCity) {
                        $city.val(this.selectedCity).trigger('change.select2');
                    }

                    $city.off('select2:select select2:clear');

                    $city.on('select2:select', function(e) {
                        self.selectedCity = e.params.data.id || '';
                    });

                    $city.on('select2:clear', function() {
                        self.selectedCity = '';
                    });
                },

                syncProvinceName() {
                    const selectedOption = $('#provinceSelect').find(':selected');
                    const provinceName = selectedOption.data('name') || '';
                    $('#provinceNameInput').val(provinceName);
                },

                async fetchCities(isInitial = false) {
                    this.loadingCities = true;

                    if (!this.selectedProvince) {
                        this.cities = [];
                        this.selectedCity = '';
                        this.renderCityOptions();
                        this.initCitySelect2();
                        this.loadingCities = false;
                        return;
                    }

                    if (!isInitial) {
                        this.selectedCity = '';
                    }

                    try {
                        const response = await fetch(`/admin/warehouses/cities/${this.selectedProvince}`);
                        const data = await response.json();

                        this.cities = Array.isArray(data) ? data : [];

                        this.renderCityOptions();
                        this.initCitySelect2();

                        if (isInitial && this.oldCity) {
                            const exists = this.cities.find(city => city.name === this.oldCity);

                            if (exists) {
                                this.selectedCity = this.oldCity;
                                $('#citySelect').val(this.oldCity).trigger('change.select2');
                            }
                        }
                    } catch (error) {
                        console.error(error);

                        this.cities = [];
                        this.selectedCity = '';
                        this.renderCityOptions();
                        this.initCitySelect2();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data kota / kabupaten gagal dimuat',
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    } finally {
                        this.loadingCities = false;
                    }
                },

                renderCityOptions() {
                    const $city = $('#citySelect');
                    $city.empty();
                    $city.append('<option value="">-- Pilih Kota / Kabupaten --</option>');

                    this.cities.forEach(city => {
                        $city.append(`<option value="${city.name}">${city.name}</option>`);
                    });
                }
            }
        }
    </script>
@endsection
