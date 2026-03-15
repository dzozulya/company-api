<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:256',

            'edrpou' => [
                'required',
                'string',
                'max:10',
                'regex:/^[0-9]+$/'
            ],

            'address' => 'required|string'
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required',
            'edrpou.required' => 'EDRPOU code is required',
            'address.required' => 'Address is required'
        ];
    }
}
