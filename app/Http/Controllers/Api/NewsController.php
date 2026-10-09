<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = $this->locale($request);

        $posts = NewsPost::published()
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (NewsPost $post) => $this->summary($post, $locale));

        return response()->json(['data' => $posts]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $this->locale($request);
        $post = NewsPost::published()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'data' => $this->summary($post, $locale) + [
                'content' => $post->localized('content', $locale),
            ],
        ]);
    }

    private function locale(Request $request): string
    {
        $locale = strtolower(substr((string) $request->query('locale', 'fr'), 0, 2));

        return in_array($locale, NewsPost::LOCALES, true) ? $locale : 'fr';
    }

    private function summary(NewsPost $post, string $locale): array
    {
        return [
            'slug' => $post->slug,
            'title' => $post->localized('title', $locale),
            'excerpt' => $post->localized('excerpt', $locale),
            'image_url' => $post->imageUrl(),
            'image_alt' => $post->image_alt ?: $post->localized('title', $locale),
            'published_at' => $post->published_at?->toIso8601String(),
        ];
    }
}
