<x-crud.modal-create title="Import Journal" trigger="Import Journal">
    <form action="{{ route('journal_supplies.import') }}" method="POST" enctype="multipart/form-data"
        class="flex items-center w-full">
        @csrf
        <div class="flex flex-col items-start space-y-1">
            <label class="block text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
                CSV</label>
            <input
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                aria-describedby="file_input_help" id="file" name="file" type="file" accept=".csv, .txt" required>
            <a class=" text-sm text-gray-500 dark:text-gray-300 hover:underline" id="file_input_help"
                href="{{ route('journal_supplies.template') }}">Download
                Template</a>


        </div>
        <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded">
            Upload CSV
        </button>
    </form>
</x-crud.modal-create>