<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CertificateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() === true;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['approved', 'rejected', 'released'])], 'remarks' => ['nullable', 'string', 'max:1000']];
    }
}
