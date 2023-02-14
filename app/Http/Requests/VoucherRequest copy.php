<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class InvoiceRequest extends FormRequest
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
            // 'transfer_date' => 'required|date|before_or_equal:'.Carbon::now(),
            // 'product_ids' => 'required|array',
            // 'product_ids.*' => 'numeric',
            // 'transfer_code' => 'numeric|required|unique:transfers,transfer_code,'.$this->id,
            // 'from_store_id' => 'numeric|required',
            // 'to_store_id' => 'numeric|required',
            // 'quantities' => 'required|array',
            // 'quantities.*' => 'numeric',
        ];
    }
}
