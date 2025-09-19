@php
    $user = auth()->user();

    $space_id = get_space_id(request());
    setPermissionsTeamId($space_id);

    $space_role = session('space_role') ?? null;

    

    // Navbar
    $navbar_right = [
        // 'navbar.player-switcher',
        'navbar.space-switcher',
    ];



    $settings = [
        'supplies' => get_variable('space.setting.supplies') ?? true,

        'accounting' => get_variable('space.setting.accounting') ?? ($space_role == 'admin' ? true : ($space_role == 'owner' ? true : false)),

        'social' => get_variable('space.setting.social') ?? true,
    ];

    $sidebar = [
        'Dashboard' => [
            'icon' => 'icon-sidebar',
            'route' => "dashboard_space",
            'text' => 'Lobby',
        ],

        'Supplies' => [
            'dropdown_id' => 'supplies',
            'dropdown_text' => 'Persediaan',
            'dropdown_items' => [
                'items' => [
                    'auth' => $user->can('space.items.sidebar') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "items.index",
                    'text' => 'Daftar Barang',
                ],
                'supplies' => [
                    'auth' => $user->can('space.supplies.sidebar') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "supplies.index",
                    'text' => 'Daftar Akun Persediaan',
                ],
                'journal_supplies' => [
                    'auth' => $user->can('space.journal_supplies.sidebar') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "journal_supplies.index",
                    'text' => 'Mutasi Stok',
                ],
                'stockflow_items' => [
                    'auth' => $user->can('space.supplies.summary') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "supplies.summary",
                    'text' => 'Rangkuman Barang',
                    'route_params' => [
                        'summary_type' => 'stockflow_items',
                    ],
                ],
                'balance_stock' => [
                    'auth' => $user->can('space.supplies.summary') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "supplies.report",
                    'text' => 'Rangkuman Stok (beta)',
                    'route_params' => [
                        'summary_type' => 'balance_stock',
                    ],
                ],
            ]
        ],




        'Pesanan' => [
            'dropdown_id' => 'orders',
            'dropdown_text' => 'Pesanan',
            'dropdown_items' => [
                'trade' => [
                    'auth' => $user->can('space.trades.po') || $user->can('space.trades.so') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "trades.index",
                    'text' => 'Trades',
                ],

                'items.summary' => [
                    'auth' => $user->can('space.items.summary') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "items.summary",
                    'text' => 'Rangkuman Barang',
                    'route_params' => [
                        'summary_type' => 'itemflow',
                        'start_date' => now()->startOfMonth()->format('Y-m-d'),
                    ],
                ],
            ],
        ],


        'Sosial' => [
            'dropdown_id' => 'social',
            'dropdown_text' => 'Sosial',
            'dropdown_items' => [
                'players' => [
                    'auth' => $user->can('space.players') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "players.index",
                    'text' => 'Kontak',
                ],

                'quote' => [
                    'auth' => $user->can('space.quotes.po') || $user->can('space.quotes.so') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "quotes.index",
                    'text' => 'Penawaran (beta)',
                ],

                'players.summary' => [
                    'auth' => $user->can('space.players.summary') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "players.summary",
                    'text' => 'Rangkuman Kontak (beta)',
                    'route_params' => [
                        'summary_type' => 'tradeflow',
                        'start_date' => now()->startOfMonth()->format('Y-m-d'),
                    ],
                ],
            ],
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

        'Access' => [
            'dropdown_id' => 'access',
            'dropdown_text' => 'Access',
            'dropdown_items' => [
                'teams' => [
                    'auth' => $user->can('space.teams') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "teams.index",
                    'text' => 'Teams',
                ],

                'logs' => [
                    'auth' => $user->can('space.access.logs') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "logs.index",
                    'text' => 'Logs',
                ],

                'roles' => [
                    'auth' => $user->can('space.roles') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "roles.index",
                    'text' => 'Roles',
                ],

                'skills' => [
                    'auth' => $user->can('space.skills') || $space_role == 'owner',
                    'icon' => 'icon-checklist-paper',
                    'route' => "skills.index",
                    'text' => 'Skills',
                ],

                'variables' => [
                    'auth' => $user->can('space.variables') || $space_role == 'owner',
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


    // drop off sidebar
    if(!$settings['supplies']) {
        unset($sidebar['Supplies']);
    }

    if(!$settings['accounting']) {
        unset($sidebar['Accounting']);
    }

    if(!$settings['social']) {
        unset($sidebar['Social']);
    }
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