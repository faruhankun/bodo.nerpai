<?php

namespace App\Services\Primary\Basic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class SpaceService
{
    public function getSpaceId(Request $request)
    {
        $space_id = $request->space_id ?? (session('space_id') ?? null);
        if(is_null($space_id)){
            abort(403);
        }

        return $space_id;
    }
}
