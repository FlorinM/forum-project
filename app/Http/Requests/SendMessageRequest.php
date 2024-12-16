<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SendMessageRequest extends FormRequest
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
            'receiver_id' => ['required', 'exists:users,id', 'not_in:' . auth()->id()],
            'message' => ['required', 'string'],
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

                $plainText = strip_tags($this->input('message'));  // Strip out HTML tags
                $len = strlen($plainText);

                if (!empty($plainText) && $len < $min) {
                    // Add custom error message
                    $validator->errors()
                    ->add('message', 'The message must be at least ' . $min . ' characters long.');
                }

                if ($len > $max) {
                    $validator->errors()
                    ->add('message', 'The message must be at most ' . $max . ' characters long.');
                }
            },
        ];
    }
}
