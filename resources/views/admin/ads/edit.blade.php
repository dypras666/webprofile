@extends('layouts.admin')

@section('title', 'Edit Ad')
@section('page-title', 'Edit Ad')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title (Internal Name)</label>
                    <input type="text" name="title" id="title" required value="{{ old('title', $ad->title) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <!-- Position -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Position</label>
                    <select name="category_id" id="category_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select Position</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $ad->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Target URL -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700">Target URL</label>
                    <input type="url" name="excerpt" id="excerpt" placeholder="https://example.com" value="{{ old('excerpt', $ad->excerpt) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                    @if($ad->featured_image)
                        <div class="mb-4">
                            <img src="{{ Storage::url($ad->featured_image) }}" alt="Current Ad Image" class="h-24 rounded border border-gray-200">
                        </div>
                    @else
                        <div class="mb-4 text-sm text-gray-500 italic">No image uploaded</div>
                    @endif

                    <label for="featured_image" class="block text-sm font-medium text-gray-700">Change Image</label>
                    <input type="file" name="featured_image" id="featured_image"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <!-- Active Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="is_published" value="1" {{ $ad->is_published ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_published" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.ads.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Ad
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection