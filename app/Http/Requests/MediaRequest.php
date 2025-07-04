<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];

        // File is required for create, optional for update
        if ($this->isMethod('post')) {
            $rules['file'] = 'required|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,mp4,avi,mov,mp3,wav';
        } else {
            $rules['file'] = 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,mp4,avi,mov,mp3,wav';
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.max' => 'Judul media maksimal 255 karakter.',
            'alt_text.max' => 'Alt text maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'file.required' => 'File media wajib diunggah.',
            'file.file' => 'File yang diunggah harus berupa file yang valid.',
            'file.max' => 'Ukuran file maksimal 10MB.',
            'file.mimes' => 'Format file yang diizinkan: JPG, JPEG, PNG, GIF, WEBP, PDF, DOC, DOCX, MP4, AVI, MOV, MP3, WAV.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate title from filename if not provided
        if (!$this->title && $this->hasFile('file')) {
            $filename = $this->file('file')->getClientOriginalName();
            $this->merge([
                'title' => pathinfo($filename, PATHINFO_FILENAME)
            ]);
        }
    }
}
