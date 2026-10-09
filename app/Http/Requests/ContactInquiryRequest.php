<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^(?=.*[0-9])[+0-9().\-\s]{5,40}$/'],
            'message' => ['nullable', 'string', 'max:3000'],
            'website' => ['nullable', 'string', 'max:120'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'name.max' => 'Your name must be 120 characters or fewer.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.regex' => 'Please enter a valid phone number.',
            'message.max' => 'Your message must be 3000 characters or fewer.',
        ];
    }
}
