<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() === true;
    }

    public function rules(): array
    {
        $id = $this->route('resident')?->id;

        return ['user_id' => ['nullable', 'integer', 'exists:users,id', Rule::unique('residents')->ignore($id)], 'household_id' => ['nullable', 'integer', 'exists:households,id'], 'resident_number' => ['required', 'string', 'max:50', Rule::unique('residents')->ignore($id)], 'first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'], 'birth_date' => ['required', 'date', 'before:today'], 'sex' => ['required', Rule::in(['female', 'male', 'other'])], 'civil_status' => ['required', Rule::in(['single', 'married', 'widowed', 'separated'])], 'phone' => ['nullable', 'string', 'max:30'], 'registered_voter' => ['required', 'boolean']];
    }
}
