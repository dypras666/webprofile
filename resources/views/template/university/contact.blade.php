@extends('template.university.layouts.app')

@section('title', 'Hubungi Kami - ' . \App\Models\SiteSetting::getValue('site_name'))

@section('content')

    {{-- Page Header --}}
    <div class="relative bg-secondary py-16 lg:py-24 overflow-hidden">
        {{-- Abstract Pattern --}}
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-bold font-heading text-white mb-4">Hubungi Kami</h1>
            <nav class="flex justify-center items-center text-green-100 text-sm md:text-base gap-2">
                <a href="{{ route('frontend.index') }}" class="hover:text-white transition-colors">Beranda</a>
                <span class="text-green-500">/</span>
                <span class="font-medium text-white">Kontak</span>
            </nav>
        </div>
    </div>

    <section class="py-16 md:py-24 bg-white relative">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row shadow-2xl rounded-2xl overflow-hidden">

                {{-- Info Side --}}
                <div class="w-full lg:w-1/3 bg-secondary text-white p-8 md:p-12 relative overflow-hidden">
                    {{-- Decor --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-8 -mt-8"></div>
                    <div class="absolute bottom-0 left-0 w-40 h-40 bg-primary/20 rounded-full -ml-8 -mb-8"></div>

                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div>
                            <h3 class="text-2xl font-bold font-heading mb-6">Informasi Kontak</h3>
                            <p class="text-green-100 mb-8 text-sm leading-relaxed">
                                Punya pertanyaan atau butuh bantuan? Tim kami siap membantu Anda. Silakan hubungi kami
                                melalui saluran berikut.
                            </p>

                            <div class="space-y-6">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0 text-primary">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <span
                                            class="block text-xs uppercase tracking-wider text-green-400 font-bold mb-1">Alamat</span>
                                        <p class="text-white text-sm leading-snug">
                                            {{ \App\Models\SiteSetting::getValue('contact_address', 'Alamat belum diatur') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0 text-primary">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <span
                                            class="block text-xs uppercase tracking-wider text-green-400 font-bold mb-1">Email</span>
                                        <p class="text-white text-sm">
                                            {{ \App\Models\SiteSetting::getValue('contact_email', 'admin@example.com') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0 text-primary">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div>
                                        <span
                                            class="block text-xs uppercase tracking-wider text-green-400 font-bold mb-1">Telepon</span>
                                        <p class="text-white text-sm">
                                            {{ \App\Models\SiteSetting::getValue('contact_phone', '0812-3456-7890') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12">
                            <span class="block text-xs uppercase tracking-wider text-green-400 font-bold mb-4">Sosial
                                Media</span>
                            <div class="flex gap-3">
                                @if(\App\Models\SiteSetting::getValue('facebook_url'))
                                    <a href="{{ \App\Models\SiteSetting::getValue('facebook_url') }}"
                                        class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary hover:text-white transition-colors"><i
                                            class="fab fa-facebook-f"></i></a>
                                @endif
                                @if(\App\Models\SiteSetting::getValue('twitter_url'))
                                    <a href="{{ \App\Models\SiteSetting::getValue('twitter_url') }}"
                                        class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary hover:text-white transition-colors"><i
                                            class="fab fa-twitter"></i></a>
                                @endif
                                @if(\App\Models\SiteSetting::getValue('instagram_url'))
                                    <a href="{{ \App\Models\SiteSetting::getValue('instagram_url') }}"
                                        class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary hover:text-white transition-colors"><i
                                            class="fab fa-instagram"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Side --}}
                <div class="w-full lg:w-2/3 bg-white p-8 md:p-12">
                    <h3 class="text-2xl font-bold font-heading text-gray-900 mb-6">Kirim Pesan</h3>

                    @if(session('success'))
                        <div
                            class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-3">
                            <i class="fas fa-check-circle text-xl"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('frontend.contact.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none"
                                    placeholder="Masukkan nama anda">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Email</label>
                                <input type="email" name="email" required
                                    class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none"
                                    placeholder="anda@example.com">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Subjek</label>
                            <input type="text" name="subject" required
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none"
                                placeholder="Tujuan pesan anda">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Pesan</label>
                            <textarea name="message" rows="5" required
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none resize-none"
                                placeholder="Tulis pesan anda disini..."></textarea>
                        </div>

                        <button type="submit"
                            class="w-full md:w-auto px-8 py-3 bg-primary hover:bg-emerald-700 text-white font-bold rounded-lg transition-colors shadow-lg shadow-primary/30 flex items-center justify-center gap-2">
                            <span>Kirim Pesan</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

@endsection