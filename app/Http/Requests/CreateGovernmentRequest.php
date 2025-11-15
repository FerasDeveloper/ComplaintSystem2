<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGovernmentRequest extends FormRequest
{
  public function authorize(): bool
  {
    return auth()->check() && auth()->user()->isAdmin();
  }

  public function rules(): array
  {
    return [
      'name'        => 'required|string|max:150',
      'email'       => 'required|email|unique:users,email',
      'phone'       => 'nullable|string|unique:users,phone',
      'password'    => 'required|string|min:8',
      'location'    => 'required|string|max:255',
      'description' => 'nullable|string|max:500',
    ];
  }
}
