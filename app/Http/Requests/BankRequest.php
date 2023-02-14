<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class BankRequest extends FormRequest
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
            'bank_name' => 'required|min:4|max:191|unique:banks,bank_name,'.$this->id,
            'bank_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'create_user_id' => 'required|numeric',
        ];
    }
}
