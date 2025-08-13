<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Activitylog\Models\Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Event;

use App\Models\Primary\Player;
use App\Models\Primary\Space;
use App\Models\Primary\Group;
use App\Models\Primary\Item;
use App\Models\Primary\Transaction;
use App\Models\Primary\Inventory;

use App\Models\Company\Supplier;
use App\Models\Company\Warehouse;
use App\Models\Company\Customer;
use App\Models\Company\Purchase;
use App\Models\Company\Store;
use App\Models\Company\Product;
use App\Models\Company\Inventory\InventoryTransfer;
use App\Models\Warehouse\Outbound;
// use App\Models\Company\Inventory\Inventory;

use App\Models\Primary\Person;
use App\Models\Space\Company;

use App\Models\Store\StorePos;
use App\Models\Store\StoreInventory;

use App\Models\Company\PurchaseInvoice;
use App\Models\Company\Sale\Sale;
use App\Models\Company\Sale\SaleInvoice;

use App\Models\Company\Finance\Expense;
use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\AccountType;
use App\Models\Company\Finance\JournalEntry;
use App\Models\Company\Finance\JournalEntryDetail;

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
            'PRD' => Product::class,

            'SPACE' => Space::class,
            'PLAY' => Player::class,
            'GRP' => Group::class,
            'ACC' => Account::class,
            'ACCT' => AccountType::class,
            'JE' => JournalEntry::class,
            // 'IV' => \App\Models\Primary\Inventory::class,
            'ITM' => Item::class,
            'TX' => Transaction::class,
        ]);



        // logs

        $excludedModels = [
            Activity::class,
            // \Laravel\Sanctum\PersonalAccessToken::class, // kalau pakai Sanctum
        ];


        // Listen semua created
        Event::listen('eloquent.created: *', function ($event, $models) use ($excludedModels) {
            $model = $models[0];
            if (!in_array(get_class($model), $excludedModels)) {
                $this->logActivity('created', $model, [], $model->getAttributes());
            }
        });

        // Listen semua updated
        Event::listen('eloquent.updated: *', function ($event, $models) use ($excludedModels) {
            $model = $models[0];
            if (!in_array(get_class($model), $excludedModels)) {
                $changes = $model->getChanges();

                // Ambil hanya nilai lama dari field yang berubah
                $old = collect($changes)
                    ->mapWithKeys(fn($value, $field) => [$field => $model->getOriginal($field)])
                    ->toArray();

                $this->logActivity('updated', $model, $old, $changes);
            }
        });

        // Listen semua deleted
        Event::listen('eloquent.deleted: *', function ($event, $models) use ($excludedModels) {
            $model = $models[0];
            if (!in_array(get_class($model), $excludedModels)) {
                $this->logActivity('deleted', $model, $model->getOriginal(), []);
            }
        });
	}


    private function logActivity($event, $model, $old = [], $new = [])
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->withProperties([
                'old' => $old,
                'attributes' => $new,
                'ip_address' => Request::ip(),
                'user_agent' => '',
                'url' => request()->getRequestUri(),
            ])
            ->log(class_basename($model) . " {$event}");
    }


    private function shouldLog($model, $excludedModels)
    {
        if (app()->runningInConsole() || !auth()->check()) {
            return false;
        }

        foreach ($excludedModels as $excluded) {
            if ($model instanceof $excluded) {
                return false;
            }
        }

        return true;
    }
}
