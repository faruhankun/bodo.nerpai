<?php

namespace App\Http\Controllers\Primary\Player;
use App\Http\Controllers\Controller;

use App\Models\Primary\Player;
use App\Models\Primary\Space;
use App\Models\Primary\Relation;

use App\Services\Primary\Player\ContactService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Yajra\DataTables\Facades\DataTables;


class ContactController extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }



    // Export Import
    public function eximData(Request $request){
        $query = $request->get('query');
        
        try {
            switch($query){
                case 'importTemplate':
                    $response = $this->contactService->getImportTemplate();
                    break;
                case 'export':
                    $response = $this->contactService->exportData($request);
                    break;
                case 'import':
                    $response = $this->contactService->importData($request);
                    break;
                default:
                    $response = response()->json(['error' => 'Invalid query'], 400);
                    break;

            }
            
            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function index()
    {
        return view('primary.player.contacts.index');
    }


    // crud
    public function store(Request $request){ return $this->contactService->store($request); }
    public function update(Request $request, $id){ return $this->contactService->update($request, $id); }


    public function destroy(Request $request, $id)
    {
        $request_source = get_request_source($request);
        $relations = collect();

        try {
            $space_id = get_space_id($request);

            if($space_id){
                $relation = Relation::findOrFail($id);
                $model2_id = $relation->model2_id;

                $relation->delete();


                // delete in children too
                $space_and_children = Space::find($space_id)->AllChildren()->pluck('id')->toArray();
                // $space_and_children = array_merge($space_and_children, [$space_id]);

                $relations = Relation::where('model1_type', 'SPACE')
                                    ->whereIn('model1_id', $space_and_children)
                                    ->where('model2_type', 'PLAY')
                                    ->where('model2_id', $id)
                                    ->get();

                foreach ($relations as $relation) {
                    $relation->delete();
                }
            }
        } catch (\Exception $e) {
            if($request_source == 'api'){
                return response()->json(['message' => $e->getMessage(), 'success' => false, 'data' => []], 500);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        if($request_source == 'api'){
            return response()->json(['message' => 'Player Kicked successfully :)', 'success' => true, 'data' => $relations], 200);
        }

        return redirect()->route('space_players.index')->with('success', 'Player Kicked successfully :)');
    }



    public function getQueryData(Request $request){
        $space_id = get_space_id($request);

        $query = Relation::with('model2', 'model2.type', 'model2.size')
                        ->where('model1_type', 'SPACE')
                        ->where('model1_id', $space_id)
                        ->where('model2_type', 'PLAY');

        return $query;
    }


    public function getContactsData(Request $request){
        $request_source = get_request_source($request);

        $query = $this->getQueryData($request);


        // Limit
        $limit = $request->get('limit');
        if($limit){
            if($limit != 'all'){
                $query->limit($limit);
            } 
        } else {
            $query->limit(50);
        }

        
        // Search
        $keyword = $request->get('q');
        if($keyword){
            $query->where(function($q) use ($keyword){
                $q->where('type', 'like', "%{$keyword}%")
                ->orWhere('notes', 'like', "%{$keyword}%");
            });
        }



        // return result
        $result = DataTables::of($query);

        if($request_source == 'api'){
            return $result->make(true);
        }
        
        return $result->addColumn('size_display', function ($data) {
                return ($data->size_type ?? '?') . ' : ' . ($data->size?->number ?? '?');
            })
            ->addColumn('actions', function ($data) {
                $route = 'contacts';
                
                $actions = [
                    'show' => 'modaljs',
                    // 'show_modal' => 'space.contacts.show',
                    // 'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }



    // Summary
    public function summary(Request $request)
    {
        // generate data by date
        $validated = $request->validate([
            'summary_type' => 'nullable|string',
            'query' => 'nullable|string',
        ]);

        if(isset($validated['query'])){
            if($validated['query'] == 'summary'){
                $response = $this->contactService->getSummaryData($request);
                return $response;
            }
        }

        $data = collect();
        $data->summary_types = $this->contactService->summary_types;

        return view('primary.player.contacts.summary', compact('data'));
    }
}
