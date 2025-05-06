<?php

namespace App\Http\Controllers\Primary\Access;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Access\Variable;

class VariableController extends Controller
{
    public function index(Request $request)
    {
        return view('primary.access.variables.index');
    }



    public function store(Request $request)
    {
        $space_id = session('space_id') ?? null;

        try {
            $request->validate([
                'key' => 'required',
                'name' => 'required',
                'value' => 'required',
                'status' => 'required|string|max:50',
                'notes' => 'nullable',
                'deletable' => 'required',
            ]);

            $requestData = $request->all();

            if($space_id){
                $requestData['space_type'] = 'SPACE';
                $requestData['space_id'] = $space_id;
            }

            $account = Variable::create($requestData);

            return redirect()->route('variables.index')->with('success', "Variables {$account->name} created successfully.");
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }



    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'key' => 'required',
                'name' => 'required',
                'value' => 'required',
                'status' => 'required|string|max:50',
                'notes' => 'nullable',
                'deletable' => 'required',
            ]);

            $variable = Variable::findOrFail($id);
            $variable->update($validated);

            // Reset cache
            cache()->forget("variable:$variable->key:$variable->space_type:$variable->space_id");

            return redirect()->route('variables.index')->with('success', "Variables {$variable->key} updated successfully.");
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }



    public function destroy($id)
    {
        $variable = Variable::findOrFail($id);

        cache()->forget("variable:{$variable->key}:{$variable->space_type}:{$variable->space_id}");
        $variable->delete();

        return redirect()->route('variables.index')->with('success', "Variable {$variable->key} deleted successfully");
    }



    public function getVariablesData(){
        $variables = [];

        $space_id = session('space_id') ?? null;

        $variables = Variable::with('type', 'parent')->get();

        if($space_id){
            $variables = $variables->where('space_type', 'SPACE')
                                    ->where('space_id', $space_id);
        } else {
            $variables = [];
        }

        return DataTables::of($variables)
            ->addColumn('actions', function ($data) {
                $route = 'variables';
                
                $actions = [
                    'edit' => 'modal',
                ];

                if($data->deletable){
                    $actions['delete'] = 'button';
                }

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}

