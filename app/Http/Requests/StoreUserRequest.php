<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            //
            'namadepan' => 'required',
            'namablkg' => 'required',
            'email' => 'required|email|unique:anggota',
            'password' => 'required|min:8',
        ];
    }
    public function messages(): array
    {
    return [
        'email.unique' => 'Email telah digunakan!',
    ];
    }
}
