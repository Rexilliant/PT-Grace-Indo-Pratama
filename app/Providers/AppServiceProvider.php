<?php

namespace App\Providers;

use App\Models\Procurement;
use App\Models\Shipment;
use App\Models\ShipmentReceipt;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require_once app_path('Helpers/log_helper.php');

        $totalProcurementMenunggu = 0;
        View::composer('*', function ($view) {
            $totalProcurementMenunggu = 0;
            $totalNotifPermintaanPengirimanProduk = 0;
            $totalNotifPenerimaanPengirimanProduk = 0;

            if (auth()->check()) {
                $warehouseId = auth()->user()->employee?->warehouse_id;
                if ($warehouseId) {
                    $totalProcurementMenunggu = Procurement::where('status', 'Menunggu')->where('warehouse_id', $warehouseId)->count();
                    $totalNotifPermintaanPengirimanProduk = Shipment::whereIn('status', ['Menunggu', 'Disetujui'])
                        ->whereHas('shipmentItems.productStock', function ($query) use ($warehouseId) {
                            $query->where('warehouse_id', $warehouseId);
                        })
                        ->count();
                    $totalNotifPenerimaanPengirimanProduk = ShipmentReceipt::where('status', 'diterima')
                        ->whereHas('shipment.shipmentItems.productStock', function ($query) use ($warehouseId) {
                            $query->where('warehouse_id', $warehouseId);
                        })
                        ->count();
                } else {
                    $totalProcurementMenunggu = Procurement::where('status', 'Menunggu')->count();
                    $totalNotifPermintaanPengirimanProduk = Shipment::whereIn('status', ['Menunggu', 'Disetujui'])->count();
                    $totalNotifPenerimaanPengirimanProduk = ShipmentReceipt::where('status', 'diterima')->count();
                }
            }
            $view->with([
                'totalProcurementMenunggu' => $totalProcurementMenunggu,
                'totalNotifPermintaanPengirimanProduk' => $totalNotifPermintaanPengirimanProduk,
                'totalNotifPenerimaanPengirimanProduk' => $totalNotifPenerimaanPengirimanProduk,
            ]);
        });
    }
}
