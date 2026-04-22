@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-penerimaan-pengiriman-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')


@section('content')
    @php
        $sectionClass = 'rounded-xl border border-gray-300 bg-gray-200/80 p-5 shadow';
        $labelClass = 'mb-2.5 block text-xs font-bold text-gray-800';
        $readonlyClass =
            'w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0';
        $inputBaseClass =
            'w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500';
        $inputNormalClass = $inputBaseClass . ' border border-gray-400 bg-white';
        $inputErrorClass = $inputBaseClass . ' border border-red-500 bg-red-50';
        $actionBtnClass = 'inline-flex items-center justify-center rounded-lg px-10 py-3 text-sm font-bold text-white';
    @endphp

    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Penerimaan Pengiriman</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="font-bold text-blue-600">Tambah Shipment Receipt</span>
        </div>
    </section>

    <form action="{{ route('store-shipment-receipt') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
        x-data="shipmentReceiptForm({
            shipments: @js(
    $shipments->map(function ($shipment) {
        return [
            'id' => $shipment->id,
            'shipment_code' => $shipment->shipment_code,
            'shipment_type' => $shipment->shipment_type,
            'shipping_fleet' => $shipment->shipping_fleet,
            'contact' => $shipment->contact,
            'address' => $shipment->address,
            'notes' => $shipment->notes,
            'warehouse_name' => $shipment->warehouse?->name,
            'received_by_name' => $shipment->receivedBy?->name,
        ];
    }),
),
            selectedShipmentId: @js(old('shipment_id', '')),
            status: @js(old('status', '')),
            rejectReason: @js(old('reject_reason', '')),
            items: @js(old('items', [])),
            validationErrors: @js($errors->toArray()),
            successMessage: @js(session('success')),
            errorMessage: @js(session('error')),
        })" x-init="init()">
        @csrf

        <section class="{{ $sectionClass }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <label class="{{ $labelClass }}">Tanggal Receipt</label>
                    <input type="date" value="{{ now()->format('Y-m-d') }}" readonly class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}">Pilih Shipment</label>
                    <select name="shipment_id" id="shipment_id"
                        class="@error('shipment_id') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                        <option value="">-- Pilih Shipment --</option>
                        @foreach ($shipments as $shipment)
                            <option value="{{ $shipment->id }}" @selected(old('shipment_id') == $shipment->id)>
                                {{ $shipment->shipment_code ?? $shipment->id }} -
                                {{ $shipment->shipment_type ?? '-' }} -
                                {{ $shipment->warehouse->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('shipment_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Status</label>
                    <select name="status" x-model="status"
                        class="@error('status') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                        <option value="diterima" @selected(old('status') == 'diterima')>Diterima</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Tanggal Diterima</label>
                    <input type="datetime-local" name="received_at"
                        value="{{ old('received_at', now()->format('Y-m-d\TH:i')) }}"
                        class="@error('received_at') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                    @error('received_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="status === 'rejected'" x-transition class="md:col-span-2">
                    <label class="{{ $labelClass }}">Alasan Penolakan</label>
                    <textarea name="reject_reason" rows="3" placeholder="Masukkan alasan penolakan" x-model="rejectReason"
                        :required="status === 'rejected'"
                        class="@error('reject_reason') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">{{ old('reject_reason') }}</textarea>
                    @error('reject_reason')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Kode Shipment</label>
                    <input type="text" :value="selectedShipmentData?.shipment_code ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Jenis Shipment</label>
                    <input type="text" :value="selectedShipmentData?.shipment_type ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Gudang Tujuan</label>
                    <input type="text" :value="selectedShipmentData?.warehouse_name ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Armada Pengiriman</label>
                    <input type="text" :value="selectedShipmentData?.shipping_fleet ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Kontak</label>
                    <input type="text" :value="selectedShipmentData?.contact ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Penerima Shipment</label>
                    <input type="text" :value="selectedShipmentData?.received_by_name ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div class="md:col-span-2" x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Alamat Shipment</label>
                    <textarea rows="3" readonly class="{{ $readonlyClass }}" x-text="selectedShipmentData?.address ?? '-'"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Catatan Receipt</label>
                    <textarea name="notes" rows="3" placeholder="Opsional"
                        class="@error('notes') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="{{ $sectionClass }}">
            <div class="mb-4 flex items-center justify-between">
                <div class="text-xs font-bold text-gray-800">Daftar Item Shipment Receipt</div>
            </div>

            @error('items')
                <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <div x-show="!selectedShipmentId" class="mb-4 text-xs text-gray-600">
                Pilih shipment terlebih dahulu agar daftar item muncul.
            </div>

            <div x-show="loading" class="mb-4 text-xs text-blue-600">
                Memuat item shipment...
            </div>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="rounded-lg border border-gray-300 p-4">
                        <input type="hidden" :name="`items[${index}][shipment_item_id]`" :value="item.shipment_item_id">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">SKU</label>
                                <input type="text" :value="item.sku ?? '-'" readonly class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Produk</label>
                                <input type="text" :value="item.product_name ?? '-'" readonly
                                    class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Qty Dikirim</label>
                                <input type="text" :value="item.quantity ?? 0" readonly class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Qty Diterima</label>
                                <input type="number" min="0" :max="item.quantity ?? null"
                                    :name="`items[${index}][qty_received]`" x-model="item.qty_received"
                                    placeholder="Masukkan qty diterima"
                                    :class="fieldError(index, 'qty_received') ? 'border-red-500 bg-red-50' :
                                        'border-gray-400 bg-white'"
                                    class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">

                                <template x-if="fieldError(index, 'qty_received')">
                                    <p class="mt-1 text-xs text-red-600" x-text="fieldError(index, 'qty_received')"></p>
                                </template>
                            </div>
                        </div>

                        <div x-show="Number(item.qty_received) > Number(item.quantity ?? 0)"
                            class="mt-2 text-[11px] text-gray-600">
                            Qty diterima tidak boleh melebihi qty dikirim.
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <section class="{{ $sectionClass }}">
            <div class="mb-3 text-xs font-bold text-gray-800">
                Bukti Kerusakan (Opsional)
            </div>

            <div class="relative w-full overflow-hidden rounded-xl border-2 border-dashed transition-all duration-200 ease-in-out"
                :class="dragging ? 'border-[#2D2ACD] bg-blue-50' :
                    'border-gray-300 bg-white hover:bg-gray-50 hover:border-gray-400'"
                @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                @drop.prevent="handleDrop($event)">

                <input type="file" name="damage_proofs[]" multiple accept="image/png,image/jpeg,application/pdf"
                    id="file-upload" class="absolute inset-0 z-50 h-full w-full cursor-pointer opacity-0"
                    @change="handleFileSelect($event)">

                <div class="flex flex-col items-center justify-center p-8 text-center">
                    <svg class="mb-3 h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                    <p class="mb-1 text-sm text-gray-600">
                        <span class="font-bold text-[#2D2ACD]">Klik untuk unggah</span> atau seret dan lepas file ke sini
                    </p>
                    <p class="text-xs text-gray-500">Mendukung PNG, JPG, JPEG, PDF (Maks. 3MB)</p>
                </div>
            </div>

            <template x-if="files.length > 0">
                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    <template x-for="(file, index) in files" :key="index">
                        <div
                            class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all hover:shadow-md">

                            <div class="relative flex h-24 w-full items-center justify-center bg-gray-100">
                                <template x-if="file.isImage">
                                    <img :src="file.preview" alt="preview" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!file.isImage">
                                    <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </template>
                            </div>

                            <div class="p-2">
                                <p class="truncate text-[10px] font-bold text-gray-700" x-text="file.name"></p>
                                <p class="text-[9px] text-gray-500" x-text="file.sizeFormatted"></p>
                            </div>

                            <button type="button" @click.prevent="removeFile(index)"
                                class="absolute top-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white opacity-0 shadow transition-opacity duration-200 group-hover:opacity-100 hover:bg-red-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </template>

            @error('damage_proofs')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @error('damage_proofs.*')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </section>

        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('admin.gudang-permintaan-pengiriman') }}"
                class="{{ $actionBtnClass }} bg-red-600 hover:bg-red-700">
                Batal
            </a>

            <button type="submit" class="{{ $actionBtnClass }} bg-[#2D2ACD] hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>
@endsection

@section('addJs')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function shipmentReceiptForm(config) {
            return {
                shipments: Array.isArray(config.shipments) ? config.shipments : [],
                selectedShipmentId: config.selectedShipmentId || '',
                status: config.status || '',
                rejectReason: config.rejectReason || '',
                items: Array.isArray(config.items) ? config.items : [],
                validationErrors: config.validationErrors || {},
                successMessage: config.successMessage || '',
                errorMessage: config.errorMessage || '',
                loading: false,

                init() {
                    if (this.successMessage) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: this.successMessage,
                            confirmButtonColor: '#2D2ACD'
                        });
                    }

                    if (this.errorMessage) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: this.errorMessage,
                            confirmButtonColor: '#dc2626'
                        });
                    }

                    this.$nextTick(() => {
                        this.initSelect2();
                    });

                    if (this.selectedShipmentId && this.items.length === 0) {
                        this.fetchShipmentItems(this.selectedShipmentId);
                    }
                },

                initSelect2() {
                    const self = this;
                    const el = window.jQuery('#shipment_id');

                    if (!el.length) return;

                    el.select2({
                        placeholder: '-- Pilih Shipment --',
                        width: '100%'
                    });

                    if (this.selectedShipmentId) {
                        el.val(String(this.selectedShipmentId)).trigger('change');
                    }

                    el.on('change', function() {
                        const shipmentId = window.jQuery(this).val();
                        self.selectedShipmentId = shipmentId;
                        self.handleShipmentChange(shipmentId);
                    });
                },

                get selectedShipmentData() {
                    return this.shipments.find(shipment =>
                        String(shipment.id) === String(this.selectedShipmentId)
                    ) || null;
                },

                async handleShipmentChange(shipmentId) {
                    this.items = [];

                    if (!shipmentId) {
                        return;
                    }

                    await this.fetchShipmentItems(shipmentId);
                },

                async fetchShipmentItems(shipmentId) {
                    this.loading = true;

                    try {
                        const response = await fetch(`/admin/shipments/${shipmentId}/items`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        this.items = (Array.isArray(result) ? result : []).map(item => ({
                            shipment_item_id: item.id,
                            product_stock_id: item.product_stock_id ?? '',
                            sku: item.product_stock?.product_variant?.sku ?? '-',
                            product_name: item.product_stock?.product_variant?.name ?? '-',
                            stock: item.product_stock?.stock ?? 0,
                            quantity: item.quantity ?? 0,
                            qty_received: item.quantity ?? 0,
                            notes: item.notes ?? ''
                        }));
                    } catch (error) {
                        console.error(error);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengambil item shipment',
                            confirmButtonColor: '#dc2626'
                        });
                    } finally {
                        this.loading = false;
                    }
                },

                fieldError(index, field) {
                    const key = `items.${index}.${field}`;
                    return this.validationErrors[key]?.[0] || '';
                },

                // --- STATE UNTUK UPLOAD FILE ---
                dragging: false,
                files: [], // Menyimpan data preview

                // --- METHOD UNTUK UPLOAD FILE ---
                handleFileSelect(event) {
                    this.processFiles(event.target.files);
                },

                handleDrop(event) {
                    this.dragging = false;
                    this.processFiles(event.dataTransfer.files);
                },

                processFiles(newFiles) {
                    if (!newFiles || newFiles.length === 0) return;

                    // Gunakan DataTransfer untuk merekonstruksi isi <input type="file">
                    const dt = new DataTransfer();

                    // Masukkan file lama yang sudah ada di memori
                    this.files.forEach(f => dt.items.add(f.rawFile));

                    // Masukkan file baru
                    Array.from(newFiles).forEach(file => {
                        // Validasi simpel batas 3MB (Opsional, agar user tidak kaget saat ditolak server)
                        if (file.size > 3 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'File Terlalu Besar',
                                text: `${file.name} melebihi ukuran maksimal 3MB.`,
                                confirmButtonColor: '#2D2ACD'
                            });
                            return;
                        }

                        dt.items.add(file);

                        const isImage = file.type.startsWith('image/');
                        this.files.push({
                            rawFile: file,
                            name: file.name,
                            sizeFormatted: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                            isImage: isImage,
                            preview: isImage ? URL.createObjectURL(file) : null
                        });
                    });

                    // Update value asli dari input DOM agar terkirim ke Laravel Controller
                    document.getElementById('file-upload').files = dt.files;
                },

                removeFile(index) {
                    // Bersihkan memori browser dari URL blob
                    if (this.files[index].preview) {
                        URL.revokeObjectURL(this.files[index].preview);
                    }

                    // Hapus dari state Alpine
                    this.files.splice(index, 1);

                    // Rekonstruksi ulang isi <input type="file">
                    const dt = new DataTransfer();
                    this.files.forEach(f => dt.items.add(f.rawFile));
                    document.getElementById('file-upload').files = dt.files;
                },
            }
        }
    </script>
@endsection
