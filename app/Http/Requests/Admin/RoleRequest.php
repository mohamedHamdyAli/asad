<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'permissions' => 'array|nullable',
            'permissions.*' => 'exists:permissions,name',
        ];
    }
}
