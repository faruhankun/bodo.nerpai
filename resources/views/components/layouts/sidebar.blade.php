@props([
    'route',
    'sidebar' => [],
])

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-transform bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800 [&::-webkit-scrollbar]:w-2
  [&::-webkit-scrollbar-track]:rounded-full
  [&::-webkit-scrollbar-track]:bg-gray-100
  [&::-webkit-scrollbar-thumb]:rounded-full
  [&::-webkit-scrollbar-thumb]:bg-gray-300
  dark:[&::-webkit-scrollbar-track]:bg-neutral-700
  dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
  
        <ul class="font-medium">
            @foreach ($sidebar as $item)
                <li>
                    @if (isset($item['component']))
                        @include('components.' . $item['component'], ['item' => $item])
                    @elseif (isset($item['dropdown_id']))
                        <button class="flex items-center justify-between w-full p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                            onclick="toggleDropdown('{{ $item['dropdown_id'] }}');">
                            <div class="flex items-center">
                                @if (isset($item['dropdown_icon']))
                                    @if ($item['dropdown_icon'] == '')
                                        @php
                                            $item['dropdown_icon'] = 'icon-sidebar';
                                        @endphp
                                    @endif
                                @else 
                                    @php
                                        $item['dropdown_icon'] = 'icon-sidebar';
                                    @endphp
                                @endif
                                <x-dynamic-component :component="'icons.' . $item['dropdown_icon']" class="flex-shrink-0 w-4 h-4 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" />
                                <span class="flex-1 ms-3 whitespace-nowrap">{{ $item['dropdown_text'] }}</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform transform group-hover:rotate-180" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        @php
                            $ul_show = 'hidden';
                            foreach($item['dropdown_items'] as $route =>$dropdown_item){
                                if(Request::routeIs($route . '*')){
                                    $ul_show = 'block';
                                    break;
                                }
                            }
                        @endphp
                        <ul class="ms-6 {{ $ul_show }}" id="{{ $item['dropdown_id'] }}">
                            @foreach($item['dropdown_items'] as $route => $dropdown_item)
                                @php 
                                    $dropdown_item['auth'] = isset($dropdown_item['auth']) ? $dropdown_item['auth'] : true;
                                @endphp
                                
                                @if($dropdown_item['auth'])
                                    <li>
                                        <x-sidebar-item :icon="$dropdown_item['icon'] ?? ''" 
                                                        :route="$dropdown_item['route']"
                                                        :route_params="$dropdown_item['route_params'] ?? ''"
                                                        :text="$dropdown_item['text']">
                                            {{ $dropdown_item['text'] }}
                                        </x-sidebar-item>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        @php 
                            $item['auth'] = isset($item['auth']) ? $item['auth'] : true;
                        @endphp
                        
                        @if($item['auth'])
                            <x-sidebar-item :icon="$item['icon'] ?? ''" 
                                            :route="$item['route']" 
                                            route_params="{{ $item['route_params'] ?? ''}}" 
                                            text="{{ $item['text'] }}">
                                {{ $item['text'] }}
                            </x-sidebar-item>
                        @endif
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</aside>

<script>
    function toggleDropdown(menuId) {
        var menu = document.getElementById(menuId);
        menu.classList.toggle('hidden');
    }
</script>