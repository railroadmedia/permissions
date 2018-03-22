<?php

namespace Railroad\Permissions\Requests;


class AccessCreateRequest extends FormRequest
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
            'name' => 'required|max:255',
            'slug' =>'required|max:255',
            'description' => 'nullable|max:255',
            'brand' => 'nullable|max:255'
        ];
    }
}