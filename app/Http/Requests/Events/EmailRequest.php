<?php

namespace App\Http\Requests\Events;

use App\EventEmail;
use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return EventEmail::getValidationRules(['header', 'body', 'crew']);
    }
    
    /**
     * Get the validation messages.
     * @return array
     */
    public function messages()
    {
        return EventEmail::getValidationMessages(['header', 'body', 'crew']);
    }
}