<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ReturnRequest extends FormRequest
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
            'return_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'return_code' => 'nullable|unique:returns,return_code,'.$this->id,
            'return_details' => 'nullable|max:191',
        ];
    }
}
