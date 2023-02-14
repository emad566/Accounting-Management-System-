<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class getRequest extends FormRequest
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
            'get_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'get_code' => 'nullable|unique:gets,get_code,'.$this->id,
            'client_pay' => 'required|min:0',
        ];
    }
}
