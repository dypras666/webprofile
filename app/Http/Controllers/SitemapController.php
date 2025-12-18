<?php

namespace App\Http\Controllers;


use App\Models\Post;
use App\Models\Category;
use App\Models\ProgramStudi;
use App\Models\Download;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::published()->orderBy('updated_at', 'desc')->get();
        $categories = Category::active()->orderBy('updated_at', 'desc')->get();
        $programStudis = ProgramStudi::orderBy('updated_at', 'desc')->get();
        $downloads = Download::active()->public()->orderBy('updated_at', 'desc')->get();

        $content = view('sitemap.index', compact('posts', 'categories', 'programStudis', 'downloads'))->render();

        return response($content)->header('Content-Type', 'text/xml');
    }
}
