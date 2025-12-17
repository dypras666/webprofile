@extends('layouts.admin')

@section('title', 'Theme Customization')
@section('page-title', 'Customize Theme: ' . ucfirst($template))

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Customize {{ ucfirst($template) }} Theme</h2>
                <p class="text-gray-500 text-sm mt-1">Override default theme settings. Leave blank to use defaults.</p>
            </div>
            <a href="{{ route('admin.settings.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i> Back to Settings
            </a>
        </div>

        <form action="{{ route('admin.settings.theme.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                @foreach($flatConfig as $key => $default)
                    @php
                        $currentValue = $dbValues[$key] ?? $default;
                        $label = ucwords(str_replace(['.', '_'], ' ', $key));
                        // Custom label mapping for better readability
                        $customLabels = [
                            'hero_title' => 'Hero Section Title',
                            'hero_subtitle' => 'Hero Section Subtitle',
                            'cta_text' => 'Call to Action Text',
                            'cta_url' => 'Call to Action URL',
                            // Add more mapings as needed for specific theme keys
                        ];
                        if (array_key_exists($key, $customLabels)) {
                            $label = $customLabels[$key];
                        }

                        $isTextarea = strlen($default) > 50 || str_contains($key, 'description') || str_contains($key, 'text');
                        $isImage = str_contains($key, 'image') || str_contains($key, 'background') || str_contains($key, 'logo') || str_contains($key, 'banner_image');
                    @endphp

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $label }}
                        </label>

                        <div class="text-xs text-gray-400 mb-2 font-mono">Key: {{ $key }}</div>

                        @if($isImage)
                            <div class="space-y-2">
                                @if($currentValue && $currentValue !== $default && !str_starts_with($currentValue, 'http'))
                                    {{-- Check if it looks like a stored path --}}
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($currentValue) }}" alt="Preview"
                                            class="h-20 w-auto object-cover rounded border border-gray-300">
                                    </div>
                                @endif
                                <input type="file" name="{{ $key }}" id="{{ $key }}" class="w-full text-sm text-gray-500
                                                            file:mr-4 file:py-2 file:px-4
                                                            file:rounded-full file:border-0
                                                            file:text-sm file:font-semibold
                                                            file:bg-blue-50 file:text-blue-700
                                                            hover:file:bg-blue-100">

                                @if($currentValue)
                                    <p class="text-xs text-gray-500">Current: {{ basename($currentValue) }}</p>
                                @endif
                            </div>
                        @elseif($isTextarea)
                            <textarea name="{{ $key }}" id="{{ $key }}" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ $currentValue }}</textarea>
                        @else
                            <input type="text" name="{{ $key }}" id="{{ $key }}" value="{{ $currentValue }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @endif

                        <p class="mt-1 text-xs text-gray-400">Default: {{ Str::limit($default, 50) }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-colors">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection