<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Candidate::where('organization_id', $request->user()->organization_id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->input('search') . '%')
                  ->orWhere('applied_role', 'ilike', '%' . $request->input('search') . '%')
                  ->orWhere('email', 'ilike', '%' . $request->input('search') . '%');
            });
        }

        $candidates = $query->paginate(20);

        return response()->json([
            'data' => $candidates->items(),
            'meta' => [
                'total' => $candidates->total(),
                'page' => $candidates->currentPage(),
                'perPage' => $candidates->perPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'applied_role' => 'required|string|max:255',
            'cv_text' => 'nullable|string',
            'skills' => 'nullable|array',
            'source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $candidate = Candidate::create([
            ...$validated,
            'organization_id' => $request->user()->organization_id,
            'status' => 'pending',
        ]);

        return response()->json(['data' => $candidate, 'message' => 'Candidate added'], 201);
    }

    public function show(Candidate $candidate): JsonResponse
    {
        $this->authorizeOrg($candidate);

        return response()->json(['data' => $candidate]);
    }

    public function update(Request $request, Candidate $candidate): JsonResponse
    {
        $this->authorizeOrg($candidate);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'applied_role' => 'sometimes|string',
            'cv_text' => 'nullable|string',
            'skills' => 'nullable|array',
            'status' => 'in:pending,review,shortlisted,interview,rejected,hired',
            'notes' => 'nullable|string',
        ]);

        $candidate->update($validated);

        return response()->json(['data' => $candidate, 'message' => 'Candidate updated']);
    }

    public function destroy(Candidate $candidate): JsonResponse
    {
        $this->authorizeOrg($candidate);
        $candidate->delete();

        return response()->json(['message' => 'Candidate deleted']);
    }

    public function saveAnalysis(Request $request, Candidate $candidate): JsonResponse
    {
        $this->authorizeOrg($candidate);

        $validated = $request->validate([
            'ai_score' => 'required|integer|min:0|max:100',
            'ai_analysis' => 'required|string',
            'ai_strengths' => 'nullable|array',
            'ai_weaknesses' => 'nullable|array',
            'skills' => 'nullable|array',
            'recommendation' => 'in:shortlist,review,reject',
        ]);

        $statusMap = [
            'shortlist' => 'shortlisted',
            'review' => 'review',
            'reject' => 'rejected',
        ];

        $candidate->update([
            'ai_score' => $validated['ai_score'],
            'ai_analysis' => $validated['ai_analysis'],
            'ai_strengths' => $validated['ai_strengths'] ?? [],
            'ai_weaknesses' => $validated['ai_weaknesses'] ?? [],
            'skills' => $validated['skills'] ?? $candidate->skills,
            'status' => $statusMap[$validated['recommendation']] ?? $candidate->status,
        ]);

        NotificationService::candidateAnalyzed(
            $request->user()->id,
            $request->user()->organization_id,
            $candidate->name,
            $validated['ai_score']
        );

        return response()->json(['data' => $candidate, 'message' => 'Analysis saved']);
    }

    public function stats(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        return response()->json([
            'data' => [
                'total' => Candidate::where('organization_id', $orgId)->count(),
                'byStatus' => Candidate::where('organization_id', $orgId)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'avgScore' => (int) Candidate::where('organization_id', $orgId)
                    ->whereNotNull('ai_score')
                    ->avg('ai_score'),
                'analyzed' => Candidate::where('organization_id', $orgId)
                    ->whereNotNull('ai_score')
                    ->count(),
            ],
        ]);
    }

    private function authorizeOrg(Candidate $candidate): void
    {
        if ($candidate->organization_id !== auth()->user()?->organization_id) {
            abort(403, 'Access denied');
        }
    }
}
