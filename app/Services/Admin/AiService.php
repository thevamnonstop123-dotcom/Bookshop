<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Http;

class AiService
{
    /**
     * Generate a book description using Groq API.
     */
    public function generateDescription(string $title, string $category = ''): string
    {
        $apiKey = config('services.groq.key');
        $prompt = "Write a short, professional book description for a book titled \"{$title}\"" . 
                  ($category ? " in the {$category} category" : "") . 
                  ". Keep it under 500 characters. Return ONLY the description text, no quotes or labels.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 300,
            ]);

            if ($response->successful()) {
                $text = $response->json()['choices'][0]['message']['content'] ?? '';
                return trim($text);
            }
        } catch (\Exception $e) {
            return '';
        }

        return '';
    }

    /**
     * Generate multiple books using Groq API.
     * Returns array of book data.
     */
    public function generateBooks(string $category, string $language, int $count, string $topic = ''): array
    {
        $apiKey = config('services.groq.key');
        $topicStr = $topic ? " about {$topic}" : '';
        $prompt = "Generate {$count} realistic book titles{$topicStr} in the {$category} category. " .
                  "For each book, provide: title, description (under 500 chars), and a realistic price in MMK (between 10,000 and 50,000). " .
                  "Return ONLY valid JSON in this exact format: " .
                  '[{"title":"...","description":"...","price":25000}]';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.8,
            ]);

            if ($response->successful()) {
                $text = $response->json()['choices'][0]['message']['content'] ?? '';
                // Extract JSON from response
                preg_match('/\[.*\]/s', $text, $matches);
                if (!empty($matches[0])) {
                    $books = json_decode($matches[0], true);
                    if (is_array($books)) {
                        return $books;
                    }
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }
}