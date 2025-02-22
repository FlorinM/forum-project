<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // You can change this if authorization is needed.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'query' => 'required|string|min:5|max:25', // Ensure the query is a string with at least 5 characters
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'query.required' => 'The search query is required.',
            'query.string' => 'The search query must be a valid string.',
            'query.min' => 'The search query must be at least 5 characters long.',
            'query.max' => 'The search query must not exceed 25 characters.',
        ];
    }
}
