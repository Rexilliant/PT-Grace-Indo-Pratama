@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-penerimaan-pengiriman-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>
        .filepond--root {
            font-family: inherit;
            margin-bottom: 0;
            min-height: 200px;
            transition: all 0.3s ease;
        }

        .filepond--panel-root {
            background-color: #ffffff !important;
            border: 2px dashed #d1d5db !important;
            border-radius: 1rem !important;
            transition: all 0.3s ease;
        }

        .filepond--root:hover .filepond--panel-root {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }

        .filepond--drop-label {
            background-color: transparent !important;
            cursor: pointer;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 1.5rem !important;
            height: 100% !important;
            min-height: 200px;
        }

        .filepond--label-action {
            text-decoration: none;
            cursor: pointer;
            color: #3b82f6;
            font-weight: 700;
        }

        .filepond--root.has-files {
            height: 260px !important;
            min-height: 260px !important;
        }

        .filepond--root.has-files .filepond--panel-root {
            transform: none !important;
            height: 100% !important;
        }

        .filepond--root.has-files .filepond--drop-label {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            min-height: 45px !important;
            height: 45px !important;
            padding: 0 !important;
            border-bottom: 1px dashed #d1d5db;
            background: #f8fafc !important;
            border-radius: 1rem 1rem 0 0 !important;
            z-index: 10;
            opacity: 1 !important;
            transform: none !important;
        }

        .filepond--root.has-files .fp-icon-large,
        .filepond--root.has-files .fp-text-large {
            display: none !important;
        }

        .filepond--root.has-files .fp-text-mini {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .filepond--root.has-files .filepond--list-scroller {
            position: absolute !important;
            top: 45px !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: auto !important;
            transform: none !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            padding: 15px !important;
            margin-top: 0 !important;
        }

        .filepond--root.has-files .filepond--list {
            display: flex !important;
            flex-direction: row !important;
            gap: 15px;
            position: static !important;
            transform: none !important;
            height: 100% !important;
        }

        .filepond--root.has-files .filepond--item {
            position: static !important;
            transform: none !important;
            width: 180px !important;
            height: calc(100% - 10px) !important;
            flex-shrink: 0;
            margin: 0 !important;
        }

        .filepond--list-scroller::-webkit-scrollbar {
            height: 8px;
        }

        .filepond--list-scroller::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }

        .filepond--list-scroller::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 8px;
        }

        .filepond--list-scroller::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        @media (min-width: 50em) {
            .filepond--item {
                width: calc(33.33% - 0.5em);
            }
        }
    </style>
