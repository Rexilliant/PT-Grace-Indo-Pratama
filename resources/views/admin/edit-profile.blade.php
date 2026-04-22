@extends('admin.layout.master')

{{-- sidebar active --}}
@section('menu-profile', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        $user = auth()->user();
        $employee = $user->employee ?? null;

        $canEditProfile = auth()->user()->can('edit profile') ?? true;
        $isReadOnly = !$canEditProfile;

        $inputClass = $isReadOnly
            ? 'w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed'
            : 'w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500';

        $photoUrl =
            method_exists($user, 'getFirstMediaUrl') && $user->getFirstMediaUrl('profile_photo')
                ? $user->getFirstMediaUrl('profile_photo')
                : asset('images/default-user.png');

        $fullName = old('name', $user->name ?? '');
        $nameParts = preg_split('/\s+/', trim($fullName), 2);
        $firstName = old('first_name', $nameParts[0] ?? '');
        $lastName = old('last_name', $nameParts[1] ?? '');

        $dob = old('dob', optional($employee?->birthday)->format('Y-m-d') ?? '');
        $email = old('email', $user->email ?? '');
        $phone = old('phone', $employee?->phone ?? '');
        $country = old('country', $employee?->country ?? 'Indonesia');
        $province = old('province', $employee?->province ?? '');
        $city = old('city', $employee?->city ?? '');
        $postalCode = old('postal_code', $employee?->postal_code ?? '');
    @endphp

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
        @method('PUT')

        {{-- Profile Picture --}}
        <section class="rounded-lg border border-gray-300 bg-white p-5 shadow">
            <div class="mb-4 text-sm font-semibold text-gray-800">Profile Picture</div>

            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                <div class="shrink-0">
                    <div class="h-30 w-30 flex items-center justify-center overflow-hidden rounded-full bg-black">
                        <img src="{{ $photoUrl }}" alt="Profile Picture" class="h-full w-full object-cover">
                    </div>
                </div>

                <div class="flex-1">
                    <div class="mb-2 text-sm font-semibold text-gray-700">Masukan Foto Baru</div>

                    @if ($isReadOnly)
                        <input type="text" readonly value="Mode baca - foto tidak dapat diubah"
                            class="{{ $inputClass }}">
                    @else
                        <div class="flex w-full max-w-xl">
                            <label
                                class="inline-flex cursor-pointer items-center justify-center rounded-l-md bg-slate-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                                Input
                                <input id="photo" name="photo" type="file" class="hidden" accept="image/*">
                            </label>
                            <input id="photo_name" type="text" readonly
                                class="w-full rounded-r-md border border-gray-400 bg-white px-3 py-2.5 text-sm focus:border-gray-500 focus:ring-0"
                                placeholder="">
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Personal Information --}}
        <section class="rounded-lg border border-gray-300 bg-white p-5 shadow">
            <div class="mb-4 text-sm font-semibold text-gray-800">Personal Information</div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Nama Depan</label>
                    <input name="first_name" value="{{ $firstName }}" @if ($isReadOnly) readonly @endif
                        class="{{ $inputClass }}" />
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Tanggal Lahir</label>
                    <input type="date" name="dob" value="{{ $dob }}"
                        @if ($isReadOnly) readonly @endif class="{{ $inputClass }}" />
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Email Address</label>
                    <input type="email" name="email" value="{{ $email }}"
                        @if ($isReadOnly) readonly @endif class="{{ $inputClass }}" />
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Nomor Telepon</label>
                    <input name="phone" value="{{ $phone }}" @if ($isReadOnly) readonly @endif
                        class="{{ $inputClass }}" />
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Password</label>
                    <input type="password" name="password" value="" @if ($isReadOnly) readonly @endif
                        class="{{ $inputClass }}" />
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password" value="" @if ($isReadOnly) readonly @endif
                        class="{{ $inputClass }}" />
                </div>

                
            </div>
        </section>

        {{-- Alamat --}}
        <section class="rounded-lg border border-gray-300 bg-white p-5 shadow">
            <div class="mb-4 text-sm font-semibold text-gray-800">Alamat</div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                @php
                    $countries = \PragmaRX\Countries\Package\Countries::all()
                        ->sortBy('name.common')
                        ->pluck('name.common')
                        ->values();
                @endphp

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Negara</label>
                    <select name="country" @if ($isReadOnly) disabled @endif class="{{ $inputClass }}">
                        <option value="" disabled>Pilih negara</option>
                        @foreach ($countries as $c)
                            <option value="{{ $c }}" @selected($country === $c)>
                                {{ $c }}
                            </option>
                        @endforeach
                    </select>

                    @if ($isReadOnly)
                        <input type="hidden" name="country" value="{{ $country }}">
                    @endif
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Provinsi</label>
                    <input name="province" placeholder="Contoh: Sumatera Utara" value="{{ $province }}"
                        @if ($isReadOnly) readonly @endif class="{{ $inputClass }}" />
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Kab / Kota</label>
                    <input name="city" placeholder="Contoh: Medan" value="{{ $city }}"
                        @if ($isReadOnly) readonly @endif class="{{ $inputClass }}" />
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Kode Pos</label>
                    <input name="postal_code" placeholder="Contoh: 20111" value="{{ $postalCode }}"
                        @if ($isReadOnly) readonly @endif class="{{ $inputClass }}" />
                </div>
            </div>
        </section>

        {{-- Berkas Pribadi --}}
        <section class="rounded-lg border border-gray-300 bg-white p-5 shadow">
            <div class="mb-4 text-sm font-semibold text-gray-800">Berkas Pribadi</div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Upload KTP</label>

                    @if ($isReadOnly)
                        <input type="text" readonly value="Mode baca - berkas tidak dapat diubah"
                            class="{{ $inputClass }}">
                    @else
                        <div class="flex w-full">
                            <label
                                class="inline-flex cursor-pointer items-center justify-center rounded-l-md bg-slate-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                                Input
                                <input id="ktp_1" name="ktp_1" type="file" class="hidden">
                            </label>
                            <input id="ktp_1_name" type="text" readonly
                                class="w-full rounded-r-md border border-gray-400 bg-white px-3 py-2.5 text-sm focus:border-gray-500 focus:ring-0"
                                placeholder="">
                        </div>
                    @endif
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Upload KK</label>

                    @if ($isReadOnly)
                        <input type="text" readonly value="Mode baca - berkas tidak dapat diubah"
                            class="{{ $inputClass }}">
                    @else
                        <div class="flex w-full">
                            <label
                                class="inline-flex cursor-pointer items-center justify-center rounded-l-md bg-slate-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                                Input
                                <input id="ktp_2" name="ktp_2" type="file" class="hidden">
                            </label>
                            <input id="ktp_2_name" type="text" readonly
                                class="w-full rounded-r-md border border-gray-400 bg-white px-3 py-2.5 text-sm focus:border-gray-500 focus:ring-0"
                                placeholder="">
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- actions --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="button" onclick="openCancelModal()"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </button>

            @if (!$isReadOnly)
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                    Simpan
                </button>
            @endif
        </div>

        <div id="cancelModal"
            class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">

            <div class="mx-4 w-full max-w-md animate-scale-in rounded-xl bg-white shadow-xl">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        Batalkan Perubahan?
                    </h3>
                </div>

                <div class="px-6 py-4 text-sm leading-relaxed text-gray-700">
                    Perubahan yang sudah kamu lakukan <span class="font-semibold">belum disimpan</span>.
                    Jika dibatalkan, semua perubahan akan hilang.
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" onclick="closeCancelModal()"
                        class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold hover:bg-gray-300">
                        Tetap di Halaman
                    </button>

                    <a href="{{ route('admin.profile') }}"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        Ya, Batalkan
                    </a>
                </div>
            </div>
        </div>

    </form>

    @if (!$isReadOnly)
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
    @endif

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

@endsection
