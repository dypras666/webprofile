@props(['name', 'value' => null, 'label' => 'Select Media', 'accept' => 'image/*', 'multiple' => false])

<div class="media-picker-container" x-data="{ 
    ...window.mediaPicker({{ json_encode($value) }}, {{ $multiple ? 'true' : 'false' }}) 
}" x-init="init()">
    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>

    <!-- Hidden input to store selected media ID(s) -->
    <input type="hidden" name="{{ $name }}" x-model="selectedValue">

    <!-- Preview Area -->
    <div class="mb-4" x-show="selectedMedia.length > 0">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <template x-for="media in selectedMedia" :key="media.id">
                <div class="relative group">
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                        <img x-show="media.type === 'image'" :src="media.url" :alt="media.filename"
                            class="w-full h-full object-cover">
                        <div x-show="media.type !== 'image'" class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                        </div>
                    </div>
                    <button type="button" @click="removeMedia(media.id)"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-times"></i>
                    </button>
                    <p class="text-xs text-gray-600 mt-1 truncate" x-text="media.filename"></p>
                </div>
            </template>
        </div>
    </div>

    <!-- Select Button -->
    <button type="button" @click="openMediaPicker()"
        class="w-full border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
        <i class="fas fa-plus text-2xl text-gray-400 mb-2"></i>
        <p class="text-sm text-gray-600">{{ $multiple ? 'Select Media Files' : 'Select Media File' }}</p>
        <p class="text-xs text-gray-500 mt-1">Click to browse media library</p>
    </button>
</div>

