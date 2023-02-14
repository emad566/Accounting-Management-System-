<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            'Sup_Name' => 'required|min:4|max:80|unique:suppliers,Sup_Name,'.$this->id,
            'Sup_phone' => 'required|min:11|max:11|unique:suppliers,Sup_phone,'.$this->id,
            'Sup_address' => 'required|min:4|max:191',
        ];
    }
}
