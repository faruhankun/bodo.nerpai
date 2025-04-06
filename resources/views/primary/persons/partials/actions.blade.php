<x-button-show :route="route('persons.show', $person->id)" />
<!-- <x-button-edit :route="route('persons.edit', $person->id)" /> -->
<x-button-delete :route="route('persons.destroy', $person->id)" />