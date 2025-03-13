<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdministratorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var \App\Models\Administrator $administrator */
        $administrator = $this->route('administrator');

        return [
            'username' => [
                'required',
                'string',
                'max:64',
                Rule::unique('administrators', 'username')->ignore($administrator),
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:32',
            ],
            'state' => [
                'required',
                'boolean',
            ],
            'role_ids' => [
                'nullable',
                'array',
            ],
            'role_ids.*' => [
                'integer',
            ],
        ];
    }
}
