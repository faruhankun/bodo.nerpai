@php
    $layout = $layout ?? session('layout') ?? 'lobby';
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div id="coaTree"></div>
</x-dynamic-component>

<script>
    $(document).ready(function () {
        $('#coaTree').jstree({
            core: {
                data: {
                    url: "{{ route('accounts.tree') }}", // bikin route baru
                    dataType: "json"
                }
            }
        });

        // klik node
        $('#coaTree').on("select_node.jstree", function (e, data) {
            let node = data.node;
            console.log("Klik akun:", node);
            // contoh: buka modal detail akun
            // show_account_modal(node.original);
        });
    });
</script>