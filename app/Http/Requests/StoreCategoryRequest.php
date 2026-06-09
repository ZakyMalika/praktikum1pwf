<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name'       => 'required|string|max:255|unique:kategoris,name',
            'product_id' => 'required|exists:products,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'         => 'Nama kategori wajib diisi.',
            'name.string'           => 'Nama kategori harus berupa teks.',
            'name.max'              => 'Nama kategori maksimal 255 karakter.',
            'name.unique'           => 'Nama kategori sudah terdaftar.',

            'product_id.required'   => 'Product harus dipilih.',
            'product_id.exists'     => 'Product yang dipilih tidak valid.',
        ];
    }
}
