<?php
$url = "http://localhost:8000/storage/media/3930bb97-8da8-4d66-bd49-b1136697edc6.jpg";
$headers = @get_headers($url);
echo "Checking URL: $url\n";
echo "Headers: " . print_r($headers, true) . "\n";
if ($headers && strpos($headers[0], '200') !== false) {
    echo "Image is accessible via URL.";
} else {
    echo "Image is NOT accessible via URL.";
}
