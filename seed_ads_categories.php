<?php
// Temporary script to seed Ads categories
use App\Models\Category;
use Illuminate\Support\Str;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$categories = [
    'Ads Popup' => 'ads-popup',
    'Ads Sidebar' => 'ads-sidebar',
    'Ads Footer' => 'ads-footer'
];

foreach ($categories as $name => $slug) {
    if (!Category::where('slug', $slug)->exists()) {
        Category::create([
            'name' => $name,
            'slug' => $slug,
            'description' => 'System Category for ' . $name
        ]);
        echo "Created category: $name\n";
    } else {
        echo "Category already exists: $name\n";
    }
}
