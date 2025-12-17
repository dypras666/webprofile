@extends('template.university.layouts.app')

@section('title', 'Login - ' . \App\Models\SiteSetting::getValue('site_name'))

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="relative bg-blue-900 py-24 overflow-hidden flex items-center justify-center min-h-[70vh]">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('{{ asset('images/default-post.jpg') }}'); background-size: cover; background-position: center;">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 to-black/50"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div
                class="max-w-md w-full mx-auto bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all hover:scale-[1.01]">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-center">
                    <div
                        class="mx-auto h-16 w-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4 shadow-inner">
                        <i class="fas fa-user-shield text-white text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white tracking-wide">
                        Administrator Login
                    </h2>
                    <p class="mt-2 text-blue-100 text-sm opacity-90">
                        Silakan masuk untuk mengelola website
                    </p>
                </div>

                <div class="p-8">
                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg flex items-start">
                            <i class="fas fa-exclamation-circle mt-1 mr-3 flex-shrink-0"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    <form class="space-y-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email Address
                                </label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                        class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm placeholder-gray-400 text-gray-900"
                                        placeholder="nama@email.com" value="{{ old('email') }}">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div x-data="{ showPassword: false }">
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Password
                                </label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <input id="password" name="password" :type="showPassword ? 'text' : 'password'"
                                        autocomplete="current-password" required
                                        class="appearance-none block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm placeholder-gray-400 text-gray-900"
                                        placeholder="••••••••">
                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none transition-colors">
                                        <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center">
                                <input id="remember-me" name="remember" type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                                <label for="remember-me"
                                    class="ml-2 block text-sm text-gray-600 cursor-pointer hover:text-gray-900">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        @php
                            $recaptchaSiteKey = \App\Models\SiteSetting::getValue('recaptcha_site_key');
                        @endphp

                        @if(!empty($recaptchaSiteKey))
                            <div class="flex justify-center mt-4">
                                <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                            </div>
                            @error('g-recaptcha-response')
                                <p class="text-center text-sm text-red-600 mt-2"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        @endif

                        <div class="pt-2">
                            <button type="submit"
                                class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-md hover:shadow-lg transform active:scale-[0.98]">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <i
                                        class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400 transition-colors"></i>
                                </span>
                                Simpan & Masuk
                            </button>
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} {{ \App\Models\SiteSetting::getValue('site_name') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @php
        $recaptchaSiteKey = \App\Models\SiteSetting::getValue('recaptcha_site_key');
    @endphp
    @if(!empty($recaptchaSiteKey))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
@endpush