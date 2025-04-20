<?php

namespace App\Http\Middleware\Space;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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
                    // exit space
                    $this->forgetSpace();

                    // change layout to lobby
                    Session::put('layout', 'lobby');

                    // Redirect ke halaman lobby (atau dashboard utama)
                    return redirect()->route('lobby')->with('status', 'You have exited the space.');

                    abort(403);
                }
            }
        }

        return $next($request);
    }


    public function forgetSpace()
	{
		// to forget from what space had
		foreach(session()->all() as $key => $value) {
			if(str_contains($key, 'space')) {
				session()->forget($key);				
			}
		}
	}
}
