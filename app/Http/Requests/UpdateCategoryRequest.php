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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'       => 'sometimes|string|max:255|unique:kategoris,name,' . $this->route('category'),
            'product_id' => 'sometimes|exists:products,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string'           => 'Nama kategori harus berupa teks.',
            'name.max'              => 'Nama kategori maksimal 255 karakter.',
            'name.unique'           => 'Nama kategori sudah terdaftar.',

            'product_id.exists'     => 'Product yang dipilih tidak valid.',
        ];
    }
}
