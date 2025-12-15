@extends('template.university.layouts.app')

@section('title', 'Tentang Kami - ' . \App\Models\SiteSetting::getValue('site_name'))
@section('description', 'Pelajari lebih lanjut tentang ' . \App\Models\SiteSetting::getValue('site_name') . ', visi, misi, dan tim kami.')

@section('content')

    {{-- Page Header --}}
    <div class="relative bg-secondary py-16 lg:py-24 overflow-hidden">
        {{-- Abstract Pattern --}}
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-bold font-heading text-white mb-4">Tentang Kami</h1>
            <nav class="flex justify-center items-center text-green-100 text-sm md:text-base gap-2">
                <a href="{{ route('frontend.index') }}" class="hover:text-white transition-colors">Beranda</a>
                <span class="text-green-500">/</span>
                <span class="font-medium text-white">Tentang</span>
            </nav>
            <div class="bg-white">

                {{-- Hero / About Main --}}
                <div class="container mx-auto px-4 py-12 md:py-16">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                        <div class="order-2 md:order-1">
                            <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block">Profil</span>
                            <h1 class="text-3xl md:text-5xl font-bold font-heading text-secondary mb-6 leading-tight">
                                {{ $aboutPost->title ?? 'Tentang Kami' }}
                            </h1>
                            <div class="prose prose-lg text-gray-600 leading-relaxed mb-6">
                                @if($aboutPost)
                                    {!! $aboutPost->content !!}
                                @else
                                    <p>
                                        {{ \App\Models\SiteSetting::getValue('about_content', 'Informasi tentang kami belum tersedia.') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="order-1 md:order-2">
                            <div
                                class="relative rounded-2xl overflow-hidden shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500">
                                @if($aboutPost && $aboutPost->featured_image)
                                    <img src="{{ Storage::url($aboutPost->featured_image) }}" alt="About Us"
                                        class="w-full h-auto object-cover">
                                @else
                                    <img src="{{ \App\Models\SiteSetting::getValue('about_image') ? Storage::url(\App\Models\SiteSetting::getValue('about_image')) : asset('images/default-hero.jpg') }}"
                                        alt="About Us" class="w-full h-auto object-cover">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Visi Misi Section --}}
                <div class="bg-gray-50 py-16 md:py-20 relative overflow-hidden">
                    {{-- Background Pattern --}}
                    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-secondary/5 rounded-full blur-3xl">
                    </div>

                    <div class="container mx-auto px-4 relative z-10">
                        <div class="text-center max-w-3xl mx-auto mb-16">
                            <h2 class="text-3xl md:text-4xl font-bold font-heading text-secondary mb-4">Visi & Misi</h2>
                            <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                            {{-- Visi --}}
                            <div
                                class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-4 mb-6">
                                    <div
                                        class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-2xl">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-secondary">{{ $visiPost->title ?? 'Visi' }}</h3>
                                </div>
                                <div class="prose text-gray-600">
                                    @if($visiPost)
                                        {!! $visiPost->content !!}
                                    @else
                                        <p>Menjadi institusi yang unggul dan terpercaya.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Misi --}}
                            <div
                                class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-4 mb-6">
                                    <div
                                        class="w-12 h-12 rounded-lg bg-secondary/10 flex items-center justify-center text-secondary text-2xl">
                                        <i class="fas fa-bullseye"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-secondary">{{ $misiPost->title ?? 'Misi' }}</h3>
                                </div>
                                <div class="prose text-gray-600">
                                    @if($misiPost)
                                        {!! $misiPost->content !!}
                                    @else
                                        <ul class="list-disc pl-5 space-y-2">
                                            <li>Memberikan pelayanan terbaik.</li>
                                            <li>Meningkatkan kualitas sumber daya manusia.</li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection