<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreThreadRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'content' => ['required', 'string', 'max:500'],
            'postContent' => ['required', 'string'],
        ];
    }

    /**
     * Define custom error messages for the validation rules applied in the form request.
     *
     * @return array<string, string> An associative array of validation rule error messages.
     */
    public function messages()
    {
        return [
            'title.regex' => 'The title may only contain letters, numbers, and spaces.',
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $min = 10;
                $max = 5000;

                $plainText = strip_tags($this->input('postContent'));  // Strip out HTML tags
                $len = strlen($plainText);

                if (!empty($plainText) && $len < $min) {
                    // Add custom error message
                    $validator->errors()
                        ->add('postContent', 'The post must be at least ' . $min . ' characters long.');
                }

                if ($len > $max) {
                    $validator->errors()
                        ->add('postContent', 'The post must be at most ' . $max . ' characters long.');
                }
            },
        ];
    }
}
