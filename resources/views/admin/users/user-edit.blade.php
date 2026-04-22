@extends('admin.layout.master')
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-user', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

    <style>
        .readonly-style {
            background-color: rgb(241 245 249) !important;
            cursor: not-allowed !important;
        }
    </style>
@endsection

@section('content')
    @php
        $canEditUser = auth()->user()->can('edit akun');
        $canReadUser = auth()->user()->can('baca akun');

        $isReadOnly = !$canEditUser;

        $inputClass = $isReadOnly
            ? 'w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-900 readonly-style'
            : 'w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900
               focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:border-blue-300';

        $userRole = $user->roles->pluck('name')->first();
        $defaultTab = old('role') ?? ($userRole ?? 'custom');
    @endphp

    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">User</span>
            <span class="mx-1 text-gray-400">/</span>
            <span class="text-blue-600">Edit User</span>
        </div>
    </section>

    <section x-data="userForm({ isReadOnly: @js($isReadOnly) })" x-init="init()" class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
        <h1 class="mb-5 text-2xl font-semibold tracking-tight text-slate-900">Edit User</h1>

        <form action="{{ route('update-user', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                {{-- Employee --}}
                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Karyawan (Opsional)</label>
                    <select x-ref="employee" name="employee_id" @if ($isReadOnly) disabled @endif
                        class="{{ $inputClass }}">
                        <option value="" data-name="" data-email="">-- Tidak dikaitkan ke karyawan --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                data-email="{{ $employee->email }}"
                                {{ old('employee_id', $user->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} {{ $employee->nip ? '(' . $employee->nip . ')' : '' }}
                            </option>
                        @endforeach
                    </select>

                    @if ($isReadOnly)
                        <input type="hidden" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}">
                    @endif

                    @error('employee_id')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Nama</label>
                    <input type="text" name="name" x-model="formName" :readonly="isEmployeeSelected || isReadOnly"
                        class="{{ $inputClass }}" />
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Email</label>
                    <input type="email" name="email" x-model="formEmail" :readonly="isEmployeeSelected || isReadOnly"
                        class="{{ $inputClass }}" />
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">
                        Password <span class="text-xs text-slate-500">(Kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password" @if ($isReadOnly) readonly @endif
                        class="{{ $inputClass }}" />
                    @error('password')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">
                        Konfirmasi Password <span class="text-xs text-slate-500">(Kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password_confirmation" @if ($isReadOnly) readonly @endif
                        class="{{ $inputClass }}" />
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <div class="flex flex-wrap gap-2 border-b border-slate-200 p-3">
                    @foreach ($roles as $role)
                        <button type="button"
                            @if (!$isReadOnly) @click="setTab('{{ $role->name }}')" @endif
                            :class="activeTab === '{{ $role->name }}' ?
                                'bg-blue-600 text-white' :
                                'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ $isReadOnly ? 'pointer-events-none opacity-80' : '' }}">
                            {{ $role->name }}
                        </button>
                    @endforeach

                    <button type="button" @if (!$isReadOnly) @click="setTab('custom')" @endif
                        :class="activeTab === 'custom' ?
                            'bg-blue-600 text-white' :
                            'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ $isReadOnly ? 'pointer-events-none opacity-80' : '' }}">
                        Custom
                    </button>
                </div>

                <div class="p-5">
                    <input type="hidden" name="role" :value="selectedRole">

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
                                <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($role->permissions as $perm)
                                        <label
                                            class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2">
                                            <input type="checkbox" checked disabled class="h-4 w-4">
                                            <span class="text-sm text-slate-800">{{ $perm->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div x-show="activeTab==='custom'" x-cloak>
                        <div class="mb-4 flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Custom Permissions</div>
                                <div class="text-xs text-slate-500">
                                    Permission ini akan dikirim sebagai <code>permissions[]</code>.
                                </div>
                            </div>

                            @if (!$isReadOnly)
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
                            @endif
                        </div>

                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($permissions as $permission)
                                <label
                                    class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="h-4 w-4" x-model="customPermissions"
                                        @if ($isReadOnly) disabled @endif
                                        {{ in_array($permission->name, old('permissions', $user->permissions->pluck('name')->toArray())) ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-800">{{ $permission->name }}</span>
                                </label>

                                @if ($isReadOnly && in_array($permission->name, old('permissions', $user->permissions->pluck('name')->toArray())))
                                    <input type="hidden" name="permissions[]" value="{{ $permission->name }}">
                                @endif
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

            @if (!$isReadOnly)
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-md bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-600/30">
                        Simpan Perubahan
                    </button>
                </div>
            @endif
        </form>
    </section>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        function userForm(config = {}) {
            return {
                tsEmployee: null,
                isReadOnly: !!config.isReadOnly,

                activeTab: @json($defaultTab),
                selectedRole: @json(old('role') ?? $userRole),
                customPermissions: @json(old('permissions', $user->permissions->pluck('name')->toArray())),

                formName: @json(old('name', $user->name)),
                formEmail: @json(old('email', $user->email)),
                isEmployeeSelected: false,

                init() {
                    if (!this.$refs.employee?.tomselect) {
                        this.tsEmployee = new TomSelect(this.$refs.employee, {
                            create: false,
                            allowEmptyOption: true,
                            placeholder: 'Pilih Karyawan (Opsional)',
                        });

                        this.tsEmployee.on('change', (val) => {
                            if (this.isReadOnly) return;
                            this.onEmployeeChange(val);
                        });
                    }

                    const initialVal = this.$refs.employee.value;
                    this.onEmployeeChange(initialVal);

                    if (this.isReadOnly && this.tsEmployee) {
                        this.tsEmployee.disable();
                    }
                },

                onEmployeeChange(val) {
                    if (!val) {
                        this.isEmployeeSelected = false;
                        return;
                    }

                    this.isEmployeeSelected = true;

                    const opt = this.$refs.employee.querySelector(`option[value="${val}"]`);
                    const empName = opt?.dataset?.name || '';
                    const empEmail = opt?.dataset?.email || '';

                    this.formName = empName;
                    this.formEmail = empEmail;
                },

                setTab(tabName) {
                    if (this.isReadOnly) return;

                    this.activeTab = tabName;

                    if (tabName === 'custom') {
                        this.selectedRole = null;
                        return;
                    }

                    this.selectedRole = tabName;
                },

                checkAll() {
                    if (this.isReadOnly) return;
                    this.customPermissions = @json($permissions->pluck('name')->values());
                },

                uncheckAll() {
                    if (this.isReadOnly) return;
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
