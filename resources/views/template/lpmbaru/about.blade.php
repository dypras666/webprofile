@extends('template.lpmbaru.layouts.app')

@section('title', 'Tentang Kami - ' . \App\Models\SiteSetting::getValue('site_name'))
@section('description', 'Pelajari lebih lanjut tentang ' . \App\Models\SiteSetting::getValue('site_name') . ', visi, misi, dan tim kami.')

@section('content')

    {{-- Page Header --}}
    <div class="relative bg-secondary py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('{{ asset('images/pattern.svg') }}'); background-size: cover;"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-white mb-4" data-aos="fade-down">Tentang Kami</h1>
            <div class="flex justify-center items-center text-gray-300 text-sm md:text-base gap-2" data-aos="fade-up"
                data-aos-delay="100">
                <a href="{{ route('frontend.index') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-primary font-medium">Tentang</span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-12 items-start">

                {{-- About Text --}}
                <div class="w-full lg:w-1/2" data-aos="fade-right">
                    <span
                        class="inline-block px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">PROFILE</span>
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-6 leading-tight">
                        Mengenal Lebih Dekat <br>
                        <span class="text-primary">{{ \App\Models\SiteSetting::getValue('site_name') }}</span>
                    </h2>
                    <div class="prose prose-lg text-gray-600 mb-8 leading-relaxed">
                        {!! $aboutContent ?? 'Konten tentang kami belum tersedia.' !!}
                    </div>

                    <div class="grid grid-cols-2 gap-6 mt-8">
                        <div class="bg-gray-50 p-6 rounded-xl border-l-4 border-primary">
                            <h4 class="text-4xl font-bold text-primary mb-2">10+</h4>
                            <p class="text-gray-600 font-medium">Tahun Pengalaman</p>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-xl border-l-4 border-primary">
                            <h4 class="text-4xl font-bold text-primary mb-2">100+</h4>
                            <p class="text-gray-600 font-medium">Program Sukses</p>
                        </div>
                    </div>
                </div>

                {{-- Image / Visual --}}
                <div class="w-full lg:w-1/2 relative" data-aos="fade-left">
                    <div class="absolute inset-0 bg-primary/20 transform translate-x-6 translate-y-6 rounded-2xl z-0"></div>
                    <img src="{{ \App\Models\SiteSetting::getValue('og_image') ? Storage::url(\App\Models\SiteSetting::getValue('og_image')) : asset('images/default-about.jpg') }}"
                        alt="About Us"
                        class="relative z-10 rounded-2xl shadow-xl w-full object-cover h-[400px] md:h-[600px]">

                    {{-- Floating Card --}}
                    <div class="absolute -bottom-6 -left-6 z-20 bg-white p-6 rounded-xl shadow-lg max-w-xs hidden md:block">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                                <i class="fas fa-quote-left text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-900 font-bold italic">"Berkomitmen untuk kualitas dan integritas."</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Vision Mission --}}
    <section class="py-16 md:py-24 bg-gray-50 relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 max-w-3xl mx-auto" data-aos="fade-up">
                <span
                    class="inline-block px-3 py-1 bg-white text-primary rounded-full text-sm font-semibold mb-4 shadow-sm">VISI
                    & MISI</span>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-6">Arah Tujuan Kami</h2>
                <p class="text-gray-600 text-lg">Menjadi lembaga yang unggul dan terpercaya dalam membangun peradaban
                    melalui pendidikan dan pengabdian.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                {{-- Vision --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group"
                    data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary text-3xl mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                        <i class="far fa-eye"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Visi</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Menjadi pusat keunggulan dalam pengembangan sumber daya manusia yang berintegritas, inovatif, dan
                        berdaya saing global dengan tetap menjunjung tinggi nilai-nilai kearifan lokal.
                    </p>
                </div>

                {{-- Mission --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group"
                    data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary text-3xl mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Misi</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-primary mt-1"></i>
                            <span>Menyelenggarakan pendidikan berkualitas yang terjangkau.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-primary mt-1"></i>
                            <span>Mengembangkan riset dan inovasi yang bermanfaat bagi masyarakat.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-primary mt-1"></i>
                            <span>Membangun kemitraan strategis dengan berbagai pihak.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Team/Structure (Placeholder) --}}
    {{-- You can add team section here later if needed --}}

@endsection