<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:180'], 'description' => ['required', 'string', 'max:5000'], 'location' => ['required', 'string', 'max:255'], 'occurred_at' => ['required', 'date', 'before_or_equal:now']];
    }
}
