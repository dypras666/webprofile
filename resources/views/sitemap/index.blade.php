<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Static Pages --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('frontend.about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('frontend.contact') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('frontend.gallery') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>{{ route('frontend.downloads') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>{{ route('frontend.posts') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ route('frontend.events') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ route('frontend.facilities') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>{{ route('frontend.team') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    {{-- Program Studi --}}
    @foreach ($programStudis as $prodi)
        <url>
            <loc>{{ route('frontend.prodi.detail', $prodi->code) }}</loc>
            <lastmod>{{ $prodi->updated_at->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    {{-- Downloads (Individual) --}}
    @foreach ($downloads as $download)
        <url>
            <loc>{{ route('frontend.downloads.show', $download) }}</loc>
            <lastmod>{{ $download->updated_at->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach

    {{-- Posts --}}
    @foreach ($posts as $post)
        <url>
            <loc>{{ $post->type === 'page' ? route('frontend.page', $post->slug) : route('frontend.post', $post->slug) }}</loc>
            <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    {{-- Categories --}}
    @foreach ($categories as $category)
        <url>
            <loc>{{ route('frontend.category', $category->slug) }}</loc>
            <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach
</urlset>