<!-- Media Picker Modal -->
<div id="media-picker-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[100]" style="z-index: 9999;"
    onclick="if(event.target === this) { window.currentMediaPicker && window.currentMediaPicker.closeMediaPicker(); }">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">Select Media</h3>
                <div class="flex items-center space-x-4">
                    <!-- Upload Button -->
                    <button type="button"
                        onclick="event.preventDefault(); window.currentMediaPicker && window.currentMediaPicker.openUploadArea()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-upload mr-2"></i>
                        Upload
                    </button>

                    <!-- Search -->
                    <div class="relative">
                        <input type="text" id="modal-search" placeholder="Search media..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Filter -->
                    <select id="modal-type-filter"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="image">Images</option>
                        <option value="video">Videos</option>
                        <option value="document">Documents</option>
                    </select>

                    <button onclick="window.currentMediaPicker && window.currentMediaPicker.closeMediaPicker()"
                        class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Match existing structure -->
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <!-- Ensure this matches the container targeted by scroll listener -->
                <!-- Upload Area (Hidden by default) -->
                <div id="modal-upload-area" class="hidden mb-6">
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-lg font-medium text-gray-900 mb-2">Drop files here or click to upload</p>
                        <p class="text-sm text-gray-500 mb-4">Support for images, videos, documents (Max: 10MB per file)
                        </p>
                        <input type="file" id="modal-file-input" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt"
                            class="hidden">
                        <button type="button"
                            onclick="event.preventDefault(); document.getElementById('modal-file-input').click()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>
                            Select Files
                        </button>
                    </div>

                    <!-- Upload Progress -->
                    <div id="modal-upload-progress" class="mt-4 hidden">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div id="modal-progress-bar"
                                class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%">
                            </div>
                        </div>
                        <p id="modal-upload-status" class="text-sm text-gray-600 mt-2">Uploading...</p>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button
                            onclick="event.preventDefault(); window.currentMediaPicker && window.currentMediaPicker.closeUploadArea()"
                            type="button"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Back to Library
                        </button>
                    </div>
                </div>

                <div id="modal-media-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <!-- Media items will be loaded here -->
                </div>

                <!-- Load More Spinner -->
                <div id="modal-loading-more" class="text-center py-4 hidden">
                    <i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>
                    <span class="ml-2 text-sm text-gray-600">Loading more...</span>
                </div>

                <!-- Main Loading State -->
                <div id="modal-loading" class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900">Loading media...</p>
                </div>

                <!-- Empty State -->
                <div id="modal-empty" class="text-center py-12 hidden">
                    <i class="fas fa-images text-4xl text-gray-400 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900">No media files found</p>
                    <p class="text-sm text-gray-500">Upload your first media file to get started.</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between p-6 border-t bg-gray-50">
                <div>
                    <p class="text-sm text-gray-600" id="selection-count">0 items selected</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button"
                        onclick="window.currentMediaPicker && window.currentMediaPicker.closeMediaPicker()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button"
                        onclick="window.currentMediaPicker && window.currentMediaPicker.confirmSelection()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Select
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Define mediaPicker function immediately
    window.mediaPicker = function (initialValue, isMultiple) {
        return {
            selectedMedia: [],
            selectedValue: '',
            isMultiple: isMultiple,
            tempSelection: new Set(),

            // Pagination & Filter State
            currentPage: 1,
            lastPage: 1,
            currentSearch: '',
            currentType: '',
            isFetching: false,

            init() {
                window.mainMediaPicker = this;

                if (initialValue) {
                    if (isMultiple && Array.isArray(initialValue)) {
                        this.loadInitialMedia(initialValue);
                    } else if (!isMultiple && initialValue) {
                        this.loadInitialMedia([initialValue]);
                    }
                }

                // Setup event delegation for the grid
                this.setupGridEventDelegation();

                // Setup scroll listener for infinite scroll
                this.setupScrollListener();
            },

            setupGridEventDelegation() {
                const grid = document.getElementById('modal-media-grid');
                if (!grid) return;

                grid.addEventListener('click', (e) => {
                    const item = e.target.closest('.media-picker-item');
                    if (item) {
                        const mediaId = item.dataset.id;
                        this.toggleMediaSelection(mediaId, item);
                    }
                });
            },

            setupScrollListener() {
                const container = document.querySelector('.max-h-\\[60vh\\]');
                if (!container) return;

                container.addEventListener('scroll', () => {
                    const { scrollTop, scrollHeight, clientHeight } = container;
                    // Trigger when within 100px of bottom
                    if (scrollTop + clientHeight >= scrollHeight - 100) {
                        this.loadNextPage();
                    }
                });
            },

            async loadInitialMedia(mediaIds) {
                try {
                    const response = await fetch('/admin/media/get-by-ids', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ ids: mediaIds })
                    });

                    const data = await response.json();
                    this.selectedMedia = data.media || [];
                    this.updateSelectedValue();
                } catch (error) {
                    console.error('Error loading initial media:', error);
                }
            },

            externalCallback: null,

            openMediaPicker(callback = null) {
                const modal = document.getElementById('media-picker-modal');
                modal.classList.remove('hidden');
                window.currentMediaPicker = this;

                if (callback && typeof callback === 'function') {
                    this.externalCallback = callback;
                    this.isMultiple = false;
                    this.tempSelection.clear();
                    document.querySelectorAll('.media-picker-item').forEach(item => {
                        item.classList.remove('border-blue-500', 'bg-blue-50');
                        item.querySelector('.absolute')?.remove();
                    });
                    this.updateSelectionCount();
                } else {
                    this.externalCallback = null;
                    this.tempSelection = new Set(this.selectedMedia.map(m => m.id.toString()));
                }

                // Initial load: reset filters and page
                this.loadMediaLibrary('', '', false);
            },

            closeMediaPicker() {
                const modal = document.getElementById('media-picker-modal');
                modal.classList.add('hidden');
                this.tempSelection.clear();
                this.externalCallback = null;
                window.currentMediaPicker = null;
            },

            loadNextPage() {
                if (this.currentPage < this.lastPage) {
                    this.loadMediaLibrary(this.currentSearch, this.currentType, true);
                }
            },

            async loadMediaLibrary(search = null, type = null, append = false) {
                if (this.isFetching) return;

                // Update state based on logic
                if (search !== null) this.currentSearch = search;
                if (type !== null) this.currentType = type;

                if (!append) {
                    this.currentPage = 1;
                    document.getElementById('modal-media-grid').innerHTML = ''; // Clear grid
                    document.getElementById('modal-loading').classList.remove('hidden');
                    document.getElementById('modal-empty').classList.add('hidden');
                } else {
                    this.currentPage++;
                    document.getElementById('modal-loading-more').classList.remove('hidden');
                }

                this.isFetching = true;

                try {
                    const params = new URLSearchParams({
                        search: this.currentSearch,
                        type: this.currentType,
                        per_page: 24, // Reasonable chunk size
                        page: this.currentPage
                    });

                    const response = await fetch(`/admin/media/api?${params}`);
                    const data = await response.json();

                    document.getElementById('modal-loading').classList.add('hidden');
                    document.getElementById('modal-loading-more').classList.add('hidden');

                    if (data.media && data.media.length > 0) {
                        this.lastPage = data.pagination.last_page;
                        this.renderMediaGrid(data.media, append);
                    } else if (!append) {
                        document.getElementById('modal-empty').classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error loading media:', error);
                    document.getElementById('modal-loading').classList.add('hidden');
                    document.getElementById('modal-loading-more').classList.add('hidden');
                    if (!append) document.getElementById('modal-empty').classList.remove('hidden');
                } finally {
                    this.isFetching = false;
                }
            },

            renderMediaGrid(mediaItems, append) {
                const grid = document.getElementById('modal-media-grid');

                const html = mediaItems.map(media => `
                <div class="media-picker-item cursor-pointer border-2 border-transparent rounded-lg overflow-hidden hover:border-blue-300 transition-colors ${this.tempSelection.has(media.id.toString()) ? 'border-blue-500 bg-blue-50' : ''
                    }" data-id="${media.id}">
                    <div class="aspect-square bg-gray-100 relative">
                        ${media.type === 'image' ?
                        `<img src="${media.url}" alt="${media.filename}" class="w-full h-full object-cover">` :
                        `<div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-file-${media.type === 'pdf' ? 'pdf' : 'alt'} text-3xl text-gray-400"></i>
                            </div>`
                    }
                    ${this.tempSelection.has(media.id.toString()) ?
                        '<div class="absolute top-2 right-2 bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center"><i class="fas fa-check text-xs"></i></div>' : ''
                    }
                    </div>
                    <div class="p-2">
                        <p class="text-xs font-medium text-gray-900 truncate" title="${media.filename}">${media.filename}</p>
                        <p class="text-xs text-gray-500">${media.type.toUpperCase()}</p>
                    </div>
                </div>
            `).join('');

                if (append) {
                    grid.insertAdjacentHTML('beforeend', html);
                } else {
                    grid.innerHTML = html;
                }

                this.updateSelectionCount();
            },

            toggleMediaSelection(mediaId, element) {
                const imgContainer = element.querySelector('.aspect-square');

                // Helper to update UI
                const updateUI = (isSelected) => {
                    if (isSelected) {
                        element.classList.add('border-blue-500', 'bg-blue-50');
                        if (!imgContainer.querySelector('.fa-check')) {
                            imgContainer.insertAdjacentHTML('beforeend', '<div class="absolute top-2 right-2 bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center"><i class="fas fa-check text-xs"></i></div>');
                        }
                    } else {
                        element.classList.remove('border-blue-500', 'bg-blue-50');
                        const checkmark = imgContainer.querySelector('.absolute.top-2.right-2');
                        if (checkmark) checkmark.remove();
                    }
                };

                if (this.isMultiple) {
                    if (this.tempSelection.has(mediaId)) {
                        this.tempSelection.delete(mediaId);
                        updateUI(false);
                    } else {
                        this.tempSelection.add(mediaId);
                        updateUI(true);
                    }
                } else {
                    // Single selection
                    // Deselect all others visually
                    document.querySelectorAll('.media-picker-item').forEach(item => {
                        const iContainer = item.querySelector('.aspect-square');
                        item.classList.remove('border-blue-500', 'bg-blue-50');
                        const existingCheck = iContainer.querySelector('.absolute.top-2.right-2');
                        if (existingCheck) existingCheck.remove();
                    });

                    this.tempSelection.clear();
                    this.tempSelection.add(mediaId);
                    updateUI(true);
                }

                this.updateSelectionCount();
            },

            updateSelectionCount() {
                const count = this.tempSelection.size;
                document.getElementById('selection-count').textContent = `${count} item${count !== 1 ? 's' : ''} selected`;
            },

            async confirmSelection() {
                if (this.tempSelection.size === 0) {
                    this.closeMediaPicker();
                    return;
                }

                try {
                    const response = await fetch('/admin/media/get-by-ids', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ ids: Array.from(this.tempSelection) })
                    });

                    const data = await response.json();

                    if (this.externalCallback) {
                        this.externalCallback(data.media);
                        this.closeMediaPicker();
                        return;
                    }

                    this.selectedMedia = data.media || [];
                    this.updateSelectedValue();
                    this.closeMediaPicker();
                } catch (error) {
                    console.error('Error confirming selection:', error);
                }
            },

            removeMedia(mediaId) {
                this.selectedMedia = this.selectedMedia.filter(media => media.id !== mediaId);
                this.updateSelectedValue();
            },

            updateSelectedValue() {
                if (this.isMultiple) {
                    this.selectedValue = JSON.stringify(this.selectedMedia.map(media => media.id));
                } else {
                    this.selectedValue = this.selectedMedia.length > 0 ? this.selectedMedia[0].id : '';
                }
            },

            openUploadArea() {
                const uploadArea = document.getElementById('modal-upload-area');
                const mediaGrid = document.getElementById('modal-media-grid');
                const loading = document.getElementById('modal-loading');
                const empty = document.getElementById('modal-empty');
                const loadMore = document.getElementById('modal-loading-more');

                uploadArea.classList.remove('hidden');
                mediaGrid.classList.add('hidden');
                loading.classList.add('hidden');
                empty.classList.add('hidden');
                if (loadMore) loadMore.classList.add('hidden');

                this.setupUploadHandlers();
            },

            closeUploadArea() {
                const uploadArea = document.getElementById('modal-upload-area');
                const mediaGrid = document.getElementById('modal-media-grid');

                uploadArea.classList.add('hidden');
                mediaGrid.classList.remove('hidden');

                this.loadMediaLibrary(null, null, false); // Reload from scratch
            },

            setupUploadHandlers() {
                const fileInput = document.getElementById('modal-file-input');
                const uploadArea = document.querySelector('#modal-upload-area .border-dashed');

                fileInput.addEventListener('change', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.handleModalFileUpload(e);
                });

                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    uploadArea.classList.add('border-blue-500', 'bg-blue-50');
                });

                uploadArea.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
                    const files = e.dataTransfer.files;
                    this.handleModalFileUpload({ target: { files } });
                });
            },

            async handleModalFileUpload(event) {
                const files = Array.from(event.target.files);
                if (files.length === 0) return;

                const formData = new FormData();
                files.forEach(file => formData.append('files[]', file));

                const progressContainer = document.getElementById('modal-upload-progress');
                const progressBar = document.getElementById('modal-progress-bar');
                const statusText = document.getElementById('modal-upload-status');

                progressContainer.classList.remove('hidden');
                progressBar.style.width = '0%';
                statusText.textContent = 'Uploading...';

                try {
                    const xhr = new XMLHttpRequest();

                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            progressBar.style.width = percentComplete + '%';
                            statusText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                        }
                    });

                    xhr.addEventListener('load', () => {
                        if (xhr.status === 200) {
                            progressBar.style.width = '100%';
                            statusText.textContent = 'Upload complete!';

                            setTimeout(() => {
                                progressContainer.classList.add('hidden');
                                document.getElementById('modal-file-input').value = '';
                                statusText.textContent = 'Files uploaded successfully! Click "Back to Library" to see them.';
                            }, 1000);
                        } else {
                            throw new Error('Upload failed');
                        }
                    });

                    xhr.addEventListener('error', () => {
                        throw new Error('Upload failed');
                    });

                    xhr.open('POST', '/admin/media/upload');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    xhr.send(formData);

                } catch (error) {
                    console.error('Upload error:', error);
                    progressContainer.classList.add('hidden');
                    statusText.textContent = 'Upload failed. Please try again.';
                    statusText.classList.add('text-red-600');
                }
            }
        };
    };

    // Initialize search and filter handlers
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('modal-search');
        const typeFilter = document.getElementById('modal-type-filter');

        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    // Trigger search in active media picker
                    const activeModal = document.querySelector('#media-picker-modal:not(.hidden)');
                    if (activeModal && window.currentMediaPicker) {
                        window.currentMediaPicker.loadMediaLibrary(this.value, null, false);
                    }
                }, 300);
            });
        }

        if (typeFilter) {
            typeFilter.addEventListener('change', function () {
                const activeModal = document.querySelector('#media-picker-modal:not(.hidden)');
                if (activeModal && window.currentMediaPicker) {
                    window.currentMediaPicker.loadMediaLibrary(null, this.value, false);
                }
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Move modal to body to avoid z-index/stacking context issues
        const modal = document.getElementById('media-picker-modal');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
</script>