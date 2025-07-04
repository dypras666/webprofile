<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SiteSetting;

$settings = SiteSetting::all();
echo "Total settings: " . $settings->count() . "\n";
echo "\nFirst 5 settings:\n";
foreach($settings->take(5) as $setting) {
    echo $setting->key . " = " . $setting->value . "\n";
}

echo "\nLooking for site_name specifically:\n";
$siteName = SiteSetting::where('key', 'site_name')->first();
if($siteName) {
    echo "Found site_name: " . $siteName->value . "\n";
} else {
    echo "site_name not found\n";
}