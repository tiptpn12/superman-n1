<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCetakBuktiKasRequest extends FormRequest
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
            'company' => 'required|exists:App\Company,company_id',
            'sub_bagian_pembuat' => 'required',
            'nama_pembuat' => 'nullable',
            'sub_bagian_pemeriksa' => 'required',
            'nama_pemeriksa' => 'required',
            'bagian_pemeriksa' => 'required',
            'nama_bagian_pemeriksa' => 'required',
            'yang_menyetujui' => 'required',
            'nama_yang_menyetujui' => 'required',
            'is_bank' => 'required|in:0,1',
            'lebih_dari_5_m' => 'required|in:0,1',
            'lebih_dari_25_jt' => 'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'company.required' => 'Field Tidak Boleh Kosong',
            'company.exists' => 'Field Tidak Boleh Kosong',
            'sub_bagian_pembuat.required' => 'Field Tidak Boleh Kosong',
            'nama_pembuat.required' => 'Field Tidak Boleh Kosong',
            'sub_bagian_pemeriksa.required' => 'Field Tidak Boleh Kosong',
            'nama_pemeriksa.required' => 'Field Tidak Boleh Kosong',
            'bagian_pemeriksa.required' => 'Field Tidak Boleh Kosong',
            'nama_bagian_pemeriksa.required' => 'Field Tidak Boleh Kosong',
            'yang_menyetujui.required' => 'Field Tidak Boleh Kosong',
            'nama_yang_menyetujui.required' => 'Field Tidak Boleh Kosong',
            'is_bank.required' => 'Field Tidak Boleh Kosong',
            'is_bank.in' => 'Field Tidak Boleh Kosong',
            'lebih_dari_5_m.required' => 'Field Tidak Boleh Kosong',
            'lebih_dari_5_m.in' => 'Field Tidak Boleh Kosong',
            'lebih_dari_25_jt.required' => 'Field Tidak Boleh Kosong',
            'lebih_dari_25_jt.in' => 'Field Tidak Boleh Kosong',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 422));
    }
}
