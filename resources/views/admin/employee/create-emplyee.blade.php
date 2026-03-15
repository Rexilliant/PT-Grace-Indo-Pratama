@extends('admin.layout.master')

@section('addCss')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- FilePond (drag & drop + preview) --}}
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />

    <style>
        .select2-container .select2-selection--single {
        width: 100%;
            height: 42px;
            border-radius: 0.375rem;
            border: 1px solid #9CA3AF;
            display: flex;
            align-items: center;
            padding-left: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }

        .select2-dropdown {
            border-radius: 0.375rem;
        }
    </style>
@endsection

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Karyawan</span>
            <span class="mx-1 text-gray-400">/</span>
            <span class="text-blue-600">Tambah Karyawan</span>
        </div>
    </section>

    <section x-data="employeeForm()" x-init="init()"
        class="p-5 rounded-md shadow-sm border border-slate-200 bg-white">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 mb-5">Tambah Karyawan</h1>

        <form action="{{ route('admin.store-employee') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-5">
                {{-- Profile Image --}}
                <div class="lg:row-span-3 block">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Foto Profil</label>

                    <input type="file" name="profile_image" id="profile_image" accept="image/*" class="block h-[300px]" />

                    @error('profile_image')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Birthday --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Tanggal Lahir</label>
                    <input type="date" name="birthday" value="{{ old('birthday') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                    @error('birthday')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">No. Hp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                    @error('phone')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Position --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Jabatan</label>
                    <input type="text" name="position" value="{{ old('position') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                    @error('position')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Country --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Negara</label>
                    <select x-ref="country" name="country"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                        <option value="">-- Select Country --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country['countryName'] }}" data-code="{{ $country['countryCode'] }}"
                                {{ old('country', 'Indonesia') == $country['countryName'] ? 'selected' : '' }}>
                                {{ $country['countryName'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('country')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Province --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Provinsi</label>
                    <select x-ref="province" name="province"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                    </select>
                    @error('province')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- City --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Kota</label>
                    <select x-ref="city" name="city"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                    @error('city')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Postal Code --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                    @error('postal_code')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Alamat Lengkap</label>
                    <textarea name="address" rows="3"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-600/30">
                    Simpan
                </button>
            </div>
        </form>
    </section>
@endsection

@section('addJs')
    {{-- jQuery + Select2 --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- FilePond --}}
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

    <script>
        FilePond.registerPlugin(FilePondPluginImagePreview);

        FilePond.create(document.querySelector('#profile_image'), {
            storeAsFile: true,
            instantUpload: false,
            allowMultiple: false,
            credits: false,
            stylePanelLayout: 'compact',
            styleButtonRemoveItemPosition: 'right',
            labelIdle: 'Drag & Drop foto di sini atau <span class="filepond--label-action">Browse</span>',
        });
    </script>

    <script>
        function employeeForm() {
            return {
                countryCode: null,

                init() {
                    if ($(this.$refs.country).hasClass('select2-hidden-accessible')) return;

                    $(this.$refs.country).select2({
                        placeholder: 'Select Country',
                        allowClear: true,
                        width: '100%'
                    });

                    $(this.$refs.province).select2({
                        placeholder: 'Select Province',
                        allowClear: true,
                        width: '100%'
                    });

                    $(this.$refs.city).select2({
                        placeholder: 'Select City',
                        allowClear: true,
                        width: '100%'
                    });

                    this.countryCode = this.getSelectedCountryCode();

                    $(this.$refs.country).on('change', async () => {
                        this.countryCode = this.getSelectedCountryCode();
                        await this.loadProvinces();
                        this.resetCities(false);
                    });

                    $(this.$refs.province).on('change', async () => {
                        await this.loadCities();
                    });

                    if (this.countryCode) {
                        this.loadProvinces();
                    }
                },

                getSelectedCountryCode() {
                    const opt = this.$refs.country.options[this.$refs.country.selectedIndex];
                    return opt ? opt.dataset.code : null;
                },

                resetProvinces(loading = false) {
                    $(this.$refs.province).html(`
                        <option value="">${loading ? 'Loading...' : '-- Select Province --'}</option>
                    `).val('').trigger('change');
                },

                resetCities(loading = false) {
                    $(this.$refs.city).html(`
                        <option value="">${loading ? 'Loading...' : '-- Select City --'}</option>
                    `).val('').trigger('change');
                },

                async loadProvinces() {
                    this.resetProvinces(true);
                    this.resetCities(false);

                    if (!this.countryCode) {
                        this.resetProvinces(false);
                        return;
                    }

                    const res = await fetch(`/admin/employees/get-provinces/${this.countryCode}`);
                    if (!res.ok) {
                        console.error(await res.text());
                        this.resetProvinces(false);
                        return;
                    }

                    const data = await res.json();

                    let options = `<option value="">-- Select Province --</option>`;
                    data.forEach(item => {
                        options +=
                            `<option value="${item.name}" data-admin-code1="${item.adminCode1}">${item.name}</option>`;
                    });

                    $(this.$refs.province).html(options).val('').trigger('change');
                },

                async loadCities() {
                    this.resetCities(true);

                    const selectedProvince = $(this.$refs.province).val();
                    if (!this.countryCode || !selectedProvince) {
                        this.resetCities(false);
                        return;
                    }

                    const provinceOption = this.$refs.province.querySelector(`option[value="${selectedProvince}"]`);
                    const adminCode1 = provinceOption ? provinceOption.dataset.adminCode1 : null;

                    const qs = new URLSearchParams({
                        adminCode1
                    }).toString();

                    const res = await fetch(`/admin/employees/get-cities/${this.countryCode}?${qs}`);
                    if (!res.ok) {
                        console.error(await res.text());
                        this.resetCities(false);
                        return;
                    }

                    const data = await res.json();

                    let options = `<option value="">-- Select City --</option>`;
                    data.forEach(item => {
                        options += `<option value="${item.name}">${item.name}</option>`;
                    });

                    $(this.$refs.city).html(options).val('').trigger('change');
                }
            }
        }
    </script>

    <script src="{{ asset('assets/js/sweetalert.js') }}"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2563eb'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc2626'
            });
        </script>
    @endif
@endsection
