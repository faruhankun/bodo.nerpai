@php
    $sidebar_access = [
        'players' => auth()->user()->can('players sidebar', 'web'),
        'users' => auth()->user()->can('users sidebar', 'web'),
        'persons' => auth()->user()->can('persons sidebar', 'web'),
        'companies' => auth()->user()->can('companies sidebar', 'web'),
        'roles' => auth()->user()->can('roles sidebar', 'web'),
        'permissions' => auth()->user()->can('permissions sidebar', 'web'),
    ];


    // Navbar
    $navbar_right = [
        'navbar.player-switcher',
        'navbar.space-switcher',
    ];



    $settings = [
        'supplies' => get_variable('space.setting.supplies') ?? true,

        'accounting' => get_variable('space.setting.accounting') ?? true,
    ];

    $sidebar = [
        'Dashboard' => [
            'icon' => 'icon-sidebar',
            'route' => "dashboard_space",
            'text' => 'Lobby',
        ],
        'Space' => [
            'dropdown_id' => 'spaces',
            'dropdown_text' => 'Spaces',
            'dropdown_items' => [
                'spaces' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "spaces.index",
                    'text' => 'Spaces',
                ],
            ]
        ],
        'Transaction' => [
            'dropdown_id' => 'transactions',
            'dropdown_text' => 'Transactions',
            'dropdown_items' => [
                'trade' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "trades.index",
                    'text' => 'Trades',
                ],
                'journal_supplies' => [
                    'auth' => $settings['supplies'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "journal_supplies.index",
                    'text' => 'Journal Supplies',
                ],
                'journal_accounts' => [
                    'auth' => $settings['accounting'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "journal_accounts.index",
                    'text' => 'Journal Accounts',
                ],
            ]
        ],
        'Players' => [
            'dropdown_id' => 'players',
            'dropdown_text' => 'Players',
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
        'Inventories' => [
            'dropdown_id' => 'inventories',
            'dropdown_text' => 'Inventories',
            'dropdown_items' => [
                'items' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "items.index",
                    'text' => 'Items',
                ],
                'supplies' => [
                    'auth' => $settings['supplies'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "supplies.index",
                    'text' => 'Supplies',
                ],
                'accountsp' => [
                    'auth' => $settings['accounting'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "accountsp.index",
                    'text' => 'Accounts',
                ],
                'accounts' => [
                    'auth' => $settings['accounting'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "accounts.index",
                    'text' => 'Akun',
                ]
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
                'balance_sheet' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "accountsp.summary",
                    'text' => 'Neraca',
                    'route_params' => [
                        'summary_type' => 'balance_sheet',
                    ],
                ],
                'profit_loss' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "accountsp.summary",
                    'text' => 'Laba Rugi',
                    'route_params' => [
                        'summary_type' => 'profit_loss',
                    ],
                ],
                'cashflow' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "accountsp.summary",
                    'text' => 'Arus Kas',
                    'route_params' => [
                        'summary_type' => 'cashflow',
                    ],
                ]
            ]
        ],
        'Access' => [
            'dropdown_id' => 'access',
            'dropdown_text' => 'Access',
            'dropdown_items' => [
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