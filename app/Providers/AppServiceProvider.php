<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Relations\Relation;

use App\Models\Company\Supplier;
use App\Models\Company\Warehouse;
use App\Models\Company\Customer;
use App\Models\Company\Purchase;
use App\Models\Company\Sale;
use App\Models\Company\Store;
use App\Models\Company\Inventory\InventoryTransfer;
use App\Models\Warehouse\Outbound;
use App\Models\Company\Inventory\Inventory;

use App\Models\Space\Person;
use App\Models\Space\Company;

use App\Models\Store\StorePos;
use App\Models\Store\StoreInventory;

use App\Models\Company\PurchaseInvoice;
use App\Models\Company\SaleInvoice;

use App\Models\Company\Finance\Expense;

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
    public function boot()
	{
        URL::forceScheme('http');

		if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // morph
        Relation::morphMap([
            'SUP' => Supplier::class,
            'WH' => Warehouse::class,
            'CUST' => Customer::class,
            'PO' => Purchase::class,
            'SO' => Sale::class,
            'ST' => Store::class,
            'ITF' => InventoryTransfer::class,
            'OUTB' => Outbound::class,
            'POS' => StorePos::class,
            'PERS' => Person::class,
            'COMP' => Company::class,
            'POI' => PurchaseInvoice::class,
            'SOI' => SaleInvoice::class,
            'EXP' => Expense::class,
            'IVT' => Inventory::class,
            'SIVT' => StoreInventory::class,
        ]);
	}
}
