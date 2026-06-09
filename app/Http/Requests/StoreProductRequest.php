<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name'     => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'price'    => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'Nama produk wajib diisi.',
            'name.string'       => 'Nama produk harus berupa teks.',
            'name.max'          => 'Nama produk maksimal 255 karakter.',

            'quantity.required' => 'Jumlah (quantity) wajib diisi.',
            'quantity.numeric'  => 'Jumlah harus berupa angka.',
            'quantity.min'      => 'Jumlah tidak boleh kurang dari 0.',

            'price.required'    => 'Harga produk wajib diisi.',
            'price.numeric'     => 'Harga produk harus berupa angka.',
            'price.min'         => 'Harga produk tidak boleh kurang dari 0.',
        ];
    }
}
