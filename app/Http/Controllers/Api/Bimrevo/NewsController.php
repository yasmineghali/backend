<?php

namespace App\Http\Controllers\Api\Bimrevo;

use App\Http\Controllers\Controller;
use App\Models\Bimrevo\NewsPost;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = NewsPost::published()
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (NewsPost $post) => $this->summary($post));

        return response()->json(['data' => $posts]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = NewsPost::published()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'data' => $this->summary($post) + ['content' => $post->content],
        ]);
    }

    private function summary(NewsPost $post): array
    {
        return [
            'slug' => $post->slug,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'image_url' => $post->imageUrl(),
            'image_alt' => $post->image_alt ?: $post->title,
            'author' => $post->author,
            'published_at' => $post->published_at?->toIso8601String(),
        ];
    }
}
