<?php

use Illuminate\Http\Request;

use App\Models\Primary\Access\Variable;

use Illuminate\Support\Str;


if (!function_exists('get_variable')) {
    function get_variable($key, $sourceType = null, $sourceId = null) {
        if (is_null($sourceType) && is_null($sourceId)) {
            $sourceType = 'SPACE';
            $sourceId = session('space_id') ?? null;
        }
        
        return Variable::get($key, $sourceType, $sourceId);
    }
}



use App\Models\Primary\Space;
if(!function_exists('get_space_id')) {
    function get_space_id(Request $request, $abort = true) {
        $space_id = $request->space_id ?? ($request->header('X-Space-Id') ?? (session('space_id') ?? null));
        if(is_null($space_id) && $abort) {
            abort(403);
        }

        return $space_id;
    }
}



if(!function_exists('get_player_id')) {
    function get_player_id(Request $request, $abort = true) {
        $player_id = $request->player_id ?? 
                        ($request->header('X-Player-Id') ?? 
                            (session('player_id') ?? 
                                (Auth::user()->player_id ?? null
                        )));
        if(is_null($player_id) && $abort) {
            abort(403);
        }

        return $player_id;
    }
}



if(!function_exists('get_request_source')){
    function get_request_source(Request $request) {
        $source = $request->input('request_source') ?? (Str::startsWith($request->path(), 'api') ? 'api' : 'web');

        return $source;
    }
}



if(!function_exists('get_token_web')) {
    function get_token_web(Request $request) {
        if(!session('token')) {
            session(['token' => auth()->user()->createToken('react-web')->plainTextToken]);
        }

        return session('token');
    }
}