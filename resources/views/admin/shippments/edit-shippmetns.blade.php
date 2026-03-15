@extends('admin.layout.master')
{{-- sidebar active (sesuaikan menu kamu) --}}
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
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span>Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span>Permintaan Pengiriman</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Detail Pengiriman</span>
        </div>
    </section>

    <div x-data="shipmentForm()" x-init="init()">
        <form action="{{ route('update-shippment', ['id' => $shippment->id]) }}" method="POST"
            enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                <p>Id Pengajuan : {{ $shippment->shippment_code }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Pengajuan</label>
                        <input type="text" value="{{ $shippment->created_at?->format('Y-m-d') }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Permintaan Pengiriman</label>
                        <input type="text" value="{{ $shippment->shippment_request_at?->format('Y-m-d') }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>
                        <input type="text" value="{{ $shippment->personResponsible->name }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Jenis Pengiriman</label>
                        <input type="text" value="{{ $shippment->shippment_type }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Provinsi Tujuan</label>
                        <input type="text" value="{{ $shippment->province }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Armada Pengiriman</label>
                        <input type="text" value="{{ $shippment->shipping_fleet }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Penerima</label>
                        <input type="text" value="{{ $shippment->receivedBy->name ?? '-' }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Kontak</label>
                        <input type="text" value="{{ $shippment->contact }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Alamat</label>
                        <textarea rows="3" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">{{ $shippment->address }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Catatan</label>
                        <textarea rows="3" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">{{ $shippment->notes }}</textarea>
                    </div>
                </div>
            </section>

            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                <div class="text-xs font-bold text-gray-800 mb-4">
                    Daftar Item Pengiriman
                </div>

                <div class="space-y-4">
                    @foreach ($shippment->shippmentItems as $item)
                        <div class="rounded-lg border border-gray-300 p-4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                <div>
                                    <label class="mb-2 block text-xs font-bold text-gray-800">Produk</label>
                                    <input type="text"
                                        value="{{ $item->productStock->productVariant->sku }} - {{ $item->productStock->productVariant->name }}"
                                        readonly
                                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-bold text-gray-800">Provinsi Stok</label>
                                    <input type="text" value="{{ $item->productStock->province }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-bold text-gray-800">Stock Saat Ini</label>
                                    <input type="text" value="{{ $item->productStock->stock }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-bold text-gray-800">Jumlah</label>
                                    <input type="text" value="{{ $item->quantity }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Ubah Status</label>

                        @php
                            $status = strtolower($shippment->status);
                            $tanggalStatus = null;

                            if ($status === 'ditolak') {
                                $tanggalStatus = $shippment->rejected_at;
                            }

                            if (in_array($status, ['disetujui', 'selesai'])) {
                                $tanggalStatus = $shippment->approved_at;
                            }
                        @endphp

                        <input type="text" readonly value="{{ $tanggalStatus ? $tanggalStatus->format('d-m-Y') : '-' }}"
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>

                        <input type="text" readonly
                            value="{{ strtolower($shippment->status) === 'ditolak'
                                ? optional($shippment->rejectedBy)->name
                                : (in_array(strtolower($shippment->status), ['disetujui', 'selesai'])
                                    ? optional($shippment->approvedBy)->name
                                    : '-') }}"
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Status Permintaan</label>

                        <select name="status" x-model="statusPermintaan"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold"
                            @if (in_array(strtolower($shippment->status), ['ditolak', 'selesai'])) disabled @endif>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Ditolak">Ditolak</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="Selesai">Selesai</option>
                        </select>

                        @if (in_array(strtolower($shippment->status), ['ditolak', 'selesai']))
                            <input type="hidden" name="status" value="{{ $shippment->status }}">
                        @endif
                    </div>
                </div>
            </section>

            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl" x-show="showReason" x-cloak>
                <label class="block text-xs font-bold text-gray-800 mb-2.5">Alasan</label>

                <textarea name="reason" rows="3"
                    class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold"
                    @if ($shippment->reason) readonly @endif>{{ old('reason', $shippment->reason) }}</textarea>
            </section>

            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl" x-show="showInvoiceSection"
                x-cloak>
                <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm mb-5">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-800">
                            <thead class="bg-[#5aba6f]/70 text-gray-900">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-extrabold text-left">File</th>
                                    <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="bg-gray-200 divide-y divide-gray-500">
                                @forelse ($invoices as $invoice)
                                    <tr class="hover:bg-gray-300">
                                        <td class="px-6 py-4 font-semibold">
                                            <a target="_blank" href="{{ $invoice->getUrl() }}">
                                                {{ $invoice->file_name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-start gap-6 font-semibold">
                                                @if (strtolower($shippment->status) !== 'selesai')
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
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Pengiriman</label>
                    <input type="date" name="shippment_at"
                        value="{{ old('shippment_at', $shippment->shippment_at?->format('Y-m-d')) }}"
                        @if ($shippment->shippment_at) readonly disabled @endif
                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 @error('shippment_at') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">

                    @if ($shippment->shippment_at)
                        <input type="hidden" name="shippment_at"
                            value="{{ $shippment->shippment_at?->format('Y-m-d') }}">
                    @endif

                    @error('shippment_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if (strtolower($shippment->status) !== 'selesai')
                    <div class="mt-5">
                        <label class="block text-sm font-bold mb-3 text-gray-800">Invoice Pembelian Barang</label>

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

            <div class="flex justify-end pt-2">
                @if (strtolower($shippment->status) !== 'selesai')
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                        Simpan
                    </button>
                @endif
            </div>
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
        function shipmentForm() {
            return {
                statusPermintaan: @js(old('status', $shippment->status)),
                pond: null,

                get showInvoiceSection() {
                    const status = (this.statusPermintaan || '').toLowerCase();
                    return status === 'selesai';
                },

                get showReason() {
                    const status = (this.statusPermintaan || '').toLowerCase();
                    return status === 'ditolak';
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

                init() {
                    this.$nextTick(() => {
                        this.initFilePond();
                    });

                    @if (session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: @js(session('success')),
                            confirmButtonColor: '#2D2ACD'
                        });
                    @endif

                    @if (session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: @js(session('error')),
                            confirmButtonColor: '#dc2626'
                        });
                    @endif
                },

                initFilePond() {
                    const input = this.$refs.invoices;

                    if (!input || typeof FilePond === 'undefined') return;
                    if (this.pond) return;

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
