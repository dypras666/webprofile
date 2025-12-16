@extends('layouts.admin')

@section('title', isset($member) ? 'Edit Team Member' : 'Add Team Member')
@section('page-title', isset($member) ? 'Edit Team Member' : 'Add Team Member')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ isset($member) ? route('admin.team.update', $member->id) : route('admin.team.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($member))
                @method('PUT')
            @endif

            <div class="space-y-6">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $member->name ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="e.g. Dr. John Doe">
                </div>

                {{-- Position --}}
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700">Position / Jabatan</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $member->position ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="e.g. Rector, Lecturer">
                </div>

                {{-- Image --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photo</label>
                    @if(isset($member) && $member->image)
                        <div class="mt-2 mb-2">
                            <img src="{{ $member->image_url }}" alt="Current Photo" class="h-24 w-24 object-cover rounded-full border border-gray-200">
                        </div>
                    @endif
                    <x-media-picker
                        name="image"
                        :value="$member->image ?? null"
                        label="Select Photo"
                        accept="image/*"
                        :multiple="false"
                    />
                    <p class="mt-1 text-sm text-gray-500">Recommended size: Square (500x500px).</p>
                </div>

                {{-- Status --}}
                <div class="flex items-center">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" id="status" value="1"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        {{ old('status', $member->status ?? true) ? 'checked' : '' }}>
                    <label for="status" class="ml-2 block text-sm text-gray-900">
                        Enable to display on website
                    </label>
                </div>

                <hr class="border-gray-100">

                {{-- Social Media Links --}}
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-gray-700">Social Media Links</label>
                        <button type="button" onclick="addSocialLink()"
                            class="text-sm bg-blue-50 text-blue-700 px-3 py-1 rounded-full hover:bg-blue-100 transition-colors font-semibold border border-blue-200">
                            + Add Link
                        </button>
                    </div>
                    <div class="space-y-3" id="social-links-container">
                        {{-- Container for dynamic fields --}}
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Custom Fields --}}
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-gray-700">Custom Fields (e.g. NIDN)</label>
                        <button type="button" onclick="addCustomField()"
                            class="text-sm bg-purple-50 text-purple-700 px-3 py-1 rounded-full hover:bg-purple-100 transition-colors font-semibold border border-purple-200">
                            + Add Field
                        </button>
                    </div>
                    <div class="space-y-3" id="custom-fields-container">
                        {{-- Container for dynamic fields --}}
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.team.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ isset($member) ? 'Update Member' : 'Create Member' }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Templates for JS --}}
    <template id="social-link-template">
        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border border-gray-200 relative">
            <div class="flex-1 grid grid-cols-2 gap-4">
                <div>
                    <select name="social_links[INDEX][platform]"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="facebook">Facebook</option>
                        <option value="twitter">Twitter / X</option>
                        <option value="instagram">Instagram</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="youtube">YouTube</option>
                        <option value="website">Website</option>
                        <option value="email">Email</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                </div>
                <div>
                    <input type="text" name="social_links[INDEX][url]"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                        placeholder="https://...">
                </div>
            </div>
            <button type="button" onclick="this.closest('.relative').remove()"
                class="text-gray-400 hover:text-red-500 transition-colors p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>

    <template id="custom-field-template">
        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border border-gray-200 relative">
            <div class="flex-1 grid grid-cols-2 gap-4">
                <div>
                    <input type="text" name="custom_fields[INDEX][label]"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                        placeholder="Label (e.g. NIDN)">
                </div>
                <div>
                    <input type="text" name="custom_fields[INDEX][value]"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                        placeholder="Value">
                </div>
            </div>
            <button type="button" onclick="this.closest('.relative').remove()"
                class="text-gray-400 hover:text-red-500 transition-colors p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>

@endsection

@push('scripts')
    <script>
        let socialIndex = 1000;
        let customIndex = 1000;

        function addSocialLink(data = null) {
            const template = document.getElementById('social-link-template');
            const container = document.getElementById('social-links-container');
            const clone = template.content.cloneNode(true);

            // Update name attributes with index
            const index = data ? data.index : socialIndex++;

            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', index);
                if (data) {
                    if (el.name.includes('[platform]')) el.value = data.platform;
                    if (el.name.includes('[url]')) el.value = data.url;
                }
            });

            container.appendChild(clone);
        }

        function addCustomField(data = null) {
            const template = document.getElementById('custom-field-template');
            const container = document.getElementById('custom-fields-container');
            const clone = template.content.cloneNode(true);

            const index = data ? data.index : customIndex++;

            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', index);
                if (data) {
                    if (el.name.includes('[label]')) el.value = data.label;
                    if (el.name.includes('[value]')) el.value = data.value;
                }
            });

            container.appendChild(clone);
        }

        // Load existing data if edit mode
        @if(isset($member))
            const socialLinks = @json($member->social_links ?? []);
            const customFields = @json($member->custom_fields ?? []);

            if (Array.isArray(socialLinks)) {
                socialLinks.forEach((link, i) => addSocialLink({ ...link, index: i }));
            } else if (typeof socialLinks === 'object') {
                // Handle object case if cast fails or different format
                Object.values(socialLinks).forEach((link, i) => addSocialLink({ ...link, index: i }));
            }

            if (Array.isArray(customFields)) {
                customFields.forEach((field, i) => addCustomField({ ...field, index: i }));
            } else if (typeof customFields === 'object') {
                Object.values(customFields).forEach((field, i) => addCustomField({ ...field, index: i }));
            }
        @endif
    </script>
@endpush