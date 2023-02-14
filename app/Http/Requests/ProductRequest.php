<?php

namespace App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidationRule;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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

    public function messages()
    {
        return [
            'Max_Sell_Price.greater_than_field' => trans('validation.greater_than_field', ['attribute'=> trans('validation.attributes.Max_Sell_Price'), 'attribute2'=> trans('validation.attributes.Min_Sell_Price')]),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'Product_code' => 'required|min:1|max:50|unique:products,product_code,'.$this->id,
            'Product_Name' => 'required|min:4|max:50|unique:products,Product_Name,'.$this->id,
            'Public_Price' => 'required|numeric|min:0|max:1000',
            // 'Min_Sell_Price' => 'required|numeric|min:0|max:9999999',
            // 'Max_Sell_Price' => 'required|numeric|min:0|max:9999999|greater_than_field:Min_Sell_Price',
        ];


    }
}
