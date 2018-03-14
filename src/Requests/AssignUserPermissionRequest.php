<?php

namespace Railroad\Permissions\Requests;


use Railroad\Permissions\Services\ConfigService;

class AssignUserPermissionRequest extends FormRequest
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
            'user_id' => 'required|exists:'.ConfigService::$tableUser.',id',
            'permission_id' =>'required|exists:'.ConfigService::$tablePermissions.',id'
        ];
    }
}