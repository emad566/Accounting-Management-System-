<?php

namespace App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidationRule;

use Illuminate\Foundation\Http\FormRequest;

class ProductsaleRequest extends FormRequest
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
            'Max_Discount.greater_than_field' => trans('validation.greater_than_field', ['attribute'=> trans('validation.attributes.Max_Discount'), 'attribute2'=> trans('validation.attributes.Min_Discount')]),
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
            'Min_Discount' => 'required|numeric|min:0|max:100',
            'Max_Discount' => 'required|numeric|min:0|max:100|greater_than_field:Min_Discount',
        ];


    }
}
