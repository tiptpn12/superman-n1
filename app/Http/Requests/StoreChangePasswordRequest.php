<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Izinkan semua pengguna untuk membuat request ini
    }

    public function rules()
    {
        return [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8', // Minimal 8 karakter
                'regex:/[a-z]/', // Harus mengandung setidaknya satu huruf kecil
                'regex:/[A-Z]/', // Harus mengandung setidaknya satu huruf besar
                'regex:/[0-9]/', // Harus mengandung setidaknya satu angka
                'regex:/[\W]/'   // Harus mengandung setidaknya satu karakter spesial
            ],
            'new_password_validate' => 'required|same:new_password', // Pastikan password konfirmasi sama
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'Password saat ini harus diisi.',
            'new_password.required' => 'Password baru harus diisi.',
            'new_password.string' => 'Password baru harus berupa string.',
            'new_password.min' => 'Password baru harus minimal 8 karakter.',
            'new_password.regex' => 'Password baru harus mengandung: setidaknya satu huruf kecil, satu huruf besar, satu angka, dan satu karakter spesial.',
            'new_password_validate.required' => 'Konfirmasi password harus diisi.',
            'new_password_validate.same' => 'Konfirmasi password harus sama dengan password baru.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->redirectToRoute('change_password')->withErrors($validator)->withInput());
    }
}
