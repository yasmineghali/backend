<?php

namespace App\Http\Requests\Bimrevo;

use App\Models\Bimrevo\Contact;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'plan' => ['nullable', 'string', Rule::in(Contact::PLANS)],
            'message' => ['required', 'string', 'max:5000'],
            'source_url' => ['nullable', 'string', 'max:500'],
            'botcheck' => ['nullable'],
        ];
    }
}
