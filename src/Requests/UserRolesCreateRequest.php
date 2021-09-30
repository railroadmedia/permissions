<?php

namespace Railroad\Permissions\Requests;


class UserRolesCreateRequest extends FormRequest
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
            'user_id' => 'required|numeric',
            'roles' => 'required|array',
            'roles.*' =>'required|string|max:191',
        ];
    }
}
