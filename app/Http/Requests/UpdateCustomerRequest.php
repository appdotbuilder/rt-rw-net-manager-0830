<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:20',
            'username_pppoe' => 'required|string|max:50|unique:customers,username_pppoe,' . $this->route('customer')->id,
            'password_pppoe' => 'nullable|string|min:8',
            'paket_id' => 'required|exists:paket_internet,id',
            'ip_pool' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif,suspended',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
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
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'kontak.required' => 'Kontak wajib diisi.',
            'username_pppoe.required' => 'Username PPPoE wajib diisi.',
            'username_pppoe.unique' => 'Username PPPoE sudah digunakan.',
            'password_pppoe.min' => 'Password PPPoE minimal 8 karakter.',
            'paket_id.required' => 'Paket internet wajib dipilih.',
            'paket_id.exists' => 'Paket internet tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'foto_ktp.image' => 'File harus berupa gambar.',
            'foto_ktp.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'foto_ktp.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}