<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ubah menjadi true agar request ini diizinkan untuk diproses
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
            'name'     => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'price'    => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required'  => 'Pemilik produk harus dipilih.',
            'user_id.exists'    => 'Pemilik produk yang dipilih tidak valid atau tidak terdaftar.',
            
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