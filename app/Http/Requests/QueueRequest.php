<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueueRequest extends FormRequest
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
            'counter_id' => 'required|string|exists:counters,counter_id',
        ];

        // For update operations
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'status' => 'sometimes|in:waiting,called,done',
                'called_at' => 'sometimes|nullable|date',
                'called_by' => 'sometimes|nullable|string|exists:users,user_id',
            ];
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
            'counter_id.required' => 'Counter ID wajib diisi.',
            'counter_id.string' => 'Counter ID harus berupa teks.',
            'counter_id.exists' => 'Counter tidak ditemukan.',
            
            'status.in' => 'Status harus berupa waiting, called, atau done.',
            
            'called_at.date' => 'Format tanggal tidak valid.',
            
            'called_by.string' => 'Called by harus berupa teks.',
            'called_by.exists' => 'User tidak ditemukan.',
        ];
    }
}
