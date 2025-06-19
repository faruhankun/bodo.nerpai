<?php

namespace App\Http\Controllers\Primary\Player;
use App\Http\Controllers\Controller;

use App\Models\Primary\Player;
use App\Models\Primary\Space;
use App\Models\Primary\Relation;

use App\Services\Primary\Basic\EximService;
use App\Services\Primary\Player\PlayerService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Yajra\DataTables\Facades\DataTables;


class ContactController extends Controller
{
    protected $eximService;
    protected $playerService;

    public function __construct(EximService $eximService, PlayerService $playerService)
    {
        $this->eximService = $eximService;
        $this->playerService = $playerService;
    }



    // Export Import
    public function eximData(Request $request){
        $query = $request->get('query');
        
        try {
            switch($query){
                case 'importTemplate':
                    $response = $this->playerService->getImportTemplate();
                    break;
                case 'export':
                    $response = $this->playerService->exportData($request);
                    break;
                case 'import':
                    $response = $this->playerService->importData($request);
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



    public function destroy($id)
    {
        $space_id = session('space_id') ?? null;

        if($space_id){
            $space_and_children = Space::find($space_id)->AllChildren()->pluck('id')->toArray();
            $space_and_children = array_merge($space_and_children, [$space_id]);

            $relations = Relation::where('model1_type', 'SPACE')
                                ->whereIn('model1_id', $space_and_children)
                                ->where('model2_type', 'PLAY')
                                ->where('model2_id', $id)
                                ->get();

            foreach ($relations as $relation) {
                $relation->delete();
            }
        }

        return redirect()->route('space_players.index')->with('success', 'Player Kicked successfully :)');
    }



    public function getContactsData(){
        $space_id = Session::get('space_id') ?? null;

        $space = Space::find($space_id);
        $contacts = $space->players;

        return DataTables::of($contacts)
            ->addColumn('size_display', function ($data) {
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
}
