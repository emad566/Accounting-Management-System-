<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use App\Models\Invoice;

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
        $invoice = Invoice::findOrFail($this->id);
        return [
            'invoice_date' => 'required|date|before_or_equal:'.Carbon::now().'|after_or_equal:'.$invoice->voucher->voucher_date,
            'product_ids' => 'required|array',
            'product_ids.*' => 'numeric',
            'invoice_code' => 'numeric|required|unique:invoices,invoice_code,'.$this->id,
            // 'imgData' => 'required',
            'invoice_quantitys' => 'required|array',
            'invoice_quantitys.*' => 'numeric',
            'client_pay' => 'required',
        ];
    }
}
