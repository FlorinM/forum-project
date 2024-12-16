<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                $message = $this->input('message');
                $plainText = strip_tags($message);  // Strip out HTML tags

                if (!empty($plainText) && strlen($plainText) < 10) {
                    // Add custom error message
                    $validator->errors()->add('message', 'The message must be at least 10 characters long.');
                }
            },
        ];
    }
}
