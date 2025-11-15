<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return auth()->check();
        return true;

    }

    public function rules(): array
    {
        return [
            'otp' => 'required|string|size:6',
        ];
    }
}