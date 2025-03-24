<?php

return [
    // Company Settings
    'company' => [

        // Finance
        'comp.account_receivables' => env('COMP_ACCOUNT_RECEIVABLES', 4),
        'comp.account_inventories' => env('COMP_ACCOUNT_INVENTORIES', 5),
        'comp.account_downpayment_supplier' => env('COMP_ACCOUNT_DOWN_PAYMENT_SUPPLIER', 7),
        'comp.account_vat_input' => env('COMP_ACCOUNT_VAT_INPUT', 8),
        'comp.account_payables' => env('COMP_ACCOUNT_PAYABLES', 22),
        'comp.account_unearned_revenue' => env('COMP_ACCOUNT_UNEARNED_REVENUE', 24),
        'comp.account_vat_output' => env('COMP_ACCOUNT_VAT_OUTPUT', 26),
        'comp.account_common_stock' => env('COMP_ACCOUNT_COMMON_STOCK', 31),
        'comp.account_retained_earnings' => env('COMP_ACCOUNT_RETAINED_EARNINGS', 32),
        'comp.account_revenue' => env('COMP_ACCOUNT_REVENUE', 33),
        'comp.account_discount_sales' => env('COMP_ACCOUNT_DISCOUNT_SALES', 34),
        'comp.account_return_sales' => env('COMP_ACCOUNT_RETURN_SALES', 35),
        'comp.account_cogs' => env('COMP_ACCOUNT_COGS', 36),
        'comp.account_discount_purchases' => env('COMP_ACCOUNT_DISCOUNT_PURCHASES', 37),
        'comp.account_return_purchases' => env('COMP_ACCOUNT_RETURN_PURCHASES', 38),
        'comp.account_shipping_freight' => env('COMP_ACCOUNT_SHIPPING_FREIGHT', 39),
        'comp.account_cost_imports' => env('COMP_ACCOUNT_COST_IMPORTS', 40),
        'comp.account_cost_productions' => env('COMP_ACCOUNT_COST_PRODUCTIONS', 41),
        'comp.account_logistics_distribution' => env('COMP_ACCOUNT_LOGISTICS_DISTRIBUTION', 60),
        'comp.account_salary' => env('COMP_ACCOUNT_SALARY', 43),
        'comp.account_inventory_adjustments' => env('COMP_ACCOUNT_INVENTORY_ADJUSTMENTS', 59),

        'comp.payment_methods' => env('COMP_PAYMENT_METHODS', '{"CASH":1, "Rekening Bank X":2, "Rekening Bank Y":3}'),


        // Store
        'store.payment_methods' => env('STORE_PAYMENT_METHODS', '{"CASH":1}'),
    ],
];