<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaketRequest extends FormRequest
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
            'nama_paket' => 'required|string|max:255|unique:paket_internet,nama_paket,' . $this->route('paket')->id,
            'harga' => 'required|numeric|min:0',
            'bandwidth' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_paket.required' => 'Nama paket wajib diisi.',
            'nama_paket.unique' => 'Nama paket sudah ada.',
            'harga.required' => 'Harga paket wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'bandwidth.required' => 'Bandwidth wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ];
    }
}