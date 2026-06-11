<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$key = env('GEMINI_API_KEY');
$title = 'Python for Beginners';
$prompt = "Write a short, professional book description for: {$title}. Keep under 500 chars.";

$resp = Http::withHeaders(['Content-Type' => 'application/json'])
    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$key}", [
        'contents' => [['parts' => [['text' => $prompt]]]]
    ]);

echo "Status: " . $resp->status() . "\n";
echo "Body: " . $resp->body() . "\n";
