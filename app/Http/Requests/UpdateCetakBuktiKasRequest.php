<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCetakBuktiKasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:App\CetakBuktiKas,id',
            'ubah_company_id' => 'required|exists:App\Company,company_id',
            'ubah_sub_bagian_pembuat' => 'nullable',
            'ubah_nama_pembuat' => 'nullable',
            'ubah_sub_bagian_pemeriksa' => 'required',
            'ubah_nama_pemeriksa' => 'nullable',
            'ubah_bagian_pemeriksa' => 'required',
            'ubah_nama_bagian_pemeriksa' => 'nullable',
            'ubah_yang_menyetujui' => 'required',
            'ubah_nama_yang_menyetujui' => 'nullable',
            'ubah_is_bank' => 'required|in:0,1',
            'ubah_lebih_dari_5_m' => 'required|in:0,1',
            'ubah_lebih_dari_25_jt' => 'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Field Tidak Boleh Kosong',
            'id.exists' => 'Field Tidak Boleh Kosong',
            'ubah_company_id.required' => 'Field Tidak Boleh Kosong',
            'ubah_company_id.exists' => 'Field Tidak Boleh Kosong',

            'ubah_sub_bagian_pemeriksa.required' => 'Field Tidak Boleh Kosong',
            'ubah_bagian_pemeriksa.required' => 'Field Tidak Boleh Kosong',
            'ubah_yang_menyetujui.required' => 'Field Tidak Boleh Kosong',
            'ubah_is_bank.required' => 'Field Tidak Boleh Kosong',
            'ubah_is_bank.in' => 'Field Tidak Boleh Kosong',
            'ubah_lebih_dari_5_m.required' => 'Field Tidak Boleh Kosong',
            'ubah_lebih_dari_5_m.in' => 'Field Tidak Boleh Kosong',
            'ubah_lebih_dari_25_jt.required' => 'Field Tidak Boleh Kosong',
            'ubah_lebih_dari_25_jt.in' => 'Field Tidak Boleh Kosong',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 422));
    }
}
