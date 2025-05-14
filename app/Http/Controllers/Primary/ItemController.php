<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Primary\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use Yajra\DataTables\Facades\DataTables;



class ItemController extends Controller
{
    public function index()
    {
        return view('primary.items.index');
    }




    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:255',
                'status' => 'required|string|max:50',
                'notes' => 'nullable|string',
            ]);

            $item = Item::create($validatedData);

            return redirect()->route('items.index')->with('success', 'Item created successfully');
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }
    


    public function update(Request $request, $id)
    {   
        try {
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
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. Please try again. ' . $th->getMessage());
        }
    }



    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        
        // check related inventory
        if ($item->inventories()->exists()) {
            return back()->with('error', 'Item has related inventory. Cannot delete.');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully');
    }



    public function getItemsData(){
        $space_id = session('space_id') ?? null;

        $items = Item::where('space_type', 'SPACE')
            ->where('space_id', $space_id);

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



    public function importData(Request $request)
    {
        $space_id = session('space_id') ?? null;

        try {
            $validated = $request->validate([
                'file' => 'required|mimes:csv,txt'
            ]);

            $file = $validated['file'];
            $data = [];
            $failedRows = [];
            $requiredHeaders = ['code', 'sku', 'name'];

            // Read the CSV into an array of associative rows
            if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
                $headers = fgetcsv($handle);

                // validasi header
                foreach ($requiredHeaders as $header) {
                    if (!in_array($header, $headers)) {
                        return back()->with('error', 'Invalid CSV file. Missing required header: ' . $header);
                    }
                }

                // Loop through the rows
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $record = [];
                    foreach ($headers as $i => $header) {
                        $record[trim($header, " *")] = $row[$i] ?? null;
                    }
                    $data[] = $record;
                }
                fclose($handle);
            }


            // input
            foreach ($data as $i => $row) {
                try {
                    // skip if no code or name
                    if (empty($row['sku']) || empty($row['name'])) {
                        throw new \Exception('Missing required field: sku or name');
                    }

                    $item = Item::where('code', $row['code'])
                    ->orWhere('sku', $row['sku'])
                    ->orWhere('name', $row['name'])
                    ->first();

                    $payload = [
                        'code' => $row['code'],
                        'sku' => $row['sku'],
                        'name' => $row['name'],
                        'price' => $row['price'] ?? 0,
                        'cost' => $row['cost'] ?? 0,
                        'weight' => $row['weight'] ?? 0,
                        'notes' => $row['notes'] ?? null,
                    ];

                    $payload['space_type'] = 'SPACE';
                    if($space_id) 
                        $payload['space_id'] = $space_id;

                    if ($item) {
                        $item->update($payload);
                    } else {
                        Item::create($payload);
                    }
                } catch (\Throwable $e) {
                    $row['row'] = $i + 2; // +2 karena array dimulai dari 0 dan +1 untuk header CSV
                    $row['error'] = $e->getMessage();
                    $failedRows[] = $row;
                }
            }


            // Jika ada row yang gagal, langsung return CSV dari memory
            if (count($failedRows) > 0) {
                $filename = 'failed_import_' . now()->format('Ymd_His') . '.csv';

                $callback = function () use ($failedRows) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, array_keys($failedRows[0])); // tulis header

                    foreach ($failedRows as $row) {
                        fputcsv($file, $row);
                    }

                    fclose($file);
                };

                return response()->stream($callback, 200, [
                    "Content-Type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=\"$filename\"",
                    "Pragma"              => "no-cache",
                    "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                    "Expires"             => "0"
                ]);
            }

            return redirect()->route('items.index')->with('success', 'CSV uploaded and processed Successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to import csv. Please try again.' . $th->getMessage());
        }
    }



    public function importTemplate()
    {
        $headers = ['Content-Type' => 'text/csv'];
        $filename = "import_template.csv";

        // Define your column headers (template)
        $columns = ['code', 'sku', 'name', 'price', 'cost', 'weight (gram)', 'notes'];

        // Open a memory "file" for writing CSV data
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return Response::stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }


    public function exportData(Request $request)
    {
        $params = json_decode($request->get('params'), true);
        
        $space_id = session('space_id') ?? null;

        $query = Item::query()
            ->where('space_type', 'SPACE')
            ->where('space_id', $space_id);
        
        // Apply search filter
        if (!empty($params['search']['value'])) {
            $search = $params['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                ->orWhere('sku', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%")
                ->orWhere('notes', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%");
            });
        }

        // Apply ordering
        if (!empty($params['order'][0])) {
            $colIdx = $params['order'][0]['column'];
            $dir = $params['order'][0]['dir'];

            // ambil nama kolom dari index
            $column = $params['columns'][$colIdx]['data'] ?? 'id';
            $query->orderBy($column, $dir);
        }

        $query->take(1000);

        $items = $query->get();

        $filename = 'export_items_' . now()->format('Ymd_His') . '.csv';
        $columns = ['id', 'code', 'sku', 'name', 'price', 'cost', 'weight', 'notes', 'status', 'created_at', 'model_type'];

        $callback = function () use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->code,
                    $item->sku,
                    $item->name,
                    $item->price,
                    $item->cost,
                    $item->weight,
                    $item->notes,
                    $item->status,
                    $item->created_at,
                    $item->model_type,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
