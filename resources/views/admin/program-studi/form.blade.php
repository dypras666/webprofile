@extends('layouts.admin')

@section('title', isset($programStudi) ? 'Edit Program Studi' : 'Create Program Studi')
@section('page-title', 'Program Studi')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Sidebar: Logo/Image -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 bg-blue-500 text-white p-2 rounded">Logo Prodi</h3>

                <div class="text-center">
                    @if(isset($programStudi) && $programStudi->image)
                        <img id="image-preview" src="{{ Storage::url($programStudi->image) }}" alt="Preview"
                            class="mx-auto h-32 w-auto object-contain mb-4">
                    @else
                        <div id="image-placeholder"
                            class="mx-auto h-32 w-32 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                            <i class="fas fa-image text-4xl"></i>
                        </div>
                        <img id="image-preview" src="#" alt="Preview" class="hidden mx-auto h-32 w-auto object-contain mb-4">
                    @endif

                    <p class="text-sm text-gray-500 mb-2">Upload Logo/Image</p>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="md:col-span-3">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-500 rounded-t-lg">
                    <h2 class="text-lg font-medium text-white">
                        {{ isset($programStudi) ? 'Update Prodi' : 'Create New Prodi' }}
                    </h2>
                </div>

                <form
                    action="{{ isset($programStudi) ? route('admin.prodi.update', $programStudi) : route('admin.prodi.store') }}"
                    method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    @if(isset($programStudi))
                        @method('PUT')
                    @endif

                    <!-- Hidden Image Input linked to Sidebar -->
                    <input type="file" id="image" name="image" accept="image/*" class="hidden"
                        onchange="previewImage(this)">
                    <div class="flex justify-end mb-4">
                        <label for="image"
                            class="cursor-pointer bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-medium">
                            Select Logo File...
                        </label>
                    </div>

                    <!-- Row 1 -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">KA PRODI</label>
                            <select name="program_head_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">Pilih Dosen</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}" {{ (old('program_head_id', $programStudi->program_head_id ?? '') == $dosen->id) ? 'selected' : '' }}>
                                        {{ $dosen->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">FAKULTAS</label>
                            <input type="text" name="faculty" value="{{ old('faculty', $programStudi->faculty ?? '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jenjang</label>
                            <select name="degree"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="S1" {{ (old('degree', $programStudi->degree ?? '') == 'S1') ? 'selected' : '' }}>S1</option>
                                <option value="D3" {{ (old('degree', $programStudi->degree ?? '') == 'D3') ? 'selected' : '' }}>D3</option>
                                <option value="S2" {{ (old('degree', $programStudi->degree ?? '') == 'S2') ? 'selected' : '' }}>S2</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">UUID (Feeder Dikti)</label>
                            <input type="text" name="uuid" value="{{ old('uuid', $programStudi->uuid ?? '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                placeholder="Manual UUID for Feeder">
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Prodi</label>
                            <input type="text" name="name" value="{{ old('name', $programStudi->name ?? '') }}" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kode Prodi</label>
                            <input type="text" name="code" value="{{ old('code', $programStudi->code ?? '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Akreditasi</label>
                            <input type="text" name="accreditation"
                                value="{{ old('accreditation', $programStudi->accreditation ?? '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order"
                                value="{{ old('sort_order', $programStudi->sort_order ?? 0) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Berdiri</label>
                            <input type="date" name="establishment_date"
                                value="{{ old('establishment_date', isset($programStudi->establishment_date) ? $programStudi->establishment_date->format('Y-m-d') : '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">SK Penyelenggara</label>
                            <input type="text" name="decree_number"
                                value="{{ old('decree_number', $programStudi->decree_number ?? '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal SK</label>
                            <input type="date" name="decree_date"
                                value="{{ old('decree_date', isset($programStudi->decree_date) ? $programStudi->decree_date->format('Y-m-d') : '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                    </div>

                    <!-- Row 4 -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $programStudi->phone ?? '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $programStudi->email ?? '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Website</label>
                            <input type="url" name="website_url"
                                value="{{ old('website_url', $programStudi->website_url ?? '') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Alamat</label>
                        <textarea name="address"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 summernote">{{ old('address', $programStudi->address ?? '') }}</textarea>
                    </div>

                    <!-- Rich Text Editors -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Keterangan Prodi
                                (Sejarah/Deskripsi)</label>
                            <textarea id="description" name="description"
                                class="summernote">{{ old('description', $programStudi->description ?? '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Visi</label>
                                <textarea id="vision" name="vision"
                                    class="summernote">{{ old('vision', $programStudi->vision ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Misi</label>
                                <textarea id="mission" name="mission"
                                    class="summernote">{{ old('mission', $programStudi->mission ?? '') }}</textarea>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kompetensi Prodi</label>
                            <textarea id="competence" name="competence"
                                class="summernote">{{ old('competence', $programStudi->competence ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end gap-3">
                        <a href="{{ route('admin.prodi.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save
                            Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor .note-toolbar {
        background: #f3f4f6;
        border-bottom: 1px solid #e5e7eb;
    }
    .note-editor.note-frame {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
            $(document).ready(function () {
                $('.summernote').summernote({
                    height: 200,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            });

            function previewImage(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#image-preview').attr('src', e.target.result).removeClass('hidden');
                        $('#image-placeholder').addClass('hidden');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
    </script>
@endpush