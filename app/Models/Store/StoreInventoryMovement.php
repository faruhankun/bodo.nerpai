<?php

namespace App\Models\Store;

use App\Models\Company\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Company\Finance\CogsEntry;

use App\Services\Company\Finance\JournalEntryService;

class StoreInventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'store_inventory_movements';
    public $timestamps = true;

    protected $fillable = [
        'store_id',
        'store_product_id',
        'store_location_id',
        'source_type',
        'source_id',
        'quantity',
        'cost_per_unit',
        'notes',
    ];


    // relations 

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function store_product(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class);
    }

    public function store_location(): BelongsTo
    {
        return $this->belongsTo(StoreLocation::class);
    }

    public function source(): BelongsTo
    {
        return $this->morphTo();
    }



    // functions
    public function postingCost()
    {
        $cogs_entry = CogsEntry::create([
            'product_id' => $this->store_product->product_id,
            'source_type' => $this->source_type,
            'source_id' => $this->source_id,
            'quantity' => $this->quantity,
            'cost_per_unit' => $this->cost_per_unit,
            'total_cost' => $this->cost_per_unit * $this->quantity,
            'date' => $this->created_at,
        ]);

        $journalService = app(JournalEntryService::class);
        $journalService->addJournalEntry([
            'source_type' => $this->source_type,
            'source_id' => $this->source_id,
            'date' => $this->created_at,
            'type' => 'COGS',
            'description' => 'Cost of Goods Sold',
            'total' => $cogs_entry->total_cost,
        ], [
            [
                'account_id' => get_company_setting('comp.account_cogs'),                 // beban pokok pendapatan
                'debit' => $cogs_entry->total_cost,
            ],
            [
                'account_id' => get_company_setting('comp.account_inventories'),                  // inventory
                'credit' => $cogs_entry->total_cost,
            ]
        ]);
    }

    public function postMovementtoStoreInventory()
    {
        $store_location_id = $this->store_location_id;
        $source_type = $this->source_type;

        $query = StoreInventory::where('store_id', $this->store_id)
                        ->where('store_product_id', $this->store_product_id)
                        ->get();
        if($store_location_id){
            $query->where('store_location_id', $store_location_id);
        } else {
            $query->whereNull('store_location_id');
        } 

        $store_inventory = $query->first();

        if($source_type == 'POS'){
            $store_inventory->decrement('quantity', $this->quantity);
        }

        $store_inventory->save();
    }
}
