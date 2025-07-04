@extends('layouts.app')

@section('meta_title', 'Contact Us - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', $siteSettings['contact_meta_description'] ?? ('Get in touch with ' . ($siteSettings['site_name'] ?? config('app.name')) . '. Send us your questions, feedback, or inquiries and we\'ll get back to you soon.'))
@section('meta_keywords', $siteSettings['contact_meta_keywords'] ?? ('contact us, get in touch, feedback, inquiries, ' . ($siteSettings['site_name'] ?? config('app.name'))))

@section('canonical', url()->current())

@section('og_title', 'Contact Us - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('og_description', $siteSettings['contact_meta_description'] ?? ('Get in touch with ' . ($siteSettings['site_name'] ?? config('app.name')) . '. Send us your questions, feedback, or inquiries and we\'ll get back to you soon.'))
@section('og_url', url()->current())
@section('og_type', 'website')
@section('og_site_name', $siteSettings['site_name'] ?? config('app.name'))

@section('twitter_card', 'summary_large_image')
@section('twitter_title', 'Contact Us - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('twitter_description', $siteSettings['contact_meta_description'] ?? ('Get in touch with ' . ($siteSettings['site_name'] ?? config('app.name')) . '. Send us your questions, feedback, or inquiries and we\'ll get back to you soon.'))

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ContactPage",
    "name": "Contact Us - {{ $siteSettings['site_name'] ?? config('app.name') }}",
    "description": "{{ $siteSettings['contact_meta_description'] ?? ('Get in touch with ' . ($siteSettings['site_name'] ?? config('app.name')) . '. Send us your questions, feedback, or inquiries and we\'ll get back to you soon.') }}",
    "url": "{{ url()->current() }}",
    "mainEntity": {
        "@type": "Organization",
        "name": "{{ $siteSettings['site_name'] ?? config('app.name') }}",
        "url": "{{ url('/') }}",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "{{ $siteSettings['contact_phone'] ?? '+1-234-567-8900' }}",
            "contactType": "Customer Service",
            "email": "{{ $siteSettings['contact_email'] ?? 'info@example.com' }}",
            "availableLanguage": "English"
        },
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $siteSettings['contact_address'] ?? '123 Main Street' }}",
            "addressLocality": "{{ $siteSettings['contact_city'] ?? 'City' }}",
            "addressRegion": "{{ $siteSettings['contact_state'] ?? 'State' }}",
            "postalCode": "{{ $siteSettings['contact_postal_code'] ?? '12345' }}",
            "addressCountry": "{{ $siteSettings['contact_country'] ?? 'US' }}"
        }
    }
}
</script>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Contact Us</h1>
        <div class="w-20 h-1 bg-primary mx-auto mb-6"></div>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto">We'd love to hear from you. Send us your questions, feedback, or inquiries and we'll get back to you soon.</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-8 max-w-4xl mx-auto">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    <div class="max-w-6xl mx-auto">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mb-6">Send us a Message</h2>
                    
                    <form action="{{ route('frontend.contact.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror" 
                                       placeholder="Your full name" required>
                                @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('email') border-red-500 @enderror" 
                                       placeholder="your@email.com" required>
                                @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('subject') border-red-500 @enderror" 
                                   placeholder="What is this about?" required>
                            @error('subject')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                            <textarea id="message" name="message" rows="6" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('message') border-red-500 @enderror" 
                                      placeholder="Tell us more about your inquiry..." required>{{ old('message') }}</textarea>
                            @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white font-medium py-3 px-6 rounded-lg hover:bg-primary-dark transition-colors duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="space-y-6">
                <!-- Contact Details -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4">Get in Touch</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mr-4 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Address</h4>
                                <p class="text-gray-600">{{ $siteSettings['contact_address'] ?? '123 Main Street' }}<br>{{ ($siteSettings['contact_city'] ?? 'City') . ', ' . ($siteSettings['contact_state'] ?? 'State') . ' ' . ($siteSettings['contact_postal_code'] ?? '12345') }}<br>{{ $siteSettings['contact_country'] ?? 'United States' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mr-4 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Phone</h4>
                                <p class="text-gray-600">{{ $siteSettings['contact_phone'] ?? '+1 (234) 567-8900' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mr-4 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Email</h4>
                                <p class="text-gray-600">{{ $siteSettings['contact_email'] ?? 'info@example.com' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mr-4 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Business Hours</h4>
                                <p class="text-gray-600">Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Social Media -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        @if($siteSettings['social_facebook'] ?? false)
                        <a href="{{ $siteSettings['social_facebook'] }}" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors" title="Facebook" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        @endif
                        @if($siteSettings['social_twitter'] ?? false)
                        <a href="{{ $siteSettings['social_twitter'] }}" class="w-10 h-10 bg-blue-400 text-white rounded-full flex items-center justify-center hover:bg-blue-500 transition-colors" title="Twitter" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        @endif
                        @if($siteSettings['social_instagram'] ?? false)
                        <a href="{{ $siteSettings['social_instagram'] }}" class="w-10 h-10 bg-pink-600 text-white rounded-full flex items-center justify-center hover:bg-pink-700 transition-colors" title="Instagram" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297zm7.718-9.469c-.49 0-.875-.385-.875-.875s.385-.875.875-.875.875.385.875.875-.385.875-.875.875zm-3.718 9.469c-2.26 0-4.094-1.834-4.094-4.094s1.834-4.094 4.094-4.094 4.094 1.834 4.094 4.094-1.834 4.094-4.094 4.094z"/>
                            </svg>
                        </a>
                        @endif
                        @if($siteSettings['social_linkedin'] ?? false)
                        <a href="{{ $siteSettings['social_linkedin'] }}" class="w-10 h-10 bg-blue-700 text-white rounded-full flex items-center justify-center hover:bg-blue-800 transition-colors" title="LinkedIn" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                
                <!-- FAQ -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4">Quick Answers</h3>
                    <div class="space-y-3">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-1">Response Time</h4>
                            <p class="text-sm text-gray-600">We typically respond within 24 hours during business days.</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-1">Press Inquiries</h4>
                            <p class="text-sm text-gray-600">For media requests, please email press@example.com</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-1">Technical Support</h4>
                            <p class="text-sm text-gray-600">For technical issues, please include your browser and device information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection