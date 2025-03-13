<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        /** @var \App\Models\Role $role */
        $role = $this->route('role');

        return [
            'show_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'show_name')->where('guard_name', 'administrator')->ignore($role),
            ],
            'state' => [
                'required',
                'boolean',
            ],
            'permission_ids' => [
                'array',
            ],
            'permission_ids.*' => [
                'nullable',
                'integer',
            ],
        ];
    }
}
