<?php

return [
    // Company Settings
    'space' => [
        // Space Settings
        'space.setting.purchase' => env('SPACE_SETTING_PURCHASE', 0),
        'space.setting.sales' => env('SPACE_SETTING_SALES', 0),
        'space.setting.inventory' => env('SPACE_SETTING_INVENTORY', 0),
        'space.setting.accounting' => env('SPACE_SETTING_ACCOUNTING', 0),
        'space.setting.pos' => env('SPACE_SETTING_POS', 0),
        'space.setting.qc' => env('SPACE_SETTING_QC', 0),

        // Finance
        'space.account_receivables' => env('COMP_ACCOUNT_RECEIVABLES', 4),
        'space.account_inventories' => env('COMP_ACCOUNT_INVENTORIES', 5),
        'space.account_downpayment_supplier' => env('COMP_ACCOUNT_DOWN_PAYMENT_SUPPLIER', 7),
        'space.account_vat_input' => env('COMP_ACCOUNT_VAT_INPUT', 8),
        'space.account_payables' => env('COMP_ACCOUNT_PAYABLES', 22),
        'space.account_unearned_revenue' => env('COMP_ACCOUNT_UNEARNED_REVENUE', 24),
        'space.account_vat_output' => env('COMP_ACCOUNT_VAT_OUTPUT', 26),
        'space.account_common_stock' => env('COMP_ACCOUNT_COMMON_STOCK', 31),
        'space.account_retained_earnings' => env('COMP_ACCOUNT_RETAINED_EARNINGS', 32),
        'space.account_revenue' => env('COMP_ACCOUNT_REVENUE', 33),
        'space.account_discount_sales' => env('COMP_ACCOUNT_DISCOUNT_SALES', 34),
        'space.account_return_sales' => env('COMP_ACCOUNT_RETURN_SALES', 35),
        'space.account_cogs' => env('COMP_ACCOUNT_COGS', 36),
        'space.account_discount_purchases' => env('COMP_ACCOUNT_DISCOUNT_PURCHASES', 37),
        'space.account_return_purchases' => env('COMP_ACCOUNT_RETURN_PURCHASES', 38),
        'space.account_shipping_freight' => env('COMP_ACCOUNT_SHIPPING_FREIGHT', 39),
        'space.account_cost_imports' => env('COMP_ACCOUNT_COST_IMPORTS', 40),
        'space.account_cost_productions' => env('COMP_ACCOUNT_COST_PRODUCTIONS', 41),
        'space.account_logistics_distribution' => env('COMP_ACCOUNT_LOGISTICS_DISTRIBUTION', 60),
        'space.account_salary' => env('COMP_ACCOUNT_SALARY', 43),
        'space.account_inventory_adjustments' => env('COMP_ACCOUNT_INVENTORY_ADJUSTMENTS', 59),

        'space.payment_methods' => env('COMP_PAYMENT_METHODS', '{"CASH":1, "Rekening Bank X":2, "Rekening Bank Y":3}'),


        // Store
        'store.payment_methods' => env('STORE_PAYMENT_METHODS', '{"CASH":1}'),
    ],
];