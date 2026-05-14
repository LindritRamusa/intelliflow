<?php

namespace App\Http\Controllers;

use App\Models\AiLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function logs(Request $request): JsonResponse
    {
        $logs = AiLog::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(50);

        return response()->json([
            'data' => $logs->map(fn (AiLog $l) => [
                'id' => (string) $l->id,
                'prompt' => $l->prompt,
                'response' => $l->response,
                'model' => $l->model,
                'tokensUsed' => $l->tokens_used,
                'createdAt' => $l->created_at?->toISOString(),
            ]),
            'meta' => [
                'total' => $logs->total(),
                'page' => $logs->currentPage(),
                'perPage' => $logs->perPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'response' => 'required|string',
            'model' => 'required|string',
            'tokens_used' => 'integer|min:0',
            'context' => 'nullable|array',
        ]);

        $log = AiLog::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'organization_id' => $request->user()->organization_id,
        ]);

        return response()->json([
            'data' => [
                'id' => (string) $log->id,
                'prompt' => $log->prompt,
                'response' => $log->response,
                'model' => $log->model,
                'tokensUsed' => $log->tokens_used,
                'createdAt' => $log->created_at?->toISOString(),
            ],
        ], 201);
    }
}
