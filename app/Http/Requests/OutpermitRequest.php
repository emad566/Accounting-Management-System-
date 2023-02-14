<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class OutpermitRequest extends FormRequest
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
            'outpermit_date' => 'required|date|before_or_equal:'.Carbon::now(),

            'outpermit_code' => 'required|max:50',
            'product_ids' => 'required|array',
            'product_ids.*' => 'numeric',
            'inpermit_product_ids' => 'required|array',
            'inpermit_product_ids.*' => 'numeric',

            'outpermit_code' => 'nullable|unique:outpermits,outpermit_code,'.$this->id,

            'Quantity_outs' => 'required|array',
            'Quantity_outs.*' => 'numeric|nullable',
        ];
    }
}
