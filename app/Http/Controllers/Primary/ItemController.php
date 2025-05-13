<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Primary\Item;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;



class ItemController extends Controller
{
    public function index()
    {
        return view('primary.items.index');
    }




    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $item = Item::create($validatedData);

        return redirect()->route('items.index')->with('success', 'Item created successfully');
    }
    


    public function update(Request $request, $id)
    {   
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        $item = Item::find($id);
        $item->update($validatedData);


        return redirect()->route('items.index')->with('success', 'Item updated successfully');
    }



    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully');
    }



    public function getItemsData(){
        $items = Item::all();

        return DataTables::of($items)
            ->addColumn('actions', function ($data) {
                $route = 'items';
                
                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.items.show',
                    'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }



    public function searchItem(Request $request)
    {
        $search = $request->q;

        $items = Item::where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('code', 'like', "%$search%")
                ->orWhere('sku', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%");
        })
            ->orderBy('id', 'desc')
            ->limit(50) // limit hasil
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => "{$item->id} - {$item->sku} - {$item->name} : {$item->notes}",
                ];
            });

        return response()->json($items);
    }
}
