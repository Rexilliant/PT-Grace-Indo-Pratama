@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-bahan-baku', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Bahan Baku</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Tambah Bahan Baku</span>
        </div>
    </section>

    <form action="{{ route('admin.add-bahan-baku.store') }}" method="POST">
        @csrf

        <div class="flex justify-end mb-4">
            <button type="button" id="addRowBtn"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-6 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                + Tambah Kolom
            </button>
        </div>

        <div id="rawMaterialRows" class="space-y-4">
            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl raw-material-row">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                    {{-- Kode Barang --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Kode Barang</label>
                        <input name="items[0][kode_barang]" type="text" placeholder="Contoh: CA0001"
                            class="w-full rounded-md border border-gray-400 bg-white
                                   px-3 py-2.5 text-sm font-semibold text-gray-900
                                   focus:border-blue-600 focus:ring-0">
                        <span class="code-feedback-msg text-xs mt-1 block font-semibold"></span>
                    </div>

                    {{-- Bahan Baku --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Bahan Baku</label>
                        <input name="items[0][bahan_baku]" type="text" placeholder="Contoh: Kalsium"
                            class="w-full rounded-md border border-gray-400 bg-white
                                   px-3 py-2.5 text-sm font-semibold text-gray-900
                                   focus:border-blue-600 focus:ring-0">
                    </div>

                    {{-- Unit --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Unit</label>
                        <input name="items[0][unit]" type="text" placeholder="Contoh: Kg / Liter / Box"
                            class="w-full rounded-md border border-gray-400 bg-white
                                   px-3 py-2.5 text-sm font-semibold text-gray-900
                                   focus:border-blue-600 focus:ring-0">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Status</label>
                        <select name="items[0][status]"
                            class="w-full rounded-md border border-gray-400 bg-white
                                   px-3 py-2.5 text-sm font-semibold text-gray-900
                                   focus:border-blue-600 focus:ring-0">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                </div>
            </section>
        </div>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-4 pt-6">
            <button type="button" onclick="openCancelModal()"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </button>

            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>

    {{-- MODAL BATAL --}}
    <div id="cancelModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 animate-scale-in">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Batalkan Pemesanan?</h3>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700 leading-relaxed">
                Data yang sudah kamu isi <span class="font-semibold">belum disimpan</span>.
                Kalau dibatalkan, semua perubahan akan hilang.
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" onclick="closeCancelModal()"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 hover:bg-gray-300">
                    Tetap di Halaman
                </button>

                <a href="{{ route('admin.gudang-bahan-baku') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                    Ya, Batalkan
                </a>
            </div>
        </div>
    </div>

    {{-- TEMPLATE BARIS DYNAMIS --}}
    <template id="rawMaterialRowTemplate">
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl raw-material-row">
            <div class="flex justify-end mb-4">
                <button type="button"
                    class="removeRowBtn inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                    Hapus
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                {{-- Kode Barang --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Kode Barang</label>
                    <input name="items[__INDEX__][kode_barang]" type="text" placeholder="Contoh: CA0001"
                        class="w-full rounded-md border border-gray-400 bg-white
                               px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0">
                    <span class="code-feedback-msg text-xs mt-1 block font-semibold"></span>
                </div>

                {{-- Bahan Baku --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Bahan Baku</label>
                    <input name="items[__INDEX__][bahan_baku]" type="text" placeholder="Contoh: Kalsium"
                        class="w-full rounded-md border border-gray-400 bg-white
                               px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0">
                </div>

                {{-- Unit --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Unit</label>
                    <input name="items[__INDEX__][unit]" type="text" placeholder="Contoh: Kg / Liter / Box"
                        class="w-full rounded-md border border-gray-400 bg-white
                               px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Status</label>
                    <select name="items[__INDEX__][status]"
                        class="w-full rounded-md border border-gray-400 bg-white
                               px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

            </div>
        </section>
    </template>

    {{-- MODAL POP-UP KODE SUDAH DIGUNAKAN --}}
    <div id="duplicateCodeModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 border-l-4 border-red-600 p-6 animate-scale-in">
            <div class="flex items-center gap-3 mb-3">
                <div class="rounded-full bg-red-100 p-2 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Kode Barang Sudah Digunakan!</h3>
            </div>

            <p id="duplicateCodeMessage" class="text-sm text-gray-600 mb-5 leading-relaxed">
                Kode barang ini tidak dapat dipakai karena sudah terdaftar.
            </p>

            <div class="flex justify-end">
                <button type="button" onclick="closeDuplicateModal()"
                    class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                    Ganti Kode
                </button>
            </div>
        </div>
    </div>
@endsection

@section('addJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let rowCounter = 1;

            const addRowBtn = document.getElementById('addRowBtn');
            const rawMaterialRows = document.getElementById('rawMaterialRows');
            const rawMaterialRowTemplate = document.getElementById('rawMaterialRowTemplate');

            // Tambah Baris Baru
            if (addRowBtn) {
                addRowBtn.addEventListener('click', function() {
                    const html = rawMaterialRowTemplate.innerHTML.replaceAll('__INDEX__', rowCounter);
                    rowCounter++;

                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    rawMaterialRows.appendChild(wrapper.firstElementChild);
                });
            }

            // Hapus Baris (Event Delegation)
            if (rawMaterialRows) {
                rawMaterialRows.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('removeRowBtn')) {
                        const row = e.target.closest('.raw-material-row');
                        if (row) row.remove();
                    }
                });
            }

            // Real-time Check Kode Barang (Event Delegation)
            document.addEventListener('input', function(e) {
                if (e.target && e.target.name && e.target.name.includes('[kode_barang]')) {
                    handleCodeCheck(e.target);
                }
            });

            function handleCodeCheck(inputKode) {
                const codeValue = inputKode.value.trim();
                const parent = inputKode.parentElement;
                const feedbackEl = parent ? parent.querySelector('.code-feedback-msg') : null;

                // Reset tampilan
                inputKode.classList.remove('border-red-500', 'ring-1', 'ring-red-500', 'border-green-500',
                    'ring-green-500');
                if (feedbackEl) {
                    feedbackEl.textContent = '';
                    feedbackEl.className = 'code-feedback-msg text-xs mt-1 block font-semibold';
                }

                if (!codeValue) return;

                // Gunakan timer debounce
                clearTimeout(inputKode.debounceTimer);

                inputKode.debounceTimer = setTimeout(() => {
                    // 1. Cek Duplikat Lokal (di dalam form antar baris)
                    if (checkLocalDuplicate(inputKode)) {
                        setFieldError(inputKode, feedbackEl,
                            `Kode "${codeValue}" sudah dimasukkan pada baris lain!`);
                        showDuplicateModal(
                            `Kode barang "${codeValue}" sudah kamu masukkan pada baris lain di form ini.`,
                            inputKode);
                        return;
                    }

                    // 2. Cek ke Database via AJAX
                    const checkUrl = "{{ route('admin.raw-materials.check-code') }}?code=" +
                        encodeURIComponent(codeValue);

                    fetch(checkUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Network error status: ' + res.status);
                            return res.json();
                        })
                        .then(data => {
                            if (data.exists) {
                                setFieldError(inputKode, feedbackEl,
                                    `Kode "${codeValue}" sudah terdaftar di database!`);
                                showDuplicateModal(data.message, inputKode);
                            } else {
                                setFieldSuccess(inputKode, feedbackEl, 'Kode barang tersedia ✓');
                            }
                        })
                        .catch(err => {
                            console.error('Error saat mengecek kode barang:', err);
                        });
                }, 350);
            }

            function checkLocalDuplicate(currentInput) {
                const val = currentInput.value.trim().toLowerCase();
                if (!val) return false;

                const inputs = document.querySelectorAll('input[name*="[kode_barang]"]');
                let isDup = false;

                inputs.forEach(inp => {
                    if (inp !== currentInput && inp.value.trim().toLowerCase() === val) {
                        isDup = true;
                    }
                });

                return isDup;
            }

            function setFieldError(input, feedbackEl, msg) {
                input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                if (feedbackEl) {
                    feedbackEl.textContent = msg;
                    feedbackEl.classList.add('text-red-600');
                }
            }

            function setFieldSuccess(input, feedbackEl, msg) {
                input.classList.add('border-green-500', 'ring-1', 'ring-green-500');
                if (feedbackEl) {
                    feedbackEl.textContent = msg;
                    feedbackEl.classList.add('text-green-600');
                }
            }

            function showDuplicateModal(msg, inputElem) {
                const duplicateModal = document.getElementById('duplicateCodeModal');
                const duplicateMessage = document.getElementById('duplicateCodeMessage');

                if (duplicateMessage) duplicateMessage.textContent = msg;
                window.activeInputTarget = inputElem;

                if (duplicateModal) {
                    duplicateModal.classList.remove('hidden');
                    duplicateModal.classList.add('flex');
                }
            }
        });

        // Fungsi Global Modal Batal & Modal Duplikat
        function closeDuplicateModal() {
            const duplicateModal = document.getElementById('duplicateCodeModal');
            if (duplicateModal) {
                duplicateModal.classList.add('hidden');
                duplicateModal.classList.remove('flex');
            }

            if (window.activeInputTarget) {
                window.activeInputTarget.value = '';
                window.activeInputTarget.focus();
                window.activeInputTarget.classList.remove('border-red-500', 'ring-1', 'ring-red-500');

                // Triggers input event agar feedback teks juga bersih
                window.activeInputTarget.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                window.activeInputTarget = null;
            }
        }

        function openCancelModal() {
            const modal = document.getElementById('cancelModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    </script>
@endsection
