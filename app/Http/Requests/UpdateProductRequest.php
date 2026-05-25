<?php

namespace App\Http\Requests;

use App\Enum\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        return [
            'name' => ['string', 'min:10', 'max:255', 'nullable'],
            'description' => ['string', 'nullable'],
            'price' => ['numeric', 'min:1', 'nullable'],
            'status' => [Rule::enum(ProductStatus::class)]
        ];
    }
}
