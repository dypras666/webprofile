@extends('layouts.app')

@section('title', 'Login - Admin')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div class="text-center mb-8">
                    <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user-shield text-white text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        Login Admin
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Masuk ke Panel Admin
                    </p>
                </div>
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2 text-blue-600"></i>Email
                            </label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                placeholder="Masukkan email Anda" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div x-data="{ showPassword: false }">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-blue-600"></i>Password
                            </label>
                            <div class="relative">
                                <input id="password" name="password" :type="showPassword ? 'text' : 'password'"
                                    autocomplete="current-password" required
                                    class="appearance-none block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 pr-10"
                                    placeholder="Masukkan password Anda">
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember" type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                <i class="fas fa-check-circle mr-1 text-blue-600"></i>Ingat saya
                            </label>
                        </div>
                    </div>

                    @php
                        $recaptchaSiteKey = \App\Models\SiteSetting::getValue('recaptcha_site_key');
                    @endphp

                    @if(!empty($recaptchaSiteKey))
                        <div class="flex justify-center">
                            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                        </div>
                        @error('g-recaptcha-response')
                            <p class="text-center text-sm text-red-600 mt-2"><i
                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    @endif

                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-sign-in-alt mr-2 text-white"></i>
                            Login
                        </button>
                    </div>

                    @if (session('error'))
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif
                </form>
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