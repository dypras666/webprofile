<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::published()->orderBy('updated_at', 'desc')->get();
        $categories = Category::active()->orderBy('updated_at', 'desc')->get();

        $content = view('sitemap.index', compact('posts', 'categories'))->render();

        return response($content)->header('Content-Type', 'text/xml');
    }
}
