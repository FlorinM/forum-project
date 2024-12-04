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
            'content' => ['string'],
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
                $content = $this->input('content');
                $plainText = strip_tags($content);  // Strip out HTML tags

                if (!empty($plainText) && strlen($plainText) < 10) {
                    // Add custom error message
                    $validator->errors()->add('content', 'The post must be at least 10 characters long.');
                }
            },
        ];
    }
}
