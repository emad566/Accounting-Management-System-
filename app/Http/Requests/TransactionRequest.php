<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected $primaryKey = 'StoreID';

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
            // 'client_name' => 'required|min:4|max:50|unique:clients,client_name,'.$this->id,
            // 'state_id' => 'required|numeric|min:1|max:9999999',
            // 'city_id' => 'required|numeric|min:1|max:9999999',
            // 'region_id' => 'nullable|numeric|min:1|max:9999999',
            // 'client_address' => 'nullable|min:4|max:100',
            // 'client_phone' => 'nullable|numeric|min:3|max:99999999999',
            // 'client_manager_phone' => 'nullable|numeric|min:3|max:99999999999',
            // 'client_manager_name' => 'nullable|min:4|max:100',
        ];
    }
}
