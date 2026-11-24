<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Media $media */
        $media = $this->route('media');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('media', 'title')->ignore($media->id)],
            'type' => ['sometimes', 'required', 'string', Rule::in(['anime', 'drama', 'manga', 'novel'])],
            'genre' => ['sometimes', 'required', 'string', 'max:100'],
            'year' => ['sometimes', 'required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
