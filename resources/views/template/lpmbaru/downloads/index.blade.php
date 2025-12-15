@extends('template.lpmbaru.layouts.app')

@section('title', 'Downloads - ' . \App\Models\SiteSetting::getValue('site_name'))

@push('head')
    <style>
        .download-card {
            transition: all 0.3s ease;
        }

        .download-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
        }

        /* Custom scrollbar for filtes */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="relative bg-secondary py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('{{ asset('images/pattern.svg') }}'); background-size: cover;"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-white mb-4" data-aos="fade-down">Pusat Unduhan</h1>
            <p class="text-gray-300 max-w-2xl mx-auto mb-6" data-aos="fade-up" data-aos-delay="100">
                Akses berbagai dokumen, formulir, dan panduan resmi yang tersedia untuk publik.
            </p>
            <div class="flex justify-center items-center text-gray-300 text-sm md:text-base gap-2" data-aos="fade-up"
                data-aos-delay="200">
                <a href="{{ route('frontend.index') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-primary font-medium">Downloads</span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-gray-50 py-16 min-h-screen">
        <div class="container mx-auto px-4">

            {{-- Search & Filters --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-10 -mt-24 relative z-20 border border-gray-100"
                data-aos="fade-up">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    {{-- Search --}}
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                        <div class="relative">
                            <input type="text" id="search" placeholder="Cari dokumen..."
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select id="category"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all appearance-none cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                        <select id="sort"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all appearance-none cursor-pointer">
                            <option value="created_at:desc">Terbaru</option>
                            <option value="title:asc">Nama (A-Z)</option>
                            <option value="download_count:desc">Populer</option>
                        </select>
                    </div>

                    {{-- View Toggle --}}
                    <div class="md:col-span-2 flex flex-col justify-end">
                        <div class="flex bg-gray-100 p-1 rounded-xl">
                            <button id="view-grid"
                                class="flex-1 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm bg-white text-primary">
                                <i class="fas fa-grid-2 mr-1"></i> Grid
                            </button>
                            <button id="view-list"
                                class="flex-1 py-2.5 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-900">
                                <i class="fas fa-list mr-1"></i> List
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loading State --}}
            <div id="loading" class="hidden py-20 text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-4">
                </div>
                <p class="text-gray-500">Memuat data dokumen...</p>
            </div>

            {{-- Results Container --}}
            <div id="downloads-container">
                {{-- Grid View --}}
                <div id="grid-view" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>
                {{-- List View --}}
                <div id="list-view" class="hidden space-y-4 max-w-4xl mx-auto"></div>
            </div>

            {{-- Empty State --}}
            <div id="no-results" class="hidden text-center py-20">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="far fa-folder-open text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Dokumen Ditemukan</h3>
                <p class="text-gray-500">Coba gunakan kata kunci lain atau ubah filter pencarian Anda.</p>
            </div>

            {{-- Pagination --}}
            <div id="pagination" class="mt-12 flex justify-center"></div>

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
                // Search with debounce
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

                // Filters
                document.getElementById('category').addEventListener('change', (e) => {
                    this.filters.category = e.target.value;
                    this.currentPage = 1;
                    this.loadDownloads();
                });

                document.getElementById('sort').addEventListener('change', (e) => {
                    const [sort, order] = e.target.value.split(':');
                    this.filters.sort = sort;
                    this.filters.order = order;
                    this.currentPage = 1;
                    this.loadDownloads();
                });

                // View Toggles
                document.getElementById('view-grid').addEventListener('click', () => this.switchView('grid'));
                document.getElementById('view-list').addEventListener('click', () => this.switchView('list'));
            }

            switchView(view) {
                this.currentView = view;
                const gridBtn = document.getElementById('view-grid');
                const listBtn = document.getElementById('view-list');
                const gridView = document.getElementById('grid-view');
                const listView = document.getElementById('list-view');

                if (view === 'grid') {
                    gridBtn.className = 'flex-1 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm bg-white text-primary';
                    listBtn.className = 'flex-1 py-2.5 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-900';
                    gridView.classList.remove('hidden');
                    listView.classList.add('hidden');
                } else {
                    listBtn.className = 'flex-1 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm bg-white text-primary';
                    gridBtn.className = 'flex-1 py-2.5 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-900';
                    listView.classList.remove('hidden');
                    gridView.classList.add('hidden');
                }
                this.renderDownloads();
            }

            async loadDownloads() {
                this.showLoading(true);
                try {
                    const params = new URLSearchParams({
                        page: this.currentPage,
                        search: this.filters.search,
                        category: this.filters.category,
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
                const gridElem = document.getElementById('grid-view');
                const listElem = document.getElementById('list-view');
                const noResults = document.getElementById('no-results');

                if (!this.downloads || this.downloads.length === 0) {
                    gridElem.innerHTML = '';
                    listElem.innerHTML = '';
                    noResults.classList.remove('hidden');
                    return;
                }

                noResults.classList.add('hidden');

                if (this.currentView === 'grid') {
                    gridElem.innerHTML = this.downloads.map(d => this.renderGridCard(d)).join('');
                } else {
                    listElem.innerHTML = this.downloads.map(d => this.renderListCard(d)).join('');
                }
            }

            renderGridCard(d) {
                const icon = this.getFileIcon(d.file_name);
                const size = this.formatFileSize(d.file_size);

                return `
                        <div class="download-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-full group hover:border-primary/30">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 ${icon.bg} rounded-xl flex items-center justify-center text-xl ${icon.text}">
                                    <i class="${icon.class}"></i>
                                </div>
                                ${d.password ? '<div class="px-2 py-1 bg-red-50 text-red-600 text-xs font-semibold rounded-lg"><i class="fas fa-lock mr-1"></i>Protected</div>' : ''}
                            </div>

                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary transition-colors">${d.title}</h3>
                            <p class="text-sm text-gray-500 mb-4 line-clamp-3">${d.description || 'Tidak ada deskripsi'}</p>

                            <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                                <div class="text-xs text-gray-400">
                                    <div><i class="fas fa-hdd mr-1"></i> ${size}</div>
                                    <div class="mt-1"><i class="fas fa-download mr-1"></i> ${d.download_count}x</div>
                                </div>
                                <a href="${this.getDownloadUrl(d)}" class="px-4 py-2 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg text-sm font-semibold transition-all">
                                    Download
                                </a>
                            </div>
                        </div>
                    `;
            }

            renderListCard(d) {
                const icon = this.getFileIcon(d.file_name);
                const size = this.formatFileSize(d.file_size);
                const date = this.formatDate(d.created_at);

                return `
                        <div class="download-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-6 group hover:border-primary/30">
                            <div class="w-16 h-16 ${icon.bg} rounded-xl flex items-center justify-center text-2xl ${icon.text} shrink-0">
                                <i class="${icon.class}"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-primary transition-colors">${d.title}</h3>
                                        <div class="flex items-center gap-4 text-sm text-gray-400">
                                            <span><i class="far fa-calendar mr-1"></i> ${date}</span>
                                            <span><i class="fas fa-hdd mr-1"></i> ${size}</span>
                                            <span><i class="fas fa-download mr-1"></i> ${d.download_count}x</span>
                                        </div>
                                    </div>
                                    ${d.password ? '<span class="px-3 py-1 bg-red-50 text-red-600 text-xs font-semibold rounded-lg"><i class="fas fa-lock mr-1"></i></span>' : ''}
                                </div>
                                <p class="text-gray-500 text-sm mt-2 line-clamp-1">${d.description || 'Tidak ada deskripsi'}</p>
                            </div>

                            <a href="${this.getDownloadUrl(d)}" class="px-6 py-3 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-all shadow-md hover:shadow-lg shrink-0">
                                <i class="fas fa-cloud-arrow-down mr-2"></i> Download
                            </a>
                        </div>
                    `;
            }

            getFileIcon(filename) {
                const ext = filename.split('.').pop().toLowerCase();
                const map = {
                    pdf: { class: 'fas fa-file-pdf', bg: 'bg-red-50', text: 'text-red-500' },
                    doc: { class: 'fas fa-file-word', bg: 'bg-blue-50', text: 'text-blue-500' },
                    docx: { class: 'fas fa-file-word', bg: 'bg-blue-50', text: 'text-blue-500' },
                    xls: { class: 'fas fa-file-excel', bg: 'bg-green-50', text: 'text-green-500' },
                    xlsx: { class: 'fas fa-file-excel', bg: 'bg-green-50', text: 'text-green-500' },
                    zip: { class: 'fas fa-file-zipper', bg: 'bg-yellow-50', text: 'text-yellow-500' },
                    rar: { class: 'fas fa-file-zipper', bg: 'bg-yellow-50', text: 'text-yellow-500' },
                };
                return map[ext] || { class: 'fas fa-file-alt', bg: 'bg-gray-100', text: 'text-gray-500' };
            }

            formatFileSize(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('id-ID', {
                    day: 'numeric', month: 'long', year: 'numeric'
                });
            }

            getDownloadUrl(d) {
                return d.password ? `{{ url('/downloads') }}/${d.id}/password` : `{{ url('/downloads') }}/${d.id}`;
            }

            showLoading(show) {
                document.getElementById('loading').classList.toggle('hidden', !show);
                document.getElementById('downloads-container').classList.toggle('hidden', show);
                document.getElementById('pagination').classList.toggle('hidden', show);
            }

            renderPagination() {
                const container = document.getElementById('pagination');
                if (!this.pagination || this.pagination.last_page <= 1) {
                    container.innerHTML = '';
                    return;
                }

                let html = `<div class="flex gap-2">`;

                // Prev
                if (this.pagination.current_page > 1) {
                    html += `<button onclick="downloadsManager.goToPage(${this.pagination.current_page - 1})" class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600"><i class="fas fa-chevron-left"></i></button>`;
                }

                // Numbers (Simplified)
                for (let i = 1; i <= this.pagination.last_page; i++) {
                    if (i === 1 || i === this.pagination.last_page || (i >= this.pagination.current_page - 1 && i <= this.pagination.current_page + 1)) {
                        const active = i === this.pagination.current_page ? 'bg-primary text-white border-primary shadow-md' : 'border-gray-200 hover:bg-gray-50 text-gray-600';
                        html += `<button onclick="downloadsManager.goToPage(${i})" class="w-10 h-10 rounded-lg border ${active} flex items-center justify-center text-sm font-bold transition-all">${i}</button>`;
                    } else if (i === this.pagination.current_page - 2 || i === this.pagination.current_page + 2) {
                        html += `<span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>`;
                    }
                }

                // Next
                if (this.pagination.current_page < this.pagination.last_page) {
                    html += `<button onclick="downloadsManager.goToPage(${this.pagination.current_page + 1})" class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600"><i class="fas fa-chevron-right"></i></button>`;
                }

                html += `</div>`;
                container.innerHTML = html;
            }

            goToPage(page) {
                this.currentPage = page;
                this.loadDownloads();
                document.getElementById('downloads-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            window.downloadsManager = new DownloadsManager();
        });
    </script>
@endpush