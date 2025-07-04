@extends('layouts.app')

@section('meta_title', 'About Us - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', $siteSettings['about_meta_description'] ?? ('Learn more about ' . ($siteSettings['site_name'] ?? config('app.name')) . ', our mission, vision, and the team behind our success.'))
@section('meta_keywords', $siteSettings['about_meta_keywords'] ?? ('about us, company, team, mission, vision, ' . ($siteSettings['site_name'] ?? config('app.name'))))

@section('canonical', url()->current())

@section('og_title', 'About Us - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('og_description', $siteSettings['about_meta_description'] ?? ('Learn more about ' . ($siteSettings['site_name'] ?? config('app.name')) . ', our mission, vision, and the team behind our success.'))
@section('og_url', url()->current())
@section('og_type', 'website')
@section('og_site_name', $siteSettings['site_name'] ?? config('app.name'))

@section('twitter_card', 'summary_large_image')
@section('twitter_title', 'About Us - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('twitter_description', $siteSettings['about_meta_description'] ?? ('Learn more about ' . ($siteSettings['site_name'] ?? config('app.name')) . ', our mission, vision, and the team behind our success.'))

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "AboutPage",
    "name": "About Us - {{ $siteSettings['site_name'] ?? config('app.name') }}",
    "description": "{{ $siteSettings['about_meta_description'] ?? ('Learn more about ' . ($siteSettings['site_name'] ?? config('app.name')) . ', our mission, vision, and the team behind our success.') }}",
    "url": "{{ url()->current() }}",
    "mainEntity": {
        "@type": "Organization",
        "name": "{{ $siteSettings['site_name'] ?? config('app.name') }}",
        "url": "{{ url('/') }}",
        "logo": "{{ $siteSettings['logo'] ? asset('storage/' . $siteSettings['logo']) : asset('images/logo.png') }}",
        "sameAs": [
            @if($siteSettings['social_facebook'] ?? false)"{{ $siteSettings['social_facebook'] }}",@endif
@if($siteSettings['social_twitter'] ?? false)"{{ $siteSettings['social_twitter'] }}",@endif
@if($siteSettings['social_instagram'] ?? false)"{{ $siteSettings['social_instagram'] }}",@endif
@if($siteSettings['social_linkedin'] ?? false)"{{ $siteSettings['social_linkedin'] }}"@endif
        ].filter(Boolean)
    }
}
</script>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">About Us</h1>
        <div class="w-20 h-1 bg-primary mx-auto mb-6"></div>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto">Learn more about our company, our mission, and the team behind our success.</p>
    </div>

    <!-- About Content -->
    <div class="max-w-4xl mx-auto mb-12">
        <div class="prose prose-lg mx-auto">
            {!! $aboutContent !!}
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="grid md:grid-cols-2 gap-8 mb-16">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold">Our Mission</h2>
            </div>
            <p class="text-gray-600">To provide accurate, timely, and insightful information that empowers our readers to make informed decisions and stay connected with the world around them.</p>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-md">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold">Our Vision</h2>
            </div>
            <p class="text-gray-600">To become the most trusted source of news and information, known for our integrity, depth of coverage, and commitment to journalistic excellence.</p>
        </div>
    </div>

    <!-- Team Section -->
    @if(count($teamMembers) > 0)
    <div class="mb-16">
        <h2 class="text-2xl font-bold text-center mb-8">Meet Our Team</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($teamMembers as $member)
            <div class="bg-white rounded-lg overflow-hidden shadow-md transition-transform duration-300 hover:transform hover:scale-105">
                @if(isset($member['photo']))
                <img src="{{ $member['photo'] }}" alt="{{ $member['name'] }}" class="w-full h-64 object-cover">
                @else
                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                @endif
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-1">{{ $member['name'] }}</h3>
                    <p class="text-primary font-medium mb-3">{{ $member['position'] }}</p>
                    <p class="text-gray-600 mb-4">{{ $member['bio'] }}</p>
                    <div class="flex space-x-3">
                        @if(isset($member['social']['twitter']))
                        <a href="{{ $member['social']['twitter'] }}" class="text-gray-500 hover:text-primary" target="_blank" rel="noopener">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        @endif
                        @if(isset($member['social']['linkedin']))
                        <a href="{{ $member['social']['linkedin'] }}" class="text-gray-500 hover:text-primary" target="_blank" rel="noopener">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/>
                            </svg>
                        </a>
                        @endif
                        @if(isset($member['social']['email']))
                        <a href="mailto:{{ $member['social']['email'] }}" class="text-gray-500 hover:text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Company Stats -->
    <div class="bg-gray-100 p-8 rounded-lg mb-16">
        <h2 class="text-2xl font-bold text-center mb-8">Our Impact</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="p-4">
                <div class="text-4xl font-bold text-primary mb-2">10+</div>
                <div class="text-gray-600">Years of Experience</div>
            </div>
            <div class="p-4">
                <div class="text-4xl font-bold text-primary mb-2">5K+</div>
                <div class="text-gray-600">Articles Published</div>
            </div>
            <div class="p-4">
                <div class="text-4xl font-bold text-primary mb-2">100K+</div>
                <div class="text-gray-600">Monthly Readers</div>
            </div>
            <div class="p-4">
                <div class="text-4xl font-bold text-primary mb-2">50+</div>
                <div class="text-gray-600">Awards Won</div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-primary text-white p-8 rounded-lg text-center">
        <h2 class="text-2xl font-bold mb-4">Join Our Community</h2>
        <p class="mb-6 max-w-2xl mx-auto">Stay updated with our latest news, articles, and exclusive content by subscribing to our newsletter.</p>
        <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-2">
            <input type="email" placeholder="Your email address" class="flex-1 px-4 py-2 rounded-l text-gray-800" required>
            <button type="submit" class="bg-white text-primary font-medium px-6 py-2 rounded-r hover:bg-gray-100 transition-colors">Subscribe</button>
        </form>
    </div>
</div>
@endsection