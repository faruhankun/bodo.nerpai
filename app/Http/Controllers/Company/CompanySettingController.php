<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Company\CompanySetting;

class CompanySettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = CompanySetting::orderBy('key', 'asc')->get();

        return view('company.company_settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
            'module' => 'nullable|string',
            'source_type' => 'nullable|in:ST,WH',
            'source_id' => 'nullable|integer'
        ]);

        $setting = CompanySetting::set(
            $request->key,
            $request->value,
            $request->module,
            $request->source_type,
            $request->source_id,
        );

        return redirect()->route('company_settings.index')->with('success', "CompanySetting {$setting->key} created successfully");
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required',
            'module' => 'nullable|string',
            'source_type' => 'nullable|in:ST,WH',
            'source_id' => 'nullable|integer'
        ]);

        $setting = CompanySetting::findOrFail($id);
        $setting->update($validated);

        // Reset cache
        cache()->forget("setting:$setting->key:$setting->source_type:$setting->source_id");

        return redirect()->route('company_settings.index')->with('success', "CompanySetting {$setting->key} updated successfully");
    }

    public function destroy($id)
    {
        $setting = CompanySetting::findOrFail($id);
        cache()->forget("setting:{$setting->key}:{$setting->source_type}:{$setting->source_id}");
        $setting->delete();

        return redirect()->route('company_settings.index')->with('success', "CompanySetting {$setting->key} deleted successfully");
    }
}

