<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HouseholdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() === true;
    }

    public function rules(): array
    {
        $id = $this->route('household')?->id;

        return ['household_number' => ['required', 'string', 'max:50', Rule::unique('households')->ignore($id)], 'address' => ['required', 'string', 'max:255'], 'zone' => ['required', 'string', 'max:80']];
    }
}
