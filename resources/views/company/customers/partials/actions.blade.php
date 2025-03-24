<x-button-show :route="route('customers.show', $customer->id)" />
<!-- <x-button-edit :route="route('customers.edit', $customer->id)" /> -->
<x-button-delete :route="route('customers.destroy', $customer->id)" />