<x-button-show :route="route('players.show', $player->id)" />
<!-- <x-button-edit :route="route('players.edit', $player->id)" /> -->
<x-button-delete :route="route('players.destroy', $player->id)" />