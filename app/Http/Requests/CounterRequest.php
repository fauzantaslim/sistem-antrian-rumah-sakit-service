<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CounterRequest extends FormRequest
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
            'counter_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['counter_name'] = 'sometimes|string|max:255';
            $rules['description'] = 'nullable|string|max:500';
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
            'counter_name.required' => 'Nama counter wajib diisi.',
            'counter_name.string' => 'Nama counter harus berupa teks.',
            'counter_name.max' => 'Nama counter maksimal 255 karakter.',

            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
        ];
    }
}
