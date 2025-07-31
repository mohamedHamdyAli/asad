<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class FolderRequest extends FormRequest
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
        $rules = (Route::is('folder.store')) ? [
            'file_type' => 'required|string|in:gallery,document,drawing',
            'name' => 'required|string|max:255',
            'folder_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ] : [
            'file_type' => 'nullable|string|in:gallery,document,drawing',
            'name' => 'nullable|string|max:255',
            'folder_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ];

        return $rules;
    }
}
