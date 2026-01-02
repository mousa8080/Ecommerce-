<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $this->category->id,
            'slug' => 'sometimes|nullable|string|max:255|unique:categories,slug,' . $this->category->id,
            'description' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|nullable|boolean',
            'parent_id' => 'sometimes|nullable|exists:categories,id',
        ];
    }
}
