<x-div.box-show title="Export">
    <div class="flex flex-col items-start space-y-1 mt-4">
        <button class="ml-2 bg-blue-500 text-white px-4 py-2 rounded">
            <a href="#" id="exportVisibleBtn" class="btn btn-success">
                Export yang Ditampilkan (CSV upto 1000)
            </a>
        </button>
    </div>
    {{ $slot }}
</x-div.box-show>