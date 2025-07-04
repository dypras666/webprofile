<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
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
        $postId = $this->route('post') ? $this->route('post')->id : null;
        
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('posts', 'slug')->ignore($postId)
            ],
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'type' => 'required|in:page',
            'featured_image' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0'
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'slug.unique' => 'Slug sudah digunakan.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'content.required' => 'Konten wajib diisi.',
            'excerpt.max' => 'Ringkasan maksimal 500 karakter.',
            'type.required' => 'Tipe halaman wajib dipilih.',
            'type.in' => 'Tipe halaman tidak valid.',
            'featured_image.string' => 'Featured image harus berupa string.',
            'featured_image.max' => 'Featured image path maksimal 500 karakter.',
            'meta_title.max' => 'Meta title maksimal 255 karakter.',
            'meta_description.max' => 'Meta description maksimal 500 karakter.',
            'meta_keywords.max' => 'Meta keywords maksimal 255 karakter.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan minimal 0.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert checkbox values to boolean
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
            'type' => 'page' // Force type to be 'page'
        ]);

        // Set published_at if is_published is true and published_at is not set
        if ($this->boolean('is_published') && !$this->filled('published_at')) {
            $this->merge([
                'published_at' => now()
            ]);
        }
    }
}