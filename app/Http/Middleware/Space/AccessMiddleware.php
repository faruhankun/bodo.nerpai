<?php

namespace App\Http\Middleware\Space;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class AccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $space_id = session('space_id') ?? null;

        if($space_id){
            $player = Auth::user()->player;

            if(!$player->spaces->where('id', $space_id)->first()){

                // check in parent
                if(!$player->spacesWithDescendants()->where('id', $space_id)->first()){
                    abort(403);
                }
            }
        }

        return $next($request);
    }
}
