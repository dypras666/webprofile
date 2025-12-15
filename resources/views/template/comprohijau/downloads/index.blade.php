@extends('template.comprohijau.layouts.app')

@section('title', 'Download Area - ' . \App\Models\SiteSetting::getValue('site_name'))

@section('content')

    {{-- Page Header --}}
    <div class="relative bg-secondary py-16 lg:py-24 overflow-hidden">
        {{-- Abstract Pattern --}}
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-bold font-heading text-white mb-4">Dokumen & Download</h1>
            {{-- Breadcrumb --}}
            <nav
                class="text-sm text-gray-500 mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap justify-center">
                <a href="{{ route('frontend.index') }}"
                    class="hover:text-primary text-green-100 hover:text-white transition-colors">Beranda</a>
                <span class="text-green-500">/</span>
                <span class="font-medium text-white">Download</span>
            </nav>
        </div>
    </div>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-10">

                {{-- Main Content --}}
                <div class="w-full lg:w-3/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div
                            class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h2 class="text-xl font-bold text-gray-900">Daftar Dokumen</h2>

                            {{-- Search/Filter Form --}}
                            <form action="{{ route('frontend.downloads') }}" method="GET" class="flex gap-2">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari dokumen..."
                                    class="px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:ring-1 focus:ring-primary focus:border-primary text-sm outline-none">
                                <button type="submit"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead
                                    class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider font-semibold border-b border-gray-100">
                                    <tr>
                                        <th class="p-4">Nama Dokumen</th>
                                        <th class="p-4 hidden md:table-cell">Kategori</th>
                                        <th class="p-4 hidden sm:table-cell">Tanggal</th>
                                        <th class="p-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($downloads as $download)
                                        <tr class="hover:bg-green-50/50 transition-colors group">
                                            <td class="p-4">
                                                <div class="flex items-start gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded bg-red-100 text-red-500 flex items-center justify-center shrink-0 mt-1">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </div>
                                                    <div>
                                                        <h4
                                                            class="text-gray-900 font-medium group-hover:text-primary transition-colors">
                                                            {{ $download->title }}
                                                        </h4>
                                                        <p class="text-xs text-gray-500 mt-1 sm:hidden">
                                                            {{ $download->created_at->format('d M Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4 hidden md:table-cell">
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                                                    {{ $download->category->name ?? 'Umum' }}
                                                </span>
                                            </td>
                                            <td class="p-4 text-sm text-gray-500 hidden sm:table-cell">
                                                {{ $download->created_at->format('d M Y') }}
                                            </td>
                                            <td class="p-4 text-center">
                                                <a href="{{ route('frontend.downloads.show', $download) }}"
                                                    class="inline-flex items-center justify-center px-4 py-2 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg transition-colors text-sm font-medium">
                                                    <i class="fas fa-download mr-2"></i> Unduh
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-12 text-center text-gray-500">
                                                <i class="far fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                                <p>Belum ada dokumen yang tersedia.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-6 border-t border-gray-100">
                            {{ $downloads->links() }}
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <aside class="w-full lg:w-1/4 space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4 pb-2 border-b">Kategori</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('frontend.downloads') }}"
                                    class="block px-3 py-2 rounded text-sm {{ !request('category') ? 'bg-primary text-white font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                    Semua Kategori
                                </a>
                            </li>
                            @foreach($categories as $cat)
                                <li>
                                    <a href="{{ route('frontend.downloads', ['category' => $cat->slug]) }}"
                                        class="block px-3 py-2 rounded text-sm {{ request('category') == $cat->slug ? 'bg-primary text-white font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                        {{ $cat->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

            </div>
        </div>
    </section>

@endsection