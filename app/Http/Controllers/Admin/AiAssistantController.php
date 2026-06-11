<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AiAssistantService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    protected AiAssistantService $assistantService;

    public function __construct(AiAssistantService $assistantService)
    {
        $this->assistantService = $assistantService;
    }

    /**
     * Handle chat message.
     */
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $reply = $this->assistantService->chat($request->message);

        return response()->json([
            'reply' => $reply,
            'time' => now()->format('H:i'),
        ]);
    }
}