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
                'items' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "dashboard_space",
                    'text' => 'Transactions',
                ],
            ]
        ],
        'Inventory' => [
            'dropdown_id' => 'inventories',
            'dropdown_text' => 'Inventory',
            'dropdown_items' => [
                'items' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "dashboard_space",
                    'text' => 'Items',
                ],
                'inventories' => [
                    'auth' => true,
                    'icon' => 'icon-checklist-paper',
                    'route' => "dashboard_space",
                    'text' => 'Inventory',
                ],
            ]
        ],
        'Players' => [
            'dropdown_id' => 'players',
            'dropdown_text' => 'Players',
            'dropdown_items' => [
                'players' => [
                    'auth' => $sidebar_access['players'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "players.index",
                    'text' => 'Players',
                ],
                'users' => [
                    'auth' => $sidebar_access['users'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "users.index",
                    'text' => 'Users',
                ],
                'persons' => [
                    'auth' => $sidebar_access['persons'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "persons.index",
                    'text' => 'People',
                ],
                'companies' => [
                    'auth' => $sidebar_access['companies'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "companies.index",
                    'text' => 'Groups',
                ],
            ]
        ],
        'Space Access' => [
            'dropdown_id' => 'space-access',
            'dropdown_text' => 'Space Access',
            'dropdown_items' => [
                'roles' => [
                    'auth' => $sidebar_access['roles'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "roles.index",
                    'text' => 'Space Roles',
                ],
                'permissions' => [
                    'auth' => $sidebar_access['permissions'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "permissions.index",
                    'text' => 'Space Permissions',
                ],
                'settings' => [
                    'icon' => 'icon-checklist-paper',
                    'route' => "dashboard_space",
                    'text' => 'Space Settings',
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