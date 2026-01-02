<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductReqeust extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $this->product->id,
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'SKU' => 'sometimes|required|string|max:100|unique:products,SKU,' . $this->product->id,
            'image' => 'sometimes|nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg',
            'is_active' => 'boolean',
            'categorys' => 'sometimes|required|array',
            'categorys.*' => 'sometimes|exists:categories,id',
        ];
    }
}
