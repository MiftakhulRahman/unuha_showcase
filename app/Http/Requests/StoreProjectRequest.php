<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'kategori_id' => 'required|exists:kategoris,id',
            'title' => 'required|string|max:255|unique:projects',
            'description' => 'required|string|max:500',
            'content' => 'nullable|string',
            'repository_url' => 'nullable|url',
            'demo_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'tool_ids' => 'nullable|array',
            'tool_ids.*' => 'exists:tools,id',
        ];
    }
}
