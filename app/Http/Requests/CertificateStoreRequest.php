<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CertificateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['resident_id' => ['nullable', 'integer', 'exists:residents,id'], 'certificate_type' => ['required', Rule::in(['barangay_clearance', 'residency', 'indigency'])], 'purpose' => ['required', 'string', 'max:255']];
    }
}
