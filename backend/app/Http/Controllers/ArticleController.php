<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Article::where('organization_id', $request->user()->organization_id)
            ->with('author:id,name')
            ->latest();

        if ($request->filled('category') && $request->input('category') !== 'All') {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'ilike', "%{$term}%")
                  ->orWhere('content', 'ilike', "%{$term}%")
                  ->orWhere('excerpt', 'ilike', "%{$term}%");
            });
        }

        $published = $request->boolean('published', false);
        if ($published) {
            $query->whereNotNull('published_at');
        }

        $articles = $query->paginate(20);

        return response()->json([
            'data' => $articles->map(fn (Article $a) => $this->formatArticle($a)),
            'meta' => [
                'total' => $articles->total(),
                'page' => $articles->currentPage(),
                'perPage' => $articles->perPage(),
            ],
        ]);
    }

    public function categories(Request $request): JsonResponse
    {
        $categories = Article::where('organization_id', $request->user()->organization_id)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json(['data' => $categories]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'published' => 'boolean',
        ]);

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $count = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        $article = Article::create([
            'organization_id' => $request->user()->organization_id,
            'author_id' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 160),
            'category' => $validated['category'],
            'published_at' => ($validated['published'] ?? false) ? now() : null,
        ]);

        if ($article->published_at) {
            NotificationService::articlePublished(
                $request->user()->id,
                $request->user()->organization_id,
                $article->title
            );
        }

        return response()->json(['data' => $this->formatArticle($article->load('author:id,name')), 'message' => 'Article created'], 201);
    }

    public function show(Article $article): JsonResponse
    {
        $this->authorizeOrg($article);
        $article->increment('views');

        return response()->json(['data' => $this->formatArticle($article->load('author:id,name'))]);
    }

    public function update(Request $request, Article $article): JsonResponse
    {
        $this->authorizeOrg($article);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'sometimes|string|max:100',
            'published' => 'boolean',
        ]);

        $wasUnpublished = $article->published_at === null;

        if (isset($validated['title']) && $validated['title'] !== $article->title) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $count = 1;
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }
            $validated['slug'] = $slug;
        }

        if (isset($validated['published'])) {
            $validated['published_at'] = $validated['published'] ? ($article->published_at ?? now()) : null;
            unset($validated['published']);
        }

        $article->update($validated);

        if ($wasUnpublished && $article->published_at) {
            NotificationService::articlePublished(
                $request->user()->id,
                $request->user()->organization_id,
                $article->title
            );
        }

        return response()->json(['data' => $this->formatArticle($article), 'message' => 'Article updated']);
    }

    public function destroy(Article $article): JsonResponse
    {
        $this->authorizeOrg($article);
        $article->delete();

        return response()->json(['message' => 'Article deleted']);
    }

    private function formatArticle(Article $article): array
    {
        return [
            'id' => (string) $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'category' => $article->category,
            'views' => $article->views,
            'published' => $article->published_at !== null,
            'publishedAt' => $article->published_at?->toISOString(),
            'createdAt' => $article->created_at?->toISOString(),
            'updatedAt' => $article->updated_at?->toISOString(),
            'author' => $article->relationLoaded('author') ? [
                'id' => (string) $article->author?->id,
                'name' => $article->author?->name,
            ] : null,
            'updatedAgo' => $article->updated_at?->diffForHumans(),
        ];
    }

    private function authorizeOrg(Article $article): void
    {
        if ($article->organization_id !== auth()->user()?->organization_id) {
            abort(403, 'Access denied');
        }
    }
}
