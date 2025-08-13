<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;



class ActivityLogController extends Controller
{
    public function getData(Request $request){
        $query = Activity::query();



        if ($request->filled('user_id')) {
            $query->whereCauserId($request->user_id);
        }


        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $logs = $query->latest();



        $return_type = $request->get('return_type') ?? 'json';
        if($return_type == 'DT'){
            return DataTables::of($logs)
                ->filter(function ($query) use ($request) {                                  
                    if ($request->has('search') && $request->search['value'] || $request->filled('q')) {
                        $search = $request->search['value'] ?? $request->q;

                        $query = $query->where(function ($q) use ($search) {
                            $q->where('description', 'like', "%$search%")
                            ->orWhere('properties', 'like', "%$search%");
                        });
                    }    
                })
                ->make(true);
        }


        return DataTables::of($logs)->make(true);
    }


    public function index(Request $request)
    {
        $query = Activity::query();

        if ($request->filled('user_id')) {
            $query->whereCauserId($request->user_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $logs = $query->latest()->get();

        return view('logs.index', compact('logs'));
    }
}
