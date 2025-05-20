<?php

namespace App\Services\Primary\Basic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class EximService
{
    // Export Import
    public function convertCSVtoArray($file, $data = []){
        $requiredHeaders = $data['requiredHeaders'] ?? [];
        $data = [];

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

        return $data;
    }



    public function exportCSV($data = [], $columns = [])
    {
        $filename = $data['filename'] ?? "import_template.csv";

        // Open a memory "file" for writing CSV data
        $callback = function () use ($columns) {
            try {
                $file = fopen('php://output', 'w');

                // tulis header
                if(isset($columns[0])){
                    if(is_array($columns[0])){
                        fputcsv($file, array_keys($columns[0])); // tulis header
        
                        // tulis data
                        foreach ($columns as $row) {
                            fputcsv($file, $row);           
                        }
                    } else {
                        fputcsv($file, $columns);
                    }
                }

                fclose($file);

            } catch (\Exception $e) {
                return back()->with('error', 'Error exporting CSV file. Please contact the administrator.' . $e->getMessage());
            }
        };

        return Response::stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }


    // export
    public function exportQuery($query, $params, $filter_columns = []){
        // Apply search filter
        if(!empty($filter_columns)){
            if (!empty($params['search']['value'])) {
                $search = $params['search']['value'];
                $query->where(function ($q) use ($search, $filter_columns) {
                    $q->where($filter_columns[0], 'like', "%$search%");

                    foreach ($filter_columns as $i => $filter_column) {
                        if($i === 0) continue;
                        $q->orWhere($filter_column, 'like', "%$search%");
                    }
                });
            }
        }

        // Apply ordering
        if (!empty($params['order'][0])) {
            $colIdx = $params['order'][0]['column'];
            $dir = $params['order'][0]['dir'];

            // ambil nama kolom dari index
            $column = $params['columns'][$colIdx]['data'] ?? 'id';
            $query->orderBy($column, $dir);
        }

        return $query;
    }
}
