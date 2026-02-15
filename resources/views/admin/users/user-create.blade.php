@extends('admin.layout.master')

@section('addCss')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
@endsection

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">User</span>
            <span class="mx-1 text-gray-400">/</span>
            <span class="text-blue-600">Tambah User</span>
        </div>
    </section>

    <section x-data="userForm()" x-init="init()" class="p-5 rounded-md shadow-sm border border-slate-200 bg-white">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 mb-5">Tambah User</h1>

        <form action="{{ route('store-user') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-5">
                {{-- Employee --}}
                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Karyawan (Opsional)</label>
                    <select x-ref="employee" name="employee_id"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                        <option value="" data-name="" data-email="">-- Tidak dikaitkan ke karyawan --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                data-email="{{ $employee->email }}"
                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} {{ $employee->nip ? '(' . $employee->nip . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Name --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Nama</label>
                    <input type="text" name="name" x-model="formName" :readonly="isEmployeeSelected"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
           focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Email</label>
                    <input type="email" name="email" x-model="formEmail" :readonly="isEmployeeSelected"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
           focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Password --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Password</label>
                    <input type="password" name="password"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                    @error('password')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
                               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300" />
                </div>
            </div>

            {{-- Tabs: role-role + custom --}}
            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                {{-- Tab headers (role names + Custom) --}}
                <div class="flex flex-wrap gap-2 border-b border-slate-200 p-3">
                    @foreach ($roles as $role)
                        <button type="button" @click="setTab('{{ $role->name }}')"
                            :class="activeTab === '{{ $role->name }}' ?
                                'bg-blue-600 text-white' :
                                'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition">
                            {{ $role->name }}
                        </button>
                    @endforeach

                    <button type="button" @click="setTab('custom')"
                        :class="activeTab === 'custom' ?
                            'bg-blue-600 text-white' :
                            'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition">
                        Custom
                    </button>
                </div>

                <div class="p-5">
                    {{-- Hidden input untuk role terpilih (role-name) --}}
                    <input type="hidden" name="role" :value="selectedRole">

                    {{-- Panels untuk setiap role: tampil permissions role (disabled) --}}
                    @foreach ($roles as $role)
                        <div x-show="activeTab==='{{ $role->name }}'" x-cloak>
                            <div class="mb-4">
                                <div class="text-sm font-semibold text-slate-900">
                                    Permissions untuk role: <span class="text-blue-600">{{ $role->name }}</span>
                                </div>
                            </div>

                            @if ($role->permissions->count() === 0)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                                    Role ini belum punya permission.
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach ($role->permissions as $perm)
                                        <label
                                            class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 bg-white">
                                            <input type="checkbox" checked disabled class="h-4 w-4">
                                            <span class="text-sm text-slate-800">{{ $perm->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Custom: semua permission bisa dicentang --}}
                    <div x-show="activeTab==='custom'" x-cloak>
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Custom Permissions</div>
                                <div class="text-xs text-slate-500">
                                    Permission ini akan dikirim sebagai <code>permissions[]</code> dan bisa ditambahkan ke
                                    user.
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="button"
                                    class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                    @click="checkAll()">
                                    Centang Semua
                                </button>
                                <button type="button"
                                    class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                    @click="uncheckAll()">
                                    Kosongkan
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach ($permissions as $permission)
                                <label
                                    class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 bg-white">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="h-4 w-4" x-model="customPermissions"
                                        {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-800">{{ $permission->name }}</span>
                                </label>
                            @endforeach

                        </div>

                        @error('permissions')
                            <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        @error('permissions.*')
                            <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-col gap-3 sm:flex-row mt-6">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-600/30">
                    Simpan
                </button>
            </div>
        </form>
    </section>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        function userForm() {
            return {
                tsEmployee: null,

                activeTab: @json(old('role') ?? ($roles->first()->name ?? 'custom')),
                selectedRole: @json(old('role') ?? ($roles->first()->name ?? null)),
                customPermissions: @json(old('permissions', [])),

                // === autofill fields ===
                formName: @json(old('name', '')),
                formEmail: @json(old('email', '')),
                isEmployeeSelected: false,

                init() {
                    // tom-select employee
                    if (!this.$refs.employee?.tomselect) {
                        this.tsEmployee = new TomSelect(this.$refs.employee, {
                            create: false,
                            allowEmptyOption: true,
                            placeholder: 'Pilih Karyawan (Opsional)',
                        });

                        // listen change dari TomSelect
                        this.tsEmployee.on('change', (val) => {
                            this.onEmployeeChange(val);
                        });
                    }

                    // init pertama kali (kalau ada old employee_id)
                    const initialVal = this.$refs.employee.value;
                    this.onEmployeeChange(initialVal);

                    // kalau tab awal custom
                    if (this.activeTab === 'custom') this.selectedRole = null;
                },

                onEmployeeChange(val) {
                    if (!val) {
                        // tidak pilih employee -> manual isi
                        this.isEmployeeSelected = false;
                        // optional: kosongkan otomatis
                        // this.formName = '';
                        // this.formEmail = '';
                        return;
                    }

                    this.isEmployeeSelected = true;

                    // ambil data dari option terpilih
                    const opt = this.$refs.employee.querySelector(`option[value="${val}"]`);
                    const empName = opt?.dataset?.name || '';
                    const empEmail = opt?.dataset?.email || '';

                    // isi otomatis
                    this.formName = empName;
                    this.formEmail = empEmail;
                },

                setTab(tabName) {
                    this.activeTab = tabName;

                    if (tabName === 'custom') {
                        this.selectedRole = null;
                        return;
                    }
                    this.selectedRole = tabName;
                },

                checkAll() {
                    this.customPermissions = @json($permissions->pluck('name')->values());
                },

                uncheckAll() {
                    this.customPermissions = [];
                },
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
