<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChallengeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isDosen();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:challenges,title,' . $this->route('challenge')->id,
            'description' => 'required|string|max:500',
            'content' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'max_participants' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'deadline' => 'required|date|after:start_date',
            'criteria' => 'nullable|string',
        ];
    }
}
