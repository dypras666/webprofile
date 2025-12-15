@extends('template.lpmbaru.layouts.app')

@section('content')
    <div class="relative bg-secondary py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('{{ asset('images/pattern.svg') }}'); background-size: cover;"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-white mb-4" data-aos="fade-down">Hubungi Kami</h1>
            <div class="flex justify-center items-center text-gray-300 text-sm md:text-base gap-2" data-aos="fade-up"
                data-aos-delay="100">
                <a href="{{ route('frontend.index') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-primary font-medium">Kontak</span>
            </div>
        </div>
    </div>

    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-12 bg-white rounded-2xl shadow-xl overflow-hidden">

                {{-- Contact Info --}}
                <div
                    class="w-full lg:w-1/3 bg-primary text-white p-12 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-black/10 rounded-full -ml-24 -mb-24"></div>

                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold mb-6">Informasi Kontak</h3>
                        <p class="text-blue-100 mb-8 leading-relaxed">Silakan hubungi kami melalui informasi di bawah ini
                            atau kirimkan pesan melalui formulir.</p>

                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold mb-1">Alamat</h4>
                                    <p class="text-blue-100 text-sm">
                                        {{ \App\Models\SiteSetting::getValue('contact_address', 'Alamat belum diatur') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold mb-1">Email</h4>
                                    <p class="text-blue-100 text-sm">{{ \App\Models\SiteSetting::getValue('contact_email',
                                        'admin@example.com') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold mb-1">Telepon</h4>
                                    <p class="text-blue-100 text-sm">
                                        {{ \App\Models\SiteSetting::getValue('contact_phone', '0812-3456-7890') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 mt-12">
                        <h4 class="font-bold mb-4">Sosial Media</h4>
                        <div class="flex space-x-4">
                            @if(\App\Models\SiteSetting::getValue('facebook_url'))
                                <a href="{{ \App\Models\SiteSetting::getValue('facebook_url') }}"
                                    class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white hover:text-primary transition-all">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if(\App\Models\SiteSetting::getValue('twitter_url'))
                                <a href="{{ \App\Models\SiteSetting::getValue('twitter_url') }}"
                                    class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white hover:text-primary transition-all">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if(\App\Models\SiteSetting::getValue('instagram_url'))
                                <a href="{{ \App\Models\SiteSetting::getValue('instagram_url') }}"
                                    class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white hover:text-primary transition-all">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Contact Form --}}
                <div class="w-full lg:w-2/3 p-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Kirim Pesan</h3>

                    @if(session('success'))
                        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('frontend.contact.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                    placeholder="Masukkan nama anda">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                    placeholder="alamat@email.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subjek</label>
                            <input type="text" name="subject" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                placeholder="Judul pesan">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                            <textarea name="message" rows="5" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                placeholder="Tuliskan pesan anda disini..."></textarea>
                        </div>

                        <button type="submit"
                            class="w-full md:w-auto px-8 py-3 bg-primary text-white font-bold rounded-lg hover:bg-blue-700 transition-all shadow-lg hover:shadow-primary/30 transform hover:-translate-y-1">
                            Kirim Pesan
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>
@endsection