<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePostRequest extends FormRequest
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
            'content' => ['required', 'string'],
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

                $plainText = strip_tags($this->input('content'));  // Strip out HTML tags
                $len = strlen($plainText);

                if (!empty($plainText) && $len < $min) {
                    // Add custom error message
                    $validator->errors()
                        ->add('content', 'The post must be at least ' . $min . ' characters long.');
                }

                if ($len > $max) {
                    $validator->errors()
                        ->add('content', 'The post must be at most ' . $max . ' characters long.');
                }
            },
        ];
    }
}
