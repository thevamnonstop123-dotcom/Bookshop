<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AiAssistantService
{
    /**
     * Core chat pipeline utilizing dynamic database state reflection.
     */
    public function chat(string $message): string
    {
        $apiKey = config('services.groq.key');
        if (!$apiKey) {
            logger()->error('AI Assistant: Missing Groq API Key configuration.');
            return "⚠️ System configuration error. Component offline.";
        }

        // Pull fresh database state snapshot
        $contextPayload = $this->compileSystemContext();

        $systemPrompt = "You are the central Business Intelligence Core Engine for this bookstore's administration portal.\n" .
                        "You possess real-time visibility into the operational database snapshot below:\n" .
                        "=========================================================\n" .
                        "{$contextPayload}\n" .
                        "=========================================================\n\n" .
                        "CRITICAL OPERATIONAL RULES:\n" .
                        "1. Rely ONLY on the numbers provided in the matrix above. Never guess or hallucinate statistics.\n" .
                        "2. If the user asks for financial revenue, counts, or specific inventory alerts, report them exactly.\n" .
                        "3. If the user requests business planning, marketing insights, or operational advice, synthesize your strategy using the live metrics provided.\n" .
                        "4. Maintain a professional, direct tone. Format answers using markdown clear tables or bullet points. Use emojis.";

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $message]
                    ],
                    'temperature' => 0.3, // Low variance to keep metric delivery strict
                    'max_tokens' => 500
                ]);

            if ($response->successful()) {
                return trim($response->json()['choices'][0]['message']['content'] ?? '');
            }
            
            logger()->error('Groq API Connection Failed', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Exception $e) {
            logger()->error('AI Assistant Pipeline Exception: ' . $e->getMessage());
        }

        return "⚠️ Failed to stream response processing block. Please try your request again.";
    }

    /**
     * Compiles live telemetry matrix from storage layers.
     * PERFORMANCE NOTE: Ensure indexes exist on: payments(status), books(status, stock_quantity), customers(created_at)
     */
    private function compileSystemContext(): string
    {
        // Cache heavy computational calculations for 30 seconds to defend database thread pools
        return Cache::remember('admin_ai_db_snapshot', 30, function () {
            
            $booksTotal = DB::table('books')->whereNull('deleted_at')->count();
            $booksActive = DB::table('books')->where('status', 'active')->whereNull('deleted_at')->count();
            
            $customersTotal = DB::table('customers')->count();
            $customersActive = DB::table('customers')->where('status', 'active')->count();
            $customersToday = DB::table('customers')->whereDate('created_at', today())->count();
            $customersThisWeek = DB::table('customers')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
            
            $ordersTotal = DB::table('orders')->count();
            $ordersPending = DB::table('orders')->where('status', 'pending')->count();
            
            $revenueTotal = DB::table('payments')->where('status', 'completed')->sum('amount');
            $revenueMonth = DB::table('payments')->where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount');
            
            $categories = DB::table('categories')->where('status', 'active')->pluck('name')->join(', ');
            
            $lowStockArray = DB::table('books')
                ->where('stock_quantity', '<', 5)
                ->where('stock_quantity', '>', 0)
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->pluck('title')
                ->toArray();
            $lowStockText = empty($lowStockArray) ? "None (all active variants possess 5+ copies)" : implode(', ', $lowStockArray);

            $topBooks = DB::table('order_items')
                ->join('books', 'order_items.book_id', '=', 'books.id')
                ->select('books.title', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('books.id', 'books.title')
                ->orderByDesc('total_sold')
                ->limit(3)
                ->get()
                ->map(fn($b) => "{$b->title} ({$b->total_sold} units sold)")
                ->join(' | ');

            return "Gross Total Revenue: " . number_format($revenueTotal) . " MMK\n" .
                   "Current Month Revenue: " . number_format($revenueMonth) . " MMK\n" .
                   "Total Books in System: {$booksTotal} (Active: {$booksActive})\n" .
                   "Low Stock Items: {$lowStockText}\n" .
                   "Top 3 Best Selling Books: " . ($topBooks ?: "No sales data logged yet") . "\n" .
                   "Total Registered Customers: {$customersTotal} (Active: {$customersActive})\n" .
                   "New Customers Added Today: {$customersToday}\n" .
                   "New Customers Added This Week: {$customersThisWeek}\n" .
                   "Total Logged Orders: {$ordersTotal} (Pending: {$ordersPending})\n" .
                   "Active System Store Categories: [{$categories}]";
        });
    }
}