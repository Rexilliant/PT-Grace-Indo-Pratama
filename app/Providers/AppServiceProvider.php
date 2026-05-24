<?php

namespace App\Providers;

use App\Models\Procurement;
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

            if (auth()->check()) {

                $warehouseId = auth()->user()->employee?->warehouse_id;

                if ($warehouseId) {

                    $totalProcurementMenunggu = Procurement::where('status', 'Menunggu')
                        ->where('warehouse_id', $warehouseId)
                        ->count();
                }
            }

            $view->with('totalProcurementMenunggu', $totalProcurementMenunggu);
        });
    }
}
