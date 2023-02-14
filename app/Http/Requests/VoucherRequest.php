<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class VoucherRequest extends FormRequest
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
            'voucher_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'product_ids' => 'required|array',
            'product_ids.*' => 'numeric',
            'voucher_code' => 'nullable|unique:vouchers,voucher_code,'.$this->id,
            'store_id' => 'numeric|required',
            'voucher_quantity_outs' => 'required|array',
            'voucher_quantity_outs.*' => 'numeric',
        ];
    }
}
