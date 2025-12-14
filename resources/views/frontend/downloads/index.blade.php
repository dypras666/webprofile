@extends('layouts.app')

@section('title', 'Downloads')

@section('meta')
    <meta name="description" content="Download berbagai file dan dokumen yang tersedia">
    <meta name="keywords" content="download, file, dokumen">
@endsection

@push('styles')
    <style>
        .download-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .download-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #3b82f6;
        }

        .file-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 20px;
            color: white;
            font-weight: bold;
        }

        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Downloads</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Temukan dan unduh berbagai file dan dokumen yang tersedia
                </p>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari File</label>
                        <div class="relative">
                            <input type="text" id="search" placeholder="Masukkan kata kunci..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select id="category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                        <select id="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Tipe</option>
                            <option value="public">Public</option>
                            <option value="protected">Protected</option>
                        </select>
                    </div>
                </div>

                <!-- Sort & View Options -->
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                        <label class="text-sm font-medium text-gray-700">Urutkan:</label>
                        <select id="sort"
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="created_at:desc">Terbaru</option>
                            <option value="created_at:asc">Terlama</option>
                            <option value="title:asc">Nama A-Z</option>
                            <option value="title:desc">Nama Z-A</option>
                            <option value="download_count:desc">Paling Populer</option>
                            <option value="file_size:desc">Ukuran Terbesar</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Tampilan:</label>
                        <div class="flex bg-gray-100 rounded-lg p-1">
                            <button id="view-grid"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-colors bg-blue-500 text-white">
                                <i class="fas fa-th-large mr-1"></i> Grid
                            </button>
                            <button id="view-list"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-gray-900">
                                <i class="fas fa-list mr-1"></i> List
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading" class="hidden text-center py-12">
                <div class="loading-spinner mx-auto mb-4"></div>
                <p class="text-gray-600">Memuat data...</p>
            </div>

            <!-- Downloads Container -->
            <div id="downloads-container">
                <!-- Grid View -->
                <div id="grid-view" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Downloads will be loaded here -->
                </div>

                <!-- List View -->
                <div id="list-view" class="hidden space-y-4">
                    <!-- Downloads will be loaded here -->
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="mt-12 flex justify-center">
                <!-- Pagination will be loaded here -->
            </div>

            <!-- No Results -->
            <div id="no-results" class="hidden text-center py-12">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-search text-6xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada file ditemukan</h3>
                <p class="text-gray-600">Coba ubah filter atau kata kunci pencarian Anda</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        class DownloadsManager {
            constructor() {
                this.currentPage = 1;
                this.currentView = 'grid';
                this.filters = {
                    search: '',
                    category: '',
                    type: '',
                    sort: 'created_at',
                    order: 'desc'
                };

                this.init();
            }

            init() {
                this.bindEvents();
                this.loadDownloads();
            }

            bindEvents() {
                // Search input
                const searchInput = document.getElementById('search');
                let searchTimeout;
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.filters.search = e.target.value;
                        this.currentPage = 1;
                        this.loadDownloads();
                    }, 500);
                });

                // Filter selects
                ['category', 'type'].forEach(filterId => {
                    document.getElementById(filterId).addEventListener('change', (e) => {
                        this.filters[filterId] = e.target.value;
                        this.currentPage = 1;
                        this.loadDownloads();
                    });
                });

                // Sort select
                document.getElementById('sort').addEventListener('change', (e) => {
                    const [sort, order] = e.target.value.split(':');
                    this.filters.sort = sort;
                    this.filters.order = order;
                    this.currentPage = 1;
                    this.loadDownloads();
                });

                // View toggle
                document.getElementById('view-grid').addEventListener('click', () => this.switchView('grid'));
                document.getElementById('view-list').addEventListener('click', () => this.switchView('list'));
            }

            switchView(view) {
                this.currentView = view;

                // Update buttons
                document.getElementById('view-grid').className = view === 'grid'
                    ? 'px-3 py-1 rounded-md text-sm font-medium transition-colors bg-blue-500 text-white'
                    : 'px-3 py-1 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-gray-900';

                document.getElementById('view-list').className = view === 'list'
                    ? 'px-3 py-1 rounded-md text-sm font-medium transition-colors bg-blue-500 text-white'
                    : 'px-3 py-1 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-gray-900';

                // Show/hide views
                document.getElementById('grid-view').style.display = view === 'grid' ? 'grid' : 'none';
                document.getElementById('list-view').style.display = view === 'list' ? 'block' : 'none';

                this.renderDownloads();
            }

            async loadDownloads() {
                this.showLoading(true);

                try {
                    const params = new URLSearchParams({
                        page: this.currentPage,
                        search: this.filters.search,
                        category: this.filters.category,
                        type: this.filters.type,
                        sort: this.filters.sort,
                        order: this.filters.order,
                        per_page: 12
                    });

                    const response = await fetch(`{{ route('frontend.downloads.json') }}?${params}`);
                    const data = await response.json();

                    this.downloads = data.data;
                    this.pagination = data.pagination;

                    this.renderDownloads();
                    this.renderPagination();

                } catch (error) {
                    console.error('Error loading downloads:', error);
                } finally {
                    this.showLoading(false);
                }
            }

            renderDownloads() {
                const gridView = document.getElementById('grid-view');
                const listView = document.getElementById('list-view');
                const noResults = document.getElementById('no-results');

                if (!this.downloads || this.downloads.length === 0) {
                    gridView.innerHTML = '';
                    listView.innerHTML = '';
                    noResults.classList.remove('hidden');
                    return;
                }

                noResults.classList.add('hidden');

                if (this.currentView === 'grid') {
                    gridView.innerHTML = this.downloads.map(download => this.renderGridCard(download)).join('');
                } else {
                    listView.innerHTML = this.downloads.map(download => this.renderListCard(download)).join('');
                }
            }

            renderGridCard(download) {
                const fileIcon = this.getFileIcon(download.file_name);
                const fileSize = this.formatFileSize(download.file_size);
                const isProtected = download.password ? true : false;

                return `
                    <div class="download-card bg-white rounded-lg p-6 fade-in">
                        <div class="flex items-start justify-between mb-4">
                            <div class="file-icon ${fileIcon.color}">
                                <i class="${fileIcon.icon}"></i>
                            </div>
                            <div class="flex items-center space-x-2">
                                ${!download.is_public ? '<span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">Private</span>' : ''}
                                ${isProtected ? '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full"><i class="fas fa-lock mr-1"></i>Protected</span>' : ''}
                            </div>
                        </div>

                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">${download.title}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">${download.description || 'Tidak ada deskripsi'}</p>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-download mr-1"></i>${download.download_count} downloads</span>
                            <span><i class="fas fa-hdd mr-1"></i>${fileSize}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">${this.formatDate(download.created_at)}</span>
                            <a href="${this.getDownloadUrl(download)}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                    </div>
                `;
            }

            renderListCard(download) {
                const fileIcon = this.getFileIcon(download.file_name);
                const fileSize = this.formatFileSize(download.file_size);
                const isProtected = download.password ? true : false;

                return `
                    <div class="download-card bg-white rounded-lg p-6 fade-in">
                        <div class="flex items-center space-x-4">
                            <div class="file-icon ${fileIcon.color} flex-shrink-0">
                                <i class="${fileIcon.icon}"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 mb-1">${download.title}</h3>
                                        <p class="text-gray-600 text-sm mb-2 line-clamp-2">${download.description || 'Tidak ada deskripsi'}</p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-download mr-1"></i>${download.download_count} downloads</span>
                                            <span><i class="fas fa-hdd mr-1"></i>${fileSize}</span>
                                            <span><i class="fas fa-calendar mr-1"></i>${this.formatDate(download.created_at)}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2 ml-4">
                                        ${!download.is_public ? '<span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">Private</span>' : ''}
                                        ${isProtected ? '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full"><i class="fas fa-lock mr-1"></i>Protected</span>' : ''}
                                        <a href="${this.getDownloadUrl(download)}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                            <i class="fas fa-download mr-2"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            renderPagination() {
                const paginationContainer = document.getElementById('pagination');

                if (!this.pagination || this.pagination.last_page <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }

                let paginationHTML = `
                <div class="flex flex-col items-center">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            `;

                // Helper specifically for icons since FontAwesome is not available
                const icons = {
                    prev: `<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>`,
                    next: `<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>`
                };

                // Previous button
                if (this.pagination.current_page > 1) {
                    paginationHTML += `
                    <button onclick="downloadsManager.goToPage(${this.pagination.current_page - 1})" 
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20">
                        <span class="sr-only">Previous</span>
                        ${icons.prev}
                    </button>
                `;
                } else {
                    paginationHTML += `
                    <button disabled 
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                        <span class="sr-only">Previous</span>
                        ${icons.prev}
                    </button>
                `;
                }

                // Page numbers
                const current = this.pagination.current_page;
                const last = this.pagination.last_page;
                const delta = 2;
                const range = [];
                const rangeWithDots = [];
                let l;

                range.push(1);
                for (let i = current - delta; i <= current + delta; i++) {
                    if (i < last && i > 1) {
                        range.push(i);
                    }
                }
                range.push(last);

                for (let i of range) {
                    if (l) {
                        if (i - l === 2) {
                            rangeWithDots.push(l + 1);
                        } else if (i - l !== 1) {
                            rangeWithDots.push('...');
                        }
                    }
                    rangeWithDots.push(i);
                    l = i;
                }

                for (let page of rangeWithDots) {
                    if (page === '...') {
                        paginationHTML += `
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                            ...
                        </span>
                    `;
                    } else {
                        const isActive = page === this.pagination.current_page;
                        // Add -ml-px to prevent double borders
                        paginationHTML += `
                        <button onclick="downloadsManager.goToPage(${page})" 
                                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ${isActive
                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                            } ${page !== 1 ? '-ml-px' : ''}">
                            ${page}
                        </button>
                    `;
                    }
                }

                // Next button
                if (this.pagination.current_page < this.pagination.last_page) {
                    paginationHTML += `
                    <button onclick="downloadsManager.goToPage(${this.pagination.current_page + 1})" 
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 -ml-px">
                        <span class="sr-only">Next</span>
                        ${icons.next}
                    </button>
                `;
                } else {
                    paginationHTML += `
                    <button disabled 
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed -ml-px">
                        <span class="sr-only">Next</span>
                        ${icons.next}
                    </button>
                `;
                }

                paginationHTML += `
                    </nav>
                    <div class="mt-4 text-sm text-gray-700 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-200">
                        Menampilkan <span class="font-bold text-gray-900">${this.pagination.from}</span> sampai <span class="font-bold text-gray-900">${this.pagination.to}</span> dari <span class="font-bold text-gray-900">${this.pagination.total}</span> file
                    </div>
                </div>
            `;

                paginationContainer.innerHTML = paginationHTML;
            }

            goToPage(page) {
                if (page === this.currentPage) return;
                this.currentPage = page;
                this.loadDownloads();

                // Scroll to top of results
                const container = document.getElementById('downloads-container');
                if (container) {
                    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            showLoading(show) {
                document.getElementById('loading').classList.toggle('hidden', !show);
                document.getElementById('downloads-container').classList.toggle('hidden', show);
                document.getElementById('pagination').classList.toggle('hidden', show);
            }

            getFileIcon(fileName) {
                const extension = fileName.split('.').pop().toLowerCase();

                const iconMap = {
                    pdf: { icon: 'fas fa-file-pdf', color: 'bg-red-500' },
                    doc: { icon: 'fas fa-file-word', color: 'bg-blue-500' },
                    docx: { icon: 'fas fa-file-word', color: 'bg-blue-500' },
                    xls: { icon: 'fas fa-file-excel', color: 'bg-green-500' },
                    xlsx: { icon: 'fas fa-file-excel', color: 'bg-green-500' },
                    ppt: { icon: 'fas fa-file-powerpoint', color: 'bg-orange-500' },
                    pptx: { icon: 'fas fa-file-powerpoint', color: 'bg-orange-500' },
                    zip: { icon: 'fas fa-file-archive', color: 'bg-purple-500' },
                    rar: { icon: 'fas fa-file-archive', color: 'bg-purple-500' },
                    jpg: { icon: 'fas fa-file-image', color: 'bg-pink-500' },
                    jpeg: { icon: 'fas fa-file-image', color: 'bg-pink-500' },
                    png: { icon: 'fas fa-file-image', color: 'bg-pink-500' },
                    gif: { icon: 'fas fa-file-image', color: 'bg-pink-500' },
                    mp4: { icon: 'fas fa-file-video', color: 'bg-indigo-500' },
                    avi: { icon: 'fas fa-file-video', color: 'bg-indigo-500' },
                    mp3: { icon: 'fas fa-file-audio', color: 'bg-yellow-500' },
                    wav: { icon: 'fas fa-file-audio', color: 'bg-yellow-500' },
                };

                return iconMap[extension] || { icon: 'fas fa-file', color: 'bg-gray-500' };
            }

            formatFileSize(bytes) {
                if (!bytes) return '0 B';
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(1024));
                return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
            }

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            getDownloadUrl(download) {
                if (download.password) {
                    return `{{ url('/downloads') }}/${download.id}/password`;
                }
                return `{{ url('/downloads') }}/${download.id}`;
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            window.downloadsManager = new DownloadsManager();
        });
    </script>
@endpush