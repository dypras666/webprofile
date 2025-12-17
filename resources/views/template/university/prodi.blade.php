@extends('template.university.layouts.app')

@section('title', $prodi->name . ' - ' . \App\Helpers\TemplateHelper::getSetting('site_name'))

@push('styles')
<style>
    .prodi-content h2 { margin-bottom: 20px; font-weight: bold; font-size: 1.5rem; color: #1e3a8a; }
    .prodi-content h3 { margin-bottom: 15px; font-weight: bold; font-size: 1.25rem; color: #1e3a8a; }
    .prodi-content p { margin-bottom: 15px; line-height: 1.8; color: #4b5563; }
    .prodi-content ul { list-style-type: disc; margin-left: 20px; margin-bottom: 15px; }
    .prodi-content ol { list-style-type: decimal; margin-left: 20px; margin-bottom: 15px; }
    
    .active-tab {
        border-bottom: 3px solid #1e3a8a;
        color: #1e3a8a;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
    {{-- Hero/Breadcrumb Section --}}
    <div class="relative bg-blue-900 text-white py-24 overflow-hidden">
        <div class="absolute inset-0 bg-black/40 z-10"></div>
        <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('{{ !empty($prodi->image) ? Storage::url($prodi->image) : asset('images/university-bg.jpg') }}')"></div>
        
        <div class="container mx-auto px-4 md:px-6 relative z-20 text-center">
            <div class="inline-block bg-yellow-400 text-blue-900 px-4 py-1 rounded-full font-bold text-sm mb-4 uppercase tracking-wider">
                Program Studi {{ $prodi->degree }}
            </div>
            <h1 class="text-4xl md:text-5xl font-heading font-bold mb-4">{{ $prodi->name }}</h1>
            <p class="text-blue-100 text-lg max-w-2xl mx-auto">
                {{ $prodi->faculty ? 'Fakultas ' . $prodi->faculty : '' }}
            </p>
        </div>
    </div>

    <div class="bg-gray-50 py-16" x-data="{ tab: 'general' }">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                {{-- Main Content --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                        {{-- Tabs Header --}}
                        <div class="flex border-b border-gray-200 overflow-x-auto">
                            <button @click="tab = 'general'" 
                                class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap"
                                :class="tab === 'general' ? 'active-tab' : 'text-gray-500 hover:text-gray-700'">
                                <i class="fas fa-info-circle mr-2"></i> Profil
                            </button>
                            @if($prodi->vision || $prodi->mission)
                            <button @click="tab = 'visi'" 
                                class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap"
                                :class="tab === 'visi' ? 'active-tab' : 'text-gray-500 hover:text-gray-700'">
                                <i class="fas fa-bullseye mr-2"></i> Visi & Misi
                            </button>
                            @endif
                            @if($prodi->competence)
                            <button @click="tab = 'kompetensi'" 
                                class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap"
                                :class="tab === 'kompetensi' ? 'active-tab' : 'text-gray-500 hover:text-gray-700'">
                                <i class="fas fa-star mr-2"></i> Kompetensi
                            </button>
                            @endif
                        </div>

                        {{-- Tab Contents --}}
                        <div class="p-8 prodi-content">
                            {{-- General Profil Tab --}}
                            <div x-show="tab === 'general'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                <h2 class="text-2xl font-bold font-heading mb-6 text-gray-800">Tentang Program Studi</h2>
                                
                                <div class="prose max-w-none text-gray-600">
                                    {!! $prodi->description ? $prodi->description : '<p class="italic text-gray-500">Belum ada deskripsi profil untuk program studi ini.</p>' !!}
                                </div>

                                @if($prodi->address)
                                <div class="mt-8 bg-blue-50 p-6 rounded-lg border border-blue-100">
                                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2"></i> Lokasi Kampus
                                    </h3>
                                    <p class="text-blue-800">{!! $prodi->address !!}</p>
                                </div>
                                @endif
                            </div>

                            {{-- Visi & Misi Tab --}}
                            @if($prodi->vision || $prodi->mission)
                            <div x-show="tab === 'visi'" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                @if($prodi->vision)
                                <div class="mb-8">
                                    <h2 class="text-2xl font-bold font-heading mb-4 text-gray-800 flex items-center">
                                        <span class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center text-lg mr-3 shadow-lg"><i class="fas fa-eye"></i></span>
                                        Visi
                                    </h2>
                                    <div class="bg-white p-6 rounded-xl border-l-4 border-blue-600 shadow-sm">
                                        <div class="prose max-w-none italic text-lg text-gray-700">{!! $prodi->vision !!}</div>
                                    </div>
                                </div>
                                @endif

                                @if($prodi->mission)
                                <div>
                                    <h2 class="text-2xl font-bold font-heading mb-4 text-gray-800 flex items-center">
                                        <span class="w-10 h-10 bg-yellow-400 text-blue-900 rounded-full flex items-center justify-center text-lg mr-3 shadow-lg"><i class="fas fa-rocket"></i></span>
                                        Misi
                                    </h2>
                                    <div class="prose max-w-none text-gray-600">
                                        {!! $prodi->mission !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- Kompetensi Tab --}}
                            @if($prodi->competence)
                            <div x-show="tab === 'kompetensi'" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                <h2 class="text-2xl font-bold font-heading mb-6 text-gray-800">Kompetensi Lulusan</h2>
                                <div class="prose max-w-none text-gray-600">
                                    {!! $prodi->competence !!}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1 space-y-8">
                    {{-- Quick Info Card --}}
                    <div class="bg-white rounded-xl shadow-lg border-t-4 border-blue-600 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-center mb-6">
                                @if($prodi->image)
                                    <img src="{{ Storage::url($prodi->image) }}" alt="Logo" class="h-32 w-auto object-contain">
                                @else
                                    <div class="h-32 w-32 bg-gray-100 rounded-full flex items-center justify-center text-gray-300">
                                        <i class="fas fa-graduation-cap text-5xl"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div class="flex border-b border-gray-100 pb-3">
                                    <span class="text-gray-500 w-1/3 text-sm">Jenjang</span>
                                    <span class="font-bold text-gray-800 w-2/3 text-right">{{ $prodi->degree }}</span>
                                </div>
                                <div class="flex border-b border-gray-100 pb-3">
                                    <span class="text-gray-500 w-1/3 text-sm">Akreditasi</span>
                                    <span class="font-bold text-white bg-blue-600 px-3 py-1 rounded text-xs ml-auto">{{ $prodi->accreditation ?? 'N/A' }}</span>
                                </div>
                                 <div class="flex border-b border-gray-100 pb-3">
                                    <span class="text-gray-500 w-1/3 text-sm">Kode</span>
                                    <span class="font-bold text-gray-800 w-2/3 text-right">{{ $prodi->code ?? '-' }}</span>
                                </div>
                                @if($prodi->establishment_date)
                                <div class="flex border-b border-gray-100 pb-3">
                                    <span class="text-gray-500 w-1/3 text-sm">Berdiri</span>
                                    <span class="font-bold text-gray-800 w-2/3 text-right">{{ $prodi->establishment_date->format('d M Y') }}</span>
                                </div>
                                @endif
                                
                                @if($prodi->website_url)
                                <a href="{{ $prodi->website_url }}" target="_blank" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-colors mt-6">
                                    <i class="fas fa-globe mr-2"></i> Kunjungi Website
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Kaprodi Card --}}
                    @if($prodi->programHead)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Ketua Program Studi</h3>
                        <div class="flex items-center gap-4">
                            <img src="{{ !empty($prodi->programHead->image_url) ? $prodi->programHead->image_url : asset('images/default-avatar.png') }}" 
                                 alt="{{ $prodi->programHead->name }}" 
                                 class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">{{ $prodi->programHead->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $prodi->programHead->position ?? 'Kaprodi' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Contact Info --}}
                    <div class="bg-blue-900 text-white rounded-xl shadow-lg p-6">
                        <h3 class="font-bold text-white mb-4 pb-2 border-b border-blue-800">Kontak Prodi</h3>
                        <ul class="space-y-4 text-sm">
                            @if($prodi->email)
                            <li class="flex items-center">
                                <i class="fas fa-envelope w-6 text-yellow-400"></i>
                                <span class="opacity-90">{{ $prodi->email }}</span>
                            </li>
                            @endif
                            @if($prodi->phone)
                            <li class="flex items-center">
                                <i class="fas fa-phone w-6 text-yellow-400"></i>
                                <span class="opacity-90">{{ $prodi->phone }}</span>
                            </li>
                            @endif
                            <li class="flex items-start">
                                <i class="fas fa-map w-6 text-yellow-400 mt-1"></i>
                                <span class="opacity-90">{!! $prodi->address ?? 'Alamat kampus' !!}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Related Prodi --}}
                    @if($otherProdi->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Program Studi Lainnya</h3>
                        <ul class="space-y-3">
                            @foreach($otherProdi as $p)
                            <li>
                                <a href="{{ $p->code ? route('frontend.prodi.detail', $p->code) : '#' }}" class="flex items-center group">
                                    <i class="fas fa-chevron-right text-xs text-blue-500 w-5 transition-transform group-hover:translate-x-1"></i>
                                    <span class="text-gray-600 group-hover:text-blue-600 text-sm transition-colors">{{ $p->name }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