@endsection

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

        $currentStatus = $shipmentReceipt->status ?? 'diterima';

        $canEditReceipt = auth()->user()->can('edit penerimaan pengiriman produk');
        $canReadReceipt = auth()->user()->can('baca penerimaan pengiriman produk');
        $canEditReceiptStatus = auth()->user()->can('edit status penerimaan pengiriman produk');

        // Final status tidak bisa diubah lagi
        $isFinalStatus = in_array($currentStatus, ['disetujui', 'ditolak']);

        // Detail editable hanya saat status diterima + punya akses edit
        $canEditDetail = $currentStatus === 'diterima' && $canEditReceipt && !$isFinalStatus;

        // Status editable hanya saat status diterima + punya akses edit status
        $canEditStatus = $currentStatus === 'diterima' && $canEditReceiptStatus && !$isFinalStatus;

        // Kalau tidak punya edit, atau cuma baca, atau sudah final => lock detail
        $isDetailLocked = !$canEditDetail;

        // Kalau tidak punya edit status atau sudah final => lock status
        $isStatusLocked = !$canEditStatus;

        $showSubmitButton = $canEditDetail || $canEditStatus;
    @endphp

    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Penerimaan Pengiriman</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="font-bold text-blue-600">Edit Shipment Receipt</span>
        </div>
    </section>

    <form action="{{ route('update-shipment-receipt', $shipmentReceipt->id) }}" method="POST" enctype="multipart/form-data"
        class="space-y-5"
        x-data="shipmentReceiptEditForm({
            shipment: @js([
                'id' => $shipmentReceipt->shipment?->id,
                'shipment_code' => $shipmentReceipt->shipment?->shipment_code,
                'shipment_type' => $shipmentReceipt->shipment?->shipment_type,
                'shipping_fleet' => $shipmentReceipt->shipment?->shipping_fleet,
                'contact' => $shipmentReceipt->shipment?->contact,
                'address' => $shipmentReceipt->shipment?->address,
                'notes' => $shipmentReceipt->shipment?->notes,
                'warehouse_name' => $shipmentReceipt->shipment?->warehouse?->name,
                'received_by_name' => $shipmentReceipt->shipment?->received_name,
            ]),
            items: @js(
                $shipmentReceipt->items->map(function ($item) {
                    return [
                        'shipment_receipt_item_id' => $item->id,
                        'shipment_item_id' => $item->shipment_item_id,
                        'sku' => $item->shipmentItem?->productStock?->productVariant?->sku ?? '-',
                        'product_name' => $item->shipmentItem?->productStock?->productVariant?->name ?? '-',
                        'quantity' => $item->shipmentItem?->quantity ?? 0,
                        'qty_received' => $item->qty_received ?? 0,
                        'notes' => $item->notes ?? '',
                    ];
                }),
            ),
            status: @js($shipmentReceipt->status ?? 'diterima'),
            receivedAt: @js(optional($shipmentReceipt->received_at)->format('Y-m-d\TH:i')),
            notes: @js($shipmentReceipt->notes),
            rejectReason: @js($shipmentReceipt->reject_reason ?? ''),
            validationErrors: @js($errors->toArray()),
            successMessage: @js(session('success')),
            errorMessage: @js(session('error')),
            isDetailLocked: @js($isDetailLocked),
            isStatusLocked: @js($isStatusLocked),
            isFinalStatus: @js($isFinalStatus),
        })"
        x-init="init()">
        @csrf
        @method('PUT')

        <input type="hidden" name="shipment_id" value="{{ $shipmentReceipt->shipment_id }}">

        <section class="{{ $sectionClass }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <label class="{{ $labelClass }}">Tanggal Receipt</label>
                    <input type="date" value="{{ optional($shipmentReceipt->created_at)->format('Y-m-d') }}" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}">Kode Shipment</label>
                    <input type="text" :value="shipment?.shipment_code ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}">Status</label>
                    <select name="status" x-model="status" @if ($isStatusLocked) disabled @endif
                        class="@error('status') {{ $inputErrorClass }} @else {{ $isStatusLocked ? $readonlyClass : $inputNormalClass }} @enderror">
                        <option value="diterima">Diterima</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>

                    @if ($isStatusLocked)
                        <input type="hidden" name="status" value="{{ old('status', $shipmentReceipt->status ?? 'diterima') }}">
                    @endif

                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Tanggal Diterima</label>
                    <input type="datetime-local" name="received_at" x-model="receivedAt"
                        @if ($isDetailLocked) readonly @endif
                        class="@error('received_at') {{ $inputErrorClass }} @else {{ $isDetailLocked ? $readonlyClass : $inputNormalClass }} @enderror">
                    @error('received_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="status === 'ditolak'" x-transition class="md:col-span-2">
                    <label class="{{ $labelClass }}">Alasan Penolakan</label>
                    <textarea name="reject_reason" rows="3" x-model="rejectReason" placeholder="Masukkan alasan penolakan"
                        :required="status === 'ditolak'" @if ($isStatusLocked) readonly @endif
                        class="@error('reject_reason') {{ $inputErrorClass }} @else {{ $isStatusLocked ? $readonlyClass : $inputNormalClass }} @enderror"></textarea>
                    @error('reject_reason')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Jenis Shipment</label>
                    <input type="text" :value="shipment?.shipment_type ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Gudang Tujuan</label>
                    <input type="text" :value="shipment?.warehouse_name ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Armada Pengiriman</label>
                    <input type="text" :value="shipment?.shipping_fleet ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Kontak</label>
                    <input type="text" :value="shipment?.contact ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Penerima Shipment</label>
                    <input type="text" :value="shipment?.received_by_name ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div class="md:col-span-2" x-show="shipment">
                    <label class="{{ $labelClass }}">Alamat Shipment</label>
                    <textarea rows="3" readonly class="{{ $readonlyClass }}" x-text="shipment?.address ?? '-'"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Catatan Receipt</label>
                    <textarea name="notes" rows="3" placeholder="Opsional" x-model="notes"
                        @if ($isDetailLocked) readonly @endif
                        class="@error('notes') {{ $inputErrorClass }} @else {{ $isDetailLocked ? $readonlyClass : $inputNormalClass }} @enderror"></textarea>
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

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="item.shipment_receipt_item_id ?? index">
                    <div class="rounded-lg border border-gray-300 p-4">
                        <input type="hidden" :name="`items[${index}][shipment_receipt_item_id]`"
                            :value="item.shipment_receipt_item_id">
                        <input type="hidden" :name="`items[${index}][shipment_item_id]`" :value="item.shipment_item_id">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">SKU</label>
                                <input type="text" :value="item.sku ?? '-'" readonly class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Produk</label>
                                <input type="text" :value="item.product_name ?? '-'" readonly class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Qty Dikirim</label>
                                <input type="text" :value="item.quantity ?? 0" readonly class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Qty Diterima</label>
                                <input type="number" min="0" :max="item.quantity ?? null"
                                    :name="`items[${index}][qty_received]`" x-model="item.qty_received"
                                    placeholder="Masukkan qty diterima" :readonly="isDetailLocked"
                                    :class="isDetailLocked ? '{{ $readonlyClass }}' :
                                        (fieldError(index, 'qty_received') ? 'border-red-500 bg-red-50' : 'border-gray-400 bg-white')"
                                    class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">

                                <template x-if="fieldError(index, 'qty_received') && !isDetailLocked">
                                    <p class="mt-1 text-xs text-red-600" x-text="fieldError(index, 'qty_received')"></p>
                                </template>
                            </div>
                        </div>

                        <div x-show="Number(item.qty_received) > Number(item.quantity ?? 0) && !isDetailLocked"
                            class="mt-2 text-[11px] text-gray-600">
                            Qty diterima tidak boleh melebihi qty dikirim.
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <section class="{{ $sectionClass }}">
            <div class="md:col-span-2">
                <label class="{{ $labelClass }}">Bukti Barang Rusak</label>

                @if (isset($damageProofs) && $damageProofs->count())
                    <div class="mb-4 grid grid-cols-2 gap-4 md:grid-cols-4">
                        @foreach ($damageProofs as $media)
                            <div class="group relative overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm transition hover:shadow-md">
                                <a href="{{ $media->getUrl() }}" target="_blank" class="block overflow-hidden">
                                    <img src="{{ $media->getUrl() }}"
                                        class="h-32 w-full object-cover transition duration-200 group-hover:scale-105">
                                </a>

                                <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-2.5 py-2">
                                    <span class="truncate text-[11px] font-medium text-gray-700" title="{{ $media->file_name }}">
                                        {{ $media->file_name }}
                                    </span>

                                    @if (!$isDetailLocked)
                                        <button type="button"
                                            class="ml-2 inline-flex items-center text-xs font-bold text-red-600 hover:text-red-800 hover:underline"
                                            @click="confirmDelete('{{ route('media.delete', ['mediaId' => $media->id]) }}')">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mb-4 text-xs text-gray-500">Belum ada bukti kerusakan yang diunggah.</p>
                @endif

                @if (!$isDetailLocked)
                    <div class="mt-3">
                        <label class="mb-2 block text-xs font-bold text-gray-700">Tambah Bukti Kerusakan Baru</label>
                        <input type="file" name="damage_proofs[]" id="damageProofsPond" multiple
                            accept="image/png, image/jpeg, image/jpg, application/pdf">

                        @error('damage_proofs')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @error('damage_proofs.*')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>
        </section>

        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('shipment-receipts') }}"
                class="{{ $actionBtnClass }} bg-red-600 hover:bg-red-700">
                Batal
            </a>

            @if ($showSubmitButton)
                <button type="submit" class="{{ $actionBtnClass }} bg-[#2D2ACD] hover:bg-blue-800">
                    Update
                </button>
            @endif
        </div>
    </form>

    <form x-ref="deleteMediaForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

    <script>
        function shipmentReceiptEditForm(config) {
            return {
                shipment: config.shipment || null,
                items: Array.isArray(config.items) ? config.items : [],
                status: config.status || 'diterima',
                receivedAt: config.receivedAt || '',
                notes: config.notes || '',
                rejectReason: config.rejectReason || '',
                isDetailLocked: !!config.isDetailLocked,
                isStatusLocked: !!config.isStatusLocked,
                isFinalStatus: !!config.isFinalStatus,
                validationErrors: config.validationErrors || {},
                successMessage: config.successMessage || '',
                errorMessage: config.errorMessage || '',
                pond: null,

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
                        this.initFilePond();
                    });
                },

                fieldError(index, field) {
                    const key = `items.${index}.${field}`;
                    return this.validationErrors[key]?.[0] || '';
                },

                confirmDelete(url) {
                    Swal.fire({
                        title: 'Yakin hapus file ini?',
                        text: 'File bukti kerusakan akan dihapus permanen.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = this.$refs.deleteMediaForm || document.querySelector('form[x-ref="deleteMediaForm"]');
                            if (form) {
                                form.action = url;
                                form.submit();
                            }
                        }
                    });
                },

                initFilePond() {
                    const input = document.getElementById('damageProofsPond');

                    if (!input || typeof FilePond === 'undefined' || this.pond) {
                        return;
                    }

                    FilePond.registerPlugin(
                        FilePondPluginFileValidateType,
                        FilePondPluginFileValidateSize,
                        FilePondPluginImagePreview
                    );

                    const customIconPlaceholder = `
                        <div class="flex flex-col items-center justify-center w-full">
                            <div class="fp-icon-large p-4 bg-blue-50 rounded-full mb-4 transition-transform duration-300 hover:scale-110">
                                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="fp-text-large text-center">
                                <p class="text-base font-bold text-gray-700"><span class="filepond--label-action">Klik</span> atau Tarik gambar ke sini</p>
                                <p class="text-xs text-gray-500 mt-1 font-medium">PNG, JPG, JPEG, PDF (Maks 3MB/file)</p>
                            </div>

                            <div class="fp-text-mini hidden cursor-pointer hover:underline text-blue-600">
                                <p class="text-sm font-bold m-0 p-0">+ Tambah Gambar Lain</p>
                            </div>
                        </div>
                    `;

                    this.pond = FilePond.create(input, {
                        storeAsFile: true,
                        allowMultiple: true,
                        maxFiles: 5,
                        credits: false,
                        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'],
                        maxFileSize: '3MB',

                        labelIdle: customIconPlaceholder,
                        labelFileTypeNotAllowed: 'Format file tidak didukung',
                        fileValidateTypeLabelExpectedTypes: 'Hanya PNG/JPG/JPEG/PDF',
                        labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                        labelMaxFileSize: 'Maksimum 3MB',

                        onupdatefiles: (files) => {
                            const rootElement = document.getElementById('damageProofsPond')?.closest('.filepond--root');
                            if (rootElement) {
                                if (files.length > 0) {
                                    rootElement.classList.add('has-files');
                                } else {
                                    rootElement.classList.remove('has-files');
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
@endsection