<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'content' => 'required|string|min:10',
            'author' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The post title is required.',
            'title.max' => 'The post title cannot exceed 255 characters.',
            'content.required' => 'The post content is required.',
            'content.min' => 'The post content must be at least 10 characters.',
            'status.required' => 'Please select a status for the post.',
            'status.in' => 'The selected status is invalid.',
            'category_id.exists' => 'The selected category does not exist.',
        ];
    }
}
