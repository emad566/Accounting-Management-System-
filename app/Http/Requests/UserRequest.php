<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|min:4|max:20|unique:users,name,'.$this -> id,
            'email' => 'required|email|min:6|max:100|unique:users,email,'.$this -> id,
            'role_id' => 'nullable|numeric|min:1|max:999999',
       ];
    }
}
