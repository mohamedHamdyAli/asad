<?php

namespace App\Http\Requests\Api;
use Illuminate\Http\Request;


class QuoteRequest extends BaseApiRequest
{

    public function __construct(Request $request)
    {

        $request->merge([
            'user_id' => guestAuth() != null ? guestAuth()->id : userAuth()->id,
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
            'type_of_building_id' => 'required|exists:type_of_buildings,id',
            'type_of_price_id' => 'required|exists:type_of_prices,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'other_title' => 'nullable|string',
            'images' => 'required|array',
            'images.*' => 'required|file|mimes:jpeg,png,jpg,svg,webp|max:6144',
            'pay_image' => 'required|file|mimes:jpeg,png,jpg,svg,webp|max:6144',

        ];
    }
}
