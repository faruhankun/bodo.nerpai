@php
    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;

    $space_name = "Space: ". (session('space_name') ?? (( session('space_id') == null ) ? 'Lobby' : 'Space - Name : ??'));
    $related_spaces = [];
    $space_id = session('space_id') ?? null;
    $parent_space_id = session('space_parent_id') ?? null;

    if($space_id){
        $related_spaces = $player?->spacesWithDescendants() ?? [];
        
        $parent_space = $related_spaces->where('id', '==', $parent_space_id) ?? [];
        $children_spaces = $related_spaces->where('parent_id', '==', $space_id) ?? [];
        
        $related_spaces = $parent_space->merge($children_spaces);
    } else {
        $related_spaces = $player?->spacesWithDescendants() ?? [];
    }
@endphp

<div class="mr-3 relative" x-data="{ open: false }">
    <button @click="open = !open"
        class="flex items-center p-2 text-sm text-gray-500 rounded-lg dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700">
        <span>{{ $space_name }}</span>
        <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <div x-show="open" @click.outside="open = false"
    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 dark:bg-gray-700">
        @foreach ($related_spaces as $space)
            <form method="POST" action="{{ route('spaces.switch', $space->id) }}">
            @csrf
                <button type="submit" name="space_id" value="{{ $space->id }}"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ $space->name }}
                </button>
            </form>
        @endforeach
        <hr class="dark:border-gray-600 border-gray-200">
        <a href="{{ route('spaces.exit', 'spaces.index') }}">
            <button type="button"
            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600">
                {{ __('List Spaces') }}
            </button>
        </a>
    </div>
</div>