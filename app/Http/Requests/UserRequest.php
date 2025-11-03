<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|in:admin,petugas',
            'counter_id' => 'required|string|exists:counters,counter_id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Route parameter 'user' is the user_id string
            $userId = $this->route('user');
            $rules['full_name'] = 'sometimes|string|max:255';
            $rules['email'] = 'sometimes|string|email|max:255|unique:users,email,' . $userId . ',user_id';
            $rules['role'] = 'sometimes|in:admin,petugas';
            $rules['counter_id'] = 'sometimes|string|exists:counters,counter_id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.string' => 'Nama lengkap harus berupa teks.',
            'full_name.max' => 'Nama lengkap maksimal 255 karakter.',
            
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah terdaftar.',
            
            'role.required' => 'Role wajib diisi.',
            'role.in' => 'Role harus berupa admin atau petugas.',
            
            'counter_id.required' => 'Counter ID wajib diisi.',
            'counter_id.string' => 'Counter ID harus berupa teks.',
            'counter_id.exists' => 'Counter tidak ditemukan.',
        ];
    }
}
