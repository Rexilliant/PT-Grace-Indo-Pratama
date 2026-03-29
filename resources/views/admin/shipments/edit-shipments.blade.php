@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-permintaan-pengiriman', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
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

        $status = $shipment->status ?? 'Menunggu';
        $statusLower = strtolower($status);

        $statusDate = null;
        $statusUser = '-';

        if ($statusLower === 'ditolak') {
            $statusDate = $shipment->rejected_at;
            $statusUser = optional($shipment->rejectedBy)->name ?? '-';
        } elseif (in_array($statusLower, ['disetujui', 'dikirim', 'selesai'])) {
            $statusDate = $shipment->approved_at;
            $statusUser = optional($shipment->approvedBy)->name ?? '-';
        }

        $statusOptions = match ($status) {
            'Menunggu' => ['Menunggu', 'Disetujui', 'Ditolak'],
            'Disetujui' => ['Disetujui', 'Dikirim'],
            'Dikirim' => ['Dikirim', 'Selesai'],
            'Ditolak' => ['Ditolak'],
            'Selesai' => ['Selesai'],
            default => [$status],
        };

        $isStatusLocked = in_array($status, ['Ditolak', 'Selesai']) || !auth()->user()->can('update-shipments');
    @endphp

    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span>Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span>Permintaan Pengiriman</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="font-bold text-blue-600">Detail Pengiriman</span>
        </div>
    </section>

    <div x-data="shipmentEditForm({
        status: @js($status),
        successMessage: @js(session('success')),
        errorMessage: @js(session('error')),
    })" x-init="init()">
        <form action="{{ route('update-shipment', ['id' => $shipment->id]) }}" method="POST"
            enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- INFORMASI UTAMA --}}
            <section class="{{ $sectionClass }}">
                <div class="mb-4 text-sm font-bold text-gray-800">
                    ID Pengajuan: {{ $shipment->shipment_code }}
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                    <div>
                        <label class="{{ $labelClass }}">Tanggal Pengajuan</label>
                        <input type="text" value="{{ $shipment->created_at?->format('Y-m-d') }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Tanggal Permintaan Pengiriman</label>
                        <input type="text" value="{{ $shipment->shipment_request_at?->format('Y-m-d') }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Penanggung Jawab</label>
                        <input type="text" value="{{ $shipment->personResponsible->name ?? '-' }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Jenis Pengiriman</label>
                        <input type="text" value="{{ $shipment->shipment_type }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Gudang / Tujuan</label>
                        <input type="text" value="{{ $shipment->warehouse->name ?? ($shipment->province ?? '-') }}"
                            readonly class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Armada Pengiriman</label>
                        <input type="text" value="{{ $shipment->shipping_fleet }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Nama Penerima</label>
                        <input type="text" value="{{ $shipment->receivedBy->name ?? '-' }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Kontak Penerima</label>
                        <input type="text" value="{{ $shipment->contact }}" readonly class="{{ $readonlyClass }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">Alamat Lengkap</label>
                        <textarea rows="3" readonly class="{{ $readonlyClass }}">{{ $shipment->address }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">Catatan</label>
                        <textarea rows="3" readonly class="{{ $readonlyClass }}">{{ $shipment->notes }}</textarea>
                    </div>
                </div>
            </section>

            {{-- ITEM PENGIRIMAN --}}
            <section class="{{ $sectionClass }}">
                <div class="mb-4 text-xs font-bold text-gray-800">Daftar Item Pengiriman</div>

                <div class="space-y-4">
                    @foreach ($shipment->shipmentItems as $item)
                        <div class="rounded-lg border border-gray-300 p-4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                <div>
                                    <label class="{{ $labelClass }}">Produk</label>
                                    <input type="text"
                                        value="{{ ($item->productStock->productVariant->sku ?? '-') . ' - ' . ($item->productStock->productVariant->name ?? '-') }}"
                                        readonly class="{{ $readonlyClass }}">
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Gudang Stok</label>
                                    <input type="text"
                                        value="{{ $item->productStock->warehouse->name ?? ($item->productStock->province ?? '-') }}"
                                        readonly class="{{ $readonlyClass }}">
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Stok Saat Ini</label>
                                    <input type="text" value="{{ $item->productStock->stock }}" readonly
                                        class="{{ $readonlyClass }}">
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Jumlah</label>
                                    <input type="text" value="{{ $item->quantity }}" readonly
                                        class="{{ $readonlyClass }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- STATUS --}}
            <section class="{{ $sectionClass }}">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
                    <div>
                        <label class="{{ $labelClass }}">Tanggal Ubah Status</label>
                        <input type="text" readonly value="{{ $statusDate ? $statusDate->format('d-m-Y') : '-' }}"
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Penanggung Jawab Status</label>
                        <input type="text" readonly value="{{ $statusUser }}" class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Status Permintaan</label>
                        <select name="status" x-model="statusPermintaan" class="{{ $inputNormalClass }}"
                            @if ($isStatusLocked) disabled @endif>
                            @foreach ($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                            @endforeach
                        </select>

                        @if ($isStatusLocked)
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                    </div>
                </div>
            </section>

            {{-- ALASAN --}}
            <section class="{{ $sectionClass }}" x-show="showReason" x-cloak>
                <label class="{{ $labelClass }}">Alasan</label>
                <textarea name="reason" rows="3" class="{{ $shipment->reason ? $readonlyClass : $inputNormalClass }}"
                    @if ($shipment->reason) readonly @endif>{{ $shipment->reason }}</textarea>
            </section>

            {{-- INVOICE + TANGGAL PENGIRIMAN --}}
            <section class="{{ $sectionClass }}" x-show="showInvoiceSection" x-cloak>
                <div class="mb-5 overflow-hidden rounded-lg border border-gray-400 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-800">
                            <thead class="bg-[#5aba6f]/70 text-gray-900">
                                <tr>
                                    <th class="px-6 py-4 text-left font-extrabold">File</th>
                                    <th class="px-6 py-4 text-left font-extrabold">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-500 bg-gray-200">
                                @forelse ($invoices as $invoice)
                                    <tr class="hover:bg-gray-300">
                                        <td class="px-6 py-4 font-semibold">
                                            <a target="_blank" href="{{ $invoice->getUrl() }}">
                                                {{ $invoice->file_name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-start gap-6 font-semibold">
                                                @if ($status !== 'Selesai')
                                                    <button type="button" class="text-[#EC0000] hover:underline"
                                                        @click="confirmDelete('{{ route('media.delete', ['mediaId' => $invoice->id]) }}')">
                                                        Hapus
                                                    </button>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada invoice
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Tanggal Pengiriman</label>
                    <input type="date" name="shipment_at" value="{{ $shipment->shipment_at?->format('Y-m-d') }}"
                        @if ($shipment->shipment_at) readonly disabled @endif
                        class="@error('shipment_at') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">

                    @if ($shipment->shipment_at)
                        <input type="hidden" name="shipment_at"
                            value="{{ $shipment->shipment_at?->format('Y-m-d') }}">
                    @endif

                    @error('shipment_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($status !== 'Selesai')
                    <div class="mt-5">
                        <label class="mb-3 block text-sm font-bold text-gray-800">Invoice Pembelian Barang</label>

                        <input x-ref="invoices" type="file" name="invoices[]" multiple
                            accept="image/png,image/jpeg,application/pdf">

                        <p class="mt-2 text-xs text-gray-600">
                            Format: PNG/JPG/JPEG/PDF • Maks 3MB per file • Bisa upload multiple.
                        </p>

                        @error('invoices')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        @error('invoices.*')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </section>

            @can('update-shipments')
                @if ($status !== 'Selesai')
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="{{ $actionBtnClass }} bg-[#2D2ACD] hover:bg-blue-800">
                            Simpan
                        </button>
                    </div>
                @endif
            @endcan
        </form>

        <form x-ref="deleteMediaForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

    <script>
        function shipmentEditForm(config) {
            return {
                statusPermintaan: config.status || 'Menunggu',
                successMessage: config.successMessage || '',
                errorMessage: config.errorMessage || '',
                pond: null,

                get showInvoiceSection() {
                    const status = (this.statusPermintaan || '').toLowerCase();
                    return status === 'dikirim' || status === 'selesai';
                },

                get showReason() {
                    return (this.statusPermintaan || '').toLowerCase() === 'ditolak';
                },

                init() {
                    this.$nextTick(() => this.initFilePond());

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
                },

                confirmDelete(url) {
                    Swal.fire({
                        title: 'Yakin hapus file ini?',
                        text: 'File invoice akan dihapus permanen.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$refs.deleteMediaForm.action = url;
                            this.$refs.deleteMediaForm.submit();
                        }
                    });
                },

                initFilePond() {
                    const input = this.$refs.invoices;

                    if (!input || typeof FilePond === 'undefined' || this.pond) {
                        return;
                    }

                    FilePond.registerPlugin(
                        FilePondPluginFileValidateType,
                        FilePondPluginFileValidateSize,
                        FilePondPluginImagePreview
                    );

                    this.pond = FilePond.create(input, {
                        storeAsFile: true,
                        instantUpload: false,
                        stylePanelLayout: 'compact',
                        allowMultiple: true,
                        maxFiles: 10,
                        credits: false,
                        acceptedFileTypes: ['image/png', 'image/jpeg', 'application/pdf'],
                        maxFileSize: '3MB',
                        labelIdle: 'Drag & Drop file atau <span class="filepond--label-action">Browse</span>',
                        labelFileTypeNotAllowed: 'Format file tidak didukung',
                        fileValidateTypeLabelExpectedTypes: 'Hanya PNG/JPG/JPEG/PDF',
                        labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                        labelMaxFileSize: 'Maksimum 3MB'
                    });
                }
            }
        }
    </script>
@endsection
