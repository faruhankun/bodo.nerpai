@php
    $sidebar_access = [
        'players' => auth()->user()->can('players sidebar', 'web'),
        'users' => auth()->user()->can('users sidebar', 'web'),
        'persons' => auth()->user()->can('persons sidebar', 'web'),
        'companies' => auth()->user()->can('companies sidebar', 'web'),
        'roles' => auth()->user()->can('roles sidebar', 'web'),
        'permissions' => auth()->user()->can('permissions sidebar', 'web'),
    ];


    $space_role = session('space_role') ?? null;


    // Navbar
    $navbar_right = [
        // 'navbar.player-switcher',
        'navbar.space-switcher',
    ];



    $settings = [
        'supplies' => get_variable('space.setting.supplies') ?? ($space_role == 'admin' ? true : ($space_role == 'owner' ? true : false)),

        'accounting' => get_variable('space.setting.accounting') ?? ($space_role == 'admin' ? true : ($space_role == 'owner' ? true : false)),
    ];

    $sidebar = [
        'Dashboard' => [
            'icon' => 'icon-sidebar',
            'route' => "dashboard_space",
            'text' => 'Lobby',
        ],
        'Space' => [
            'dropdown_id' => 'spaces',
            'dropdown_text' => 'Lahan',
            'dropdown_items' => [
                'spaces' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "spaces.index",
                    'text' => 'Spaces',
                ],
                'spacesr' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "spacesr",
                    'text' => 'Lahan (beta)',
                ]
            ]
        ],

        'Supplies' => [
            'dropdown_id' => 'supplies',
            'dropdown_text' => 'Persediaan',
            'dropdown_items' => [
                'items' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "items.index",
                    'text' => 'Daftar Barang',
                ],
                'supplies' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "supplies.index",
                    'text' => 'Daftar Akun Persediaan',
                ],
                'journal_supplies' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "journal_supplies.index",
                    'text' => 'Mutasi Stok',
                ],
                'stockflow' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "supplies.summary",
                    'text' => 'Rangkuman Mutasi Stok',
                    'route_params' => [
                        'summary_type' => 'stockflow',
                    ],
                ],
                'balance_stock' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "supplies.report",
                    'text' => 'Rangkuman Stok (beta)',
                    'route_params' => [
                        'summary_type' => 'balance_stock',
                    ],
                ],
            ]
        ],

        'Accounting' => [
            'dropdown_id' => 'accounting',
            'dropdown_text' => 'Akunting',
            'dropdown_items' => [
                'accountsp' => [
                    'auth' => $settings['accounting'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "accountsp.index",
                    'text' => 'Akun',
                ],
                'journal_accounts' => [
                    'auth' => $settings['accounting'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "journal_accounts.index",
                    'text' => 'Jurnal Umum',
                ],
                'balance_sheet' => [
                    'auth' => $settings['accounting'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "accountsp.summary",
                    'text' => 'Neraca',
                    'route_params' => [
                        'summary_type' => 'balance_sheet',
                    ],
                ],
                'profit_loss' => [
                    'auth' => $settings['accounting'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "accountsp.summary",
                    'text' => 'Laba Rugi',
                    'route_params' => [
                        'summary_type' => 'profit_loss',
                    ],
                ],
                'cashflow' => [
                    'auth' => $settings['accounting'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "accountsp.summary",
                    'text' => 'Arus Kas',
                    'route_params' => [
                        'summary_type' => 'cashflow',
                    ],
                ],
                'accounts' => [
                    'auth' => $settings['accounting'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "accounts",
                    'text' => 'Akun (beta)',
                ],
            ]
        ],

        'Transaction' => [
            'dropdown_id' => 'transactions',
            'dropdown_text' => 'Transaksi',
            'dropdown_items' => [
                'trade' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "trades.index",
                    'text' => 'Trades',
                ],
            ]
        ],

        'Players' => [
            'dropdown_id' => 'players',
            'dropdown_text' => 'Pemain',
            'dropdown_items' => [
                'contacts' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "contacts.index",
                    'text' => 'Contacts',
                ],
                'space_players' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "space_players.index",
                    'text' => 'Space Players',
                ],
            ]
        ],

        'Summary' => [
            'dropdown_id' => 'summary',
            'dropdown_text' => 'Summary',
            'dropdown_items' => [
                'summaries' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "summaries.index",
                    'text' => 'Summary Reports',
                ],
            ]
        ],

        'Access' => [
            'dropdown_id' => 'access',
            'dropdown_text' => 'Access',
            'dropdown_items' => [
                'roles' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "roles.index",
                    'text' => 'Roles',
                ],

                'skills' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "skills.index",
                    'text' => 'Skills',
                ],

                'variables' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "variables.index",
                    'text' => 'Variables',
                ],
            ]
        ],
        
        'Exit' => [
            'icon' => 'icon-arrow-right',
            'route' => "spaces.exit",
            'text' => 'Exit Space',
        ],
    ];
@endphp

@extends('layouts.base', [
    'navbar_left' => [
        'navbar-nerpai-name',
    ],
    'navbar_right' => $navbar_right,
    'navbar_dropdown_user' => [
        'navbar-user-profile',
        'navbar-user-logout',
    ],
    'sidebar' => $sidebar
])

@section('main-content')
    {{ $slot }}
    @include('layouts.footer')
@endsection