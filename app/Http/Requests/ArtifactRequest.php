<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArtifactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $artifact = $this->route('artifact');

        return [
            'title' => ['required', 'string', 'max:255', Rule::unique('artifacts')->ignore($artifact)],
            'description' => ['required', 'string', 'min:10'],
            'period' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,bmp', 'max:2048', 'dimensions:max_width=6000,max_height=6000'],
            'remove_image' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'distinct', 'exists:tags,id'],
            'events' => ['nullable', 'array'],
            'events.*' => ['integer', 'distinct', 'exists:historical_events,id'],
        ];
    }
}
