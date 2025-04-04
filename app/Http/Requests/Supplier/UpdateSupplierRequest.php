<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

enum StatusEnum: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
}

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|json',
            'email' => 'nullable|string|email',
            'phone_number' => 'nullable|string',
            'status' => ['required', new Enum(StatusEnum::class)],
            'notes' => 'nullable|string',
            'entity_type' => 'nullable|string',
            'entity_id' => 'nullable|integer',
        ];
    }
}
