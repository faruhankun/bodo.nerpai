<x-button-show :route="route('products.show', $product->id)" />
<!-- <x-button-edit :route="route('products.edit', $product->id)" /> -->
<x-button-delete :route="route('products.destroy', $product->id)" />