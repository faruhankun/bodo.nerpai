@php
    $sidebar_access = [
        'players' => auth()->user()->can('players sidebar', 'web'),
        'users' => auth()->user()->can('users sidebar', 'web'),
        'persons' => auth()->user()->can('persons sidebar', 'web'),
        'groups' => auth()->user()->can('groups sidebar', 'web'),
        'companies' => auth()->user()->can('companies sidebar', 'web'),
        'roles' => auth()->user()->can('roles sidebar', 'web'),
        'permissions' => auth()->user()->can('permissions sidebar', 'web'),
        'items' => auth()->user()->can('items sidebar', 'web'),
    ];
@endphp

@extends('layouts.base', [
    'navbar_left' => [
        'navbar-nerpai-name',
    ],
    'navbar_right' => [
        'navbar.player-switcher',
        'navbar.space-switcher',
    ],
    'navbar_dropdown_user' => [
        'navbar-user-profile',
        'navbar-user-logout',
    ],
    'sidebar' => [
        'Dashboard' => [
            'icon' => 'icon-sidebar',
            'route' => "lobby",
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
                'groups' => [
                    'auth' => $sidebar_access['groups'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "groups.index",
                    'text' => 'Groups',
                ],
            ]
        ],
        'World Access' => [
            'dropdown_id' => 'world-access',
            'dropdown_text' => 'World Access',
            'dropdown_items' => [
                'roles' => [
                    'auth' => $sidebar_access['roles'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "roles.index",
                    'text' => 'World Roles',
                ],
                'permissions' => [
                    'auth' => $sidebar_access['permissions'],
                    'icon' => 'icon-checklist-paper',
                    'route' => "permissions.index",
                    'text' => 'World Permissions',
                ],
                'settings' => [
                    'auth' => false,
                    'icon' => 'icon-checklist-paper',
                    'route' => "lobby",
                    'text' => 'World Settings',
                ],
            ]
        ],
        'Exit' => [
            'icon' => 'icon-arrow-right',
            'route' => "lobby",
            'text' => 'Exit World',
        ],
    ]
])

@section('main-content')
    {{ $slot }}
    @include('layouts.footer')
@endsection