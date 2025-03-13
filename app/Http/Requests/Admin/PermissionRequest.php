<?php

namespace App\Http\Requests\Admin;

use App\Enums\PermissionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PermissionRequest extends FormRequest
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
        $permissionTypeEnum = PermissionTypeEnum::from($this->input('type'));

        $rules = [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('permissions', 'id'),
            ],
            'show_name' => [
                'required',
                'string',
                'max:50',
            ],
            'type' => [
                'required',
                new Enum(PermissionTypeEnum::class),
            ],
            'order' => [
                'required',
                'integer',
            ],
        ];
        if ($permissionTypeEnum === PermissionTypeEnum::BUTTON) {
            $rules['name'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('permissions', 'name')
                    ->where('guard_name', 'administrator')
                    ->ignore($this->route('permission')),
            ];
        } else {
            $rules['visible'] = [
                'required',
                'boolean',
            ];
            $rules['icon'] = [
                'nullable',
                'string',
                'max:50',
            ];
            $rules['path'] = [
                'required',
                'string',
                'max:100',
            ];
            $rules['query'] = [
                'nullable',
                'string',
                'max:100',
            ];

            if ($permissionTypeEnum === PermissionTypeEnum::MENU) {
                $rules['component'] = [
                    'required',
                    'string',
                    'max:100',
                ];
            }
        }

        return $rules;
    }
}
