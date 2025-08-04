<?php

namespace App\Http\Requests\Api;
use Illuminate\Http\Request;

class UserIdRequest extends BaseApiRequest
{
    public function __construct(Request $request)
    {

        $request->merge([
            'user_id' => userAuth()->id,
        ]);
    }
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
        return [
            'user_id'  => 'required|exists:users,id',
        ];
    }
}
