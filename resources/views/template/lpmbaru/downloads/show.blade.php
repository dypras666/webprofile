@extends('template.lpmbaru.layouts.app')

@section('title', $download->title . ' - Download Area')

@section('content')
    {{-- Page Header --}}
    <div class="relative bg-secondary py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('{{ asset('images/pattern.svg') }}'); background-size: cover;"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-white mb-4" data-aos="fade-down">Detail Dokumen</h1>
            <div class="flex justify-center items-center text-gray-300 text-sm md:text-base gap-2" data-aos="fade-up"
                data-aos-delay="100">
                <a href="{{ route('frontend.index') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('frontend.downloads') }}" class="hover:text-primary transition-colors">Downloads</a>
                <span>/</span>
                <span class="text-primary font-medium truncate max-w-[200px]">{{ $download->title }}</span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <section class="py-16 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                {{-- File Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-12" data-aos="fade-up">
                    <div class="bg-gradient-to-r from-secondary to-gray-800 p-8 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full -mr-32 -mt-32 blur-3xl">
                        </div>
                        <div class="relative z-10">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-semibold mb-4 backdrop-blur-sm">
                                <i class="far fa-folder-open text-primary"></i>
                                <span>{{ $download->category_name ?? 'Umum' }}</span>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-bold mb-6 leading-tight">{{ $download->title }}</h1>

                            <div class="flex flex-wrap items-center gap-6 text-sm text-gray-300">
                                <div class="flex items-center gap-2">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>{{ ($download->published_at ?? $download->created_at)->format('d F Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-download"></i>
                                    <span>{{ $download->download_count }} Unduhan</span>
                                </div>
                                @if($download->is_protected)
                                    <div class="flex items-center gap-2 text-primary font-bold">
                                        <i class="fas fa-lock"></i>
                                        <span>Terlindungi</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-8 md:p-10">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                            {{-- Info Sidebar --}}
                            <div
                                class="md:col-span-1 border-b md:border-b-0 md:border-r border-gray-100 pb-8 md:pb-0 md:pr-8">
                                <h3 class="font-bold text-gray-900 mb-6">Informasi File</h3>
                                <div class="space-y-4">
                                    <div>
                                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Tipe
                                            File</span>
                                        <div class="flex items-center gap-2 font-semibold text-gray-700">
                                            @php
                                                $ext = strtolower(pathinfo($download->file_name, PATHINFO_EXTENSION));
                                                $icon = match ($ext) {
                                                    'pdf' => 'fa-file-pdf text-red-500',
                                                    'doc', 'docx' => 'fa-file-word text-blue-500',
                                                    'xls', 'xlsx' => 'fa-file-excel text-green-500',
                                                    'zip', 'rar' => 'fa-file-archive text-yellow-500',
                                                    default => 'fa-file-alt text-gray-400'
                                                };
                                            @endphp
                                            <i class="fas {{ $icon }} text-xl"></i>
                                            <span class="uppercase">{{ $ext }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        <span
                                            class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Ukuran</span>
                                        <span
                                            class="font-semibold text-gray-700">{{ $download->formatted_file_size }}</span>
                                    </div>

                                    <div>
                                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Diupload
                                            Oleh</span>
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                                {{ substr($download->user->name ?? 'A', 0, 1) }}
                                            </div>
                                            <span
                                                class="font-medium text-gray-700 text-sm">{{ $download->user->name ?? 'Admin' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Description & Action --}}
                            <div class="md:col-span-2">
                                <h3 class="font-bold text-gray-900 mb-4">Deskripsi</h3>
                                <div class="prose prose-sm text-gray-600 mb-8 leading-relaxed">
                                    {!! nl2br(e($download->description ?? 'Tidak ada deskripsi untuk dokumen ini.')) !!}
                                </div>

                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                                    @if($download->is_protected)
                                        <div class="text-center mb-4">
                                            <i class="fas fa-shield-alt text-4xl text-primary mb-3"></i>
                                            <p class="text-sm text-gray-600">Dokumen ini dilindungi kata sandi.</p>
                                        </div>
                                        <a href="{{ route('frontend.downloads.password', $download) }}"
                                            class="block w-full text-center py-4 bg-gray-900 text-white rounded-xl font-bold hover:bg-primary transition-all shadow-lg hover:shadow-primary/30">
                                            <i class="fas fa-key mr-2"></i> Masukkan Password
                                        </a>
                                    @else
                                        <form action="{{ route('frontend.downloads.download', $download) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full py-4 bg-primary text-white rounded-xl font-bold hover:bg-secondary transition-all shadow-lg hover:shadow-primary/30 flex items-center justify-center gap-2 group">
                                                <i
                                                    class="fas fa-cloud-arrow-down text-xl group-hover:scale-110 transition-transform"></i>
                                                <span>Download File Sekarang</span>
                                            </button>
                                        </form>
                                        <p class="text-center text-xs text-gray-400 mt-3">
                                            <i class="fas fa-check-circle text-green-500 mr-1"></i> File bebas virus dan aman
                                            untuk diunduh.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Related Files --}}
                @php
                    $relatedDownloads = App\Models\Download::active()
                        ->byCategory($download->category)
                        ->where('id', '!=', $download->id)
                        ->limit(3)
                        ->get();
                @endphp

                @if($relatedDownloads->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Dokumen Terkait</h3>
                            <a href="{{ route('frontend.downloads') }}"
                                class="text-primary hover:text-secondary text-sm font-medium">Lihat Semua</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($relatedDownloads as $related)
                                <a href="{{ route('frontend.downloads.show', $related) }}"
                                    class="group block bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
                                    <div class="flex items-start justify-between mb-3">
                                        <i
                                            class="fas fa-file-alt text-2xl text-primary/50 group-hover:text-primary transition-colors"></i>
                                        <span class="text-xs text-gray-400">{{ $related->download_count }}x diunduh</span>
                                    </div>
                                    <h4
                                        class="font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors line-clamp-2">
                                        {{ $related->title }}</h4>
                                    <p class="text-xs text-gray-500">{{ $related->created_at->format('d M Y') }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>
@endsection