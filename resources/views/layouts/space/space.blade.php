@php
    $sidebar_access = [
        'players' => auth()->user()->can('players sidebar', 'web'),
        'users' => auth()->user()->can('users sidebar', 'web'),
        'persons' => auth()->user()->can('persons sidebar', 'web'),
        'companies' => auth()->user()->can('companies sidebar', 'web'),
        'roles' => auth()->user()->can('roles sidebar', 'web'),
        'permissions' => auth()->user()->can('permissions sidebar', 'web'),
    ];
@endphp

@extends('layouts.base', [
    'navbar_left' => [
        'navbar-nerpai-name',
    ],
    'navbar_right' => [
        'navbar.space-switcher',
    ],
    'navbar_dropdown_user' => [
        'navbar-user-profile',
        'navbar-user-logout',
    ],
    'sidebar' => [
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
                'journal_accounts' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "journal_accounts.index",
                    'text' => 'Journal Accounts',
                ],
            ]
        ],
        'Players' => [
            'dropdown_id' => 'players',
            'dropdown_text' => 'Space Players',
            'dropdown_items' => [
                'players' => [
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
                'inventories' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "inventories.index",
                    'text' => 'Inventories',
                ],
                'accountsp' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "accountsp.index",
                    'text' => 'Accounts',
                ],
            ]
        ],
        'Space Access' => [
            'dropdown_id' => 'space-access',
            'dropdown_text' => 'Space Access',
            'dropdown_items' => [
                'settings' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "dashboard_space",
                    'text' => 'Space Settings',
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
        'Exit' => [
            'icon' => 'icon-arrow-right',
            'route' => "spaces.exit",
            'text' => 'Exit Space',
        ],
    ]
])

@section('main-content')
    {{ $slot }}
    @include('layouts.footer')
@endsection