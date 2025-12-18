<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255|unique:site_settings,key,' . $this->id,
            'label' => 'required|string|max:255',
            'value' => 'nullable',
            'type' => 'required|string|in:text,textarea,number,email,url,password,select,radio,checkbox,boolean,file,image,color,date,datetime,json',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'options' => 'nullable|string', // JSON string for select/radio/checkbox
            'validation_rules' => 'nullable|string|max:255',
            'is_required' => 'boolean',
            'is_public' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
