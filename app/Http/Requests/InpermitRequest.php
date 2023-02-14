<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class InpermitRequest extends FormRequest
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
            'inpermit_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'product_ids' => 'required|array',
            'product_ids.*' => 'numeric',
            'inpermit_code' => 'nullable|unique:inpermits,inpermit_code,'.$this->id,
            'quantities' => 'required|array',
            'quantities.*' => 'numeric',
            'runIDs.*' => 'max:50',
        ];
    }
}
