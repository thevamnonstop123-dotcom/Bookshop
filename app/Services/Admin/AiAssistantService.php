<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AiAssistantService
{
    /**
     * Process a chat message and return AI response.
     */
    public function chat(string $message): string
    {
        // Try to answer from database first
        $dbAnswer = $this->queryDatabase($message);
        
        if ($dbAnswer) {
            return $dbAnswer;
        }

        // Fallback: ask Groq AI
        return $this->askGroq($message);
    }

    /**
     * Try to answer from database queries.
     */
    private function queryDatabase(string $message): ?string
    {
        $msg = strtolower($message);

        // Total books
        if (preg_match('/(how many|total|count).*book/', $msg)) {
            $count = DB::table('books')->whereNull('deleted_at')->count();
            $active = DB::table('books')->where('status', 'active')->whereNull('deleted_at')->count();
            return "📚 You have **{$count}** books total, **{$active}** active.";
        }

        // Total customers
        if (preg_match('/(how many|total|count).*customer/', $msg)) {
            $count = DB::table('customers')->count();
            $active = DB::table('customers')->where('status', 'active')->count();
            return "👥 You have **{$count}** customers, **{$active}** active.";
        }

        // Total orders
        if (preg_match('/(how many|total|count).*order/', $msg)) {
            $count = DB::table('orders')->count();
            $pending = DB::table('orders')->where('status', 'pending')->count();
            return "📦 **{$count}** total orders, **{$pending}** pending.";
        }

        // Total revenue
        if (preg_match('/(revenue|sales|earning|income)/', $msg)) {
            $total = DB::table('payments')->where('status', 'completed')->sum('amount');
            $thisMonth = DB::table('payments')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount');
            return "💰 Total revenue: **" . number_format($total) . " MMK** | This month: **" . number_format($thisMonth) . " MMK**";
        }

        // Low stock
        if (preg_match('/(low|out of|running out).*stock/', $msg)) {
            $lowStock = DB::table('books')
                ->where('stock_quantity', '<', 5)
                ->where('stock_quantity', '>', 0)
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->pluck('title')
                ->toArray();
            
            if (empty($lowStock)) {
                return "✅ No low stock alerts! All books have 5+ copies.";
            }
            return "⚠️ Low stock alert: **" . implode(', ', $lowStock) . "**";
        }

        // Best sellers (by order count)
        if (preg_match('/(best|top|popular|selling).*book/', $msg)) {
            $topBooks = DB::table('order_items')
                ->join('books', 'order_items.book_id', '=', 'books.id')
                ->select('books.title', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('books.id', 'books.title')
                ->orderByDesc('total_sold')
                ->limit(3)
                ->get();
            
            if ($topBooks->isEmpty()) {
                return "📊 No sales data yet.";
            }
            $list = $topBooks->map(fn($b) => "**{$b->title}** ({$b->total_sold} sold)")->join(', ');
            return "🏆 Best sellers: " . $list;
        }

        // New customers
        if (preg_match('/(new|recent).*customer/', $msg)) {
            $today = DB::table('customers')->whereDate('created_at', today())->count();
            $thisWeek = DB::table('customers')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
            return "👋 New customers: **{$today}** today, **{$thisWeek}** this week.";
        }

        // Categories
        if (preg_match('/(category|categories)/', $msg)) {
            $categories = DB::table('categories')->where('status', 'active')->pluck('name')->toArray();
            return "📂 Categories: **" . implode(', ', $categories) . "**";
        }

        // Help
        if (preg_match('/(help|what can you do|commands)/', $msg)) {
            return "🤖 I can answer:\n• How many books/customers/orders?\n• Total revenue?\n• Low stock alerts?\n• Best selling books?\n• New customers?\n• Categories list\n\nJust ask me!";
        }

        return null;
    }

    /**
     * Fallback: ask Groq AI.
     */
    private function askGroq(string $message): string
    {
        $apiKey = config('services.groq.key');
        $context = $this->getDatabaseContext();

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => "You are a helpful bookstore admin assistant. Here's the current database summary: {$context}. Keep answers short and friendly. Use emojis."],
                    ['role' => 'user', 'content' => $message]
                ],
                'temperature' => 0.7,
                'max_tokens' => 200,
            ]);

            if ($response->successful()) {
                return trim($response->json()['choices'][0]['message']['content'] ?? '');
            }
        } catch (\Exception $e) {}

        return "Sorry, I couldn't process that. Try asking about books, orders, customers, or revenue!";
    }

    /**
     * Get a summary of the database for AI context.
     */
    private function getDatabaseContext(): string
    {
        $books = DB::table('books')->where('status', 'active')->whereNull('deleted_at')->count();
        $customers = DB::table('customers')->count();
        $orders = DB::table('orders')->count();
        $revenue = DB::table('payments')->where('status', 'completed')->sum('amount');
        $lowStock = DB::table('books')->where('stock_quantity', '<', 5)->where('stock_quantity', '>', 0)->whereNull('deleted_at')->count();

        return "Books: {$books} active, Customers: {$customers}, Orders: {$orders}, Revenue: " . number_format($revenue) . " MMK, Low stock items: {$lowStock}";
    }
}