@extends('admin.layout.master')

@section('addCss')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

    {{-- FilePond --}}
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />

    <style>
        .filepond--root {
            height: 320px;
        }

        .filepond--panel-root {
            border-radius: 1rem;
            background-color: rgb(248 250 252);
            border: 1px solid rgb(226 232 240);
            height: 100%;
        }

        .filepond--image-preview-wrapper {
            height: 100% !important;
        }

        .filepond--image-preview {
            height: 100% !important;
            object-fit: cover;
        }

        .ts-wrapper.single .ts-control {
            min-height: 50px;
            border-radius: 1rem;
            border: 1px solid rgb(226 232 240);
            background-color: rgb(248 250 252);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: rgb(15 23 42);
            box-shadow: none;
        }

        .ts-wrapper.focus .ts-control {
            border-color: rgb(147 197 253);
            box-shadow: 0 0 0 4px rgb(37 99 235 / 0.2);
            background-color: white;
        }

        .ts-dropdown {
            border-radius: 0.75rem;
            border: 1px solid rgb(226 232 240);
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <section class="mb-6">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Karyawan</span>
            <span class="mx-1 text-gray-400">/</span>
            <span class="text-blue-600">Edit Karyawan</span>
        </div>
    </section>

    <section x-data="employeeForm()" x-init="init()" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit Karyawan</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui data karyawan di bawah ini.</p>
        </div>

        <form action="{{ route('update.employee', ['id' => $employee->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Foto Profil --}}
                <div class="lg:col-span-1">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Foto Profil</label>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*" />

                    @error('profile_image')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Form --}}
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        {{-- Nama --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                                focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                            @error('name')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Tanggal Lahir</label>
                            <input type="date" name="birthday"
                                value="{{ old('birthday', optional($employee->birthday)->format('Y-m-d') ?? $employee->birthday) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                                focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                            @error('birthday')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Email</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                                focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                            @error('email')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No HP --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800">No. Hp</label>
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                                focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                            @error('phone')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jabatan --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Jabatan</label>
                            <input type="text" name="position" value="{{ old('position', $employee->position) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                                focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                            @error('position')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Gudang --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Gudang</label>
                            <select name="warehouse_id"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                                <option value="">-- Pilih Gudang --</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"
                                        {{ old('warehouse_id', $employee->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Negara --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Negara</label>
                            <select x-ref="country" name="country"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                                <option value="">-- Select Country --</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country['countryName'] }}"
                                        data-code="{{ $country['countryCode'] }}"
                                        {{ old('country', $employee->country ?? 'Indonesia') == $country['countryName'] ? 'selected' : '' }}>
                                        {{ $country['countryName'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Provinsi --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Provinsi</label>
                            <select x-ref="province" name="province"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                            @error('province')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kota --}}
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

                        {{-- Kode Pos --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Kode Pos</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $employee->postal_code) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                                focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                            @error('postal_code')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Alamat Lengkap</label>
                            <textarea name="address" rows="4"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                                focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300">{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-600/30">
                    Update
                </button>
            </div>
        </form>
    </section>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    {{-- FilePond --}}
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

    <script>
        FilePond.registerPlugin(FilePondPluginImagePreview);

        const existingImage = @json($profileImage ?? null);

        FilePond.create(document.querySelector('#profile_image'), {
            storeAsFile: true,
            instantUpload: false,
            allowMultiple: false,
            credits: false,
            stylePanelLayout: 'compact',
            imagePreviewHeight: 220,
            styleButtonRemoveItemPosition: 'right',
            labelIdle: 'Drag & Drop foto di sini atau <span class="filepond--label-action">Browse</span>',

            server: {
                load: (source, load, error, progress, abort) => {
                    fetch(source, {
                            credentials: 'same-origin'
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.blob();
                        })
                        .then(blob => load(blob))
                        .catch(err => error(err.message));

                    return {
                        abort
                    };
                }
            },

            files: existingImage ? [{
                source: existingImage,
                options: {
                    type: 'local'
                }
            }] : []
        });
    </script>

    <script>
        function employeeForm() {
            return {
                tsCountry: null,
                tsProvince: null,
                tsCity: null,
                countryCode: null,

                initialProvince: @json(old('province', $employee->province)),
                initialCity: @json(old('city', $employee->city)),

                init() {
                    if (this.$refs.country.tomselect) return;

                    this.tsCountry = new TomSelect(this.$refs.country, {
                        create: false,
                        allowEmptyOption: true,
                        placeholder: 'Select Country',
                    });

                    this.tsProvince = new TomSelect(this.$refs.province, {
                        create: false,
                        allowEmptyOption: true,
                        placeholder: 'Select Province',
                    });

                    this.tsCity = new TomSelect(this.$refs.city, {
                        create: false,
                        allowEmptyOption: true,
                        placeholder: 'Select City',
                    });

                    this.countryCode = this.getSelectedCountryCode();

                    this.tsCountry.on('change', async () => {
                        this.countryCode = this.getSelectedCountryCode();
                        this.initialProvince = null;
                        this.initialCity = null;
                        await this.loadProvinces(false);
                        this.resetCities(false);
                    });

                    this.tsProvince.on('change', async () => {
                        await this.loadCities(false);
                    });

                    if (this.countryCode) {
                        this.loadProvinces(true);
                    }
                },

                getSelectedCountryCode() {
                    const el = this.$refs.country;
                    const opt = el.options[el.selectedIndex];
                    return opt ? opt.dataset.code : null;
                },

                resetProvinces(loading = false) {
                    this.tsProvince.clear(true);
                    this.tsProvince.clearOptions();
                    this.tsProvince.addOption({
                        value: '',
                        text: loading ? 'Loading...' : '-- Select Province --'
                    });
                    this.tsProvince.setValue('', true);
                    this.tsProvince.refreshOptions(false);
                },

                resetCities(loading = false) {
                    this.tsCity.clear(true);
                    this.tsCity.clearOptions();
                    this.tsCity.addOption({
                        value: '',
                        text: loading ? 'Loading...' : '-- Select City --'
                    });
                    this.tsCity.setValue('', true);
                    this.tsCity.refreshOptions(false);
                },

                async loadProvinces(trySelectInitial = false) {
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

                    this.tsProvince.clearOptions();
                    this.tsProvince.addOption({
                        value: '',
                        text: '-- Select Province --'
                    });

                    data.forEach(item => {
                        this.tsProvince.addOption({
                            value: item.name,
                            text: item.name,
                            adminCode1: item.adminCode1
                        });
                    });

                    this.tsProvince.refreshOptions(false);

                    if (trySelectInitial && this.initialProvince) {
                        this.tsProvince.setValue(this.initialProvince, true);
                        await this.loadCities(true);
                    } else {
                        this.tsProvince.setValue('', true);
                    }
                },

                async loadCities(trySelectInitial = false) {
                    this.resetCities(true);

                    const selectedProvince = this.tsProvince.getValue();
                    if (!this.countryCode || !selectedProvince) {
                        this.resetCities(false);
                        return;
                    }

                    const provinceOption = this.tsProvince.options[selectedProvince];
                    const adminCode1 = provinceOption?.adminCode1;

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

                    this.tsCity.clearOptions();
                    this.tsCity.addOption({
                        value: '',
                        text: '-- Select City --'
                    });

                    data.forEach(item => {
                        this.tsCity.addOption({
                            value: item.name,
                            text: item.name
                        });
                    });

                    this.tsCity.refreshOptions(false);

                    if (trySelectInitial && this.initialCity) {
                        this.tsCity.setValue(this.initialCity, true);
                    } else {
                        this.tsCity.setValue('', true);
                    }
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