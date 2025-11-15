<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComplaintRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return auth()->check();
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'title' => 'required|string|max:255',
      'location' => 'required|string|max:255',
      'description' => 'required|string|max:2000',
      'government_id' => 'required|exists:governments,id',
      'status' => 'required|string|in:waiting,pending,resolved,rejected',
      'type_id' => 'required|exists:complaint_types,id',
      'attachments.*' => 'nullable|file|mimes:png,jpg,jpeg,mp4,mov,pdf,csv|max:10240'
    ];
  }
}
