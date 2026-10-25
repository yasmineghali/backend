<?php

namespace App\Http\Controllers\Api\Bimrevo;

use App\Http\Controllers\Controller;
use App\Models\Bimrevo\PageView;
use App\Support\UserAgent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrackController extends Controller
{
    public function store(Request $request): Response
    {
        $noContent = response()->noContent();

        if (! config('bimrevo.analytics.enabled')) {
            return $noContent;
        }

        // Honour Do Not Track and Global Privacy Control.
        if ($request->header('DNT') === '1' || $request->header('Sec-GPC') === '1') {
            return $noContent;
        }

        $userAgent = $request->userAgent();
        if (UserAgent::isBot($userAgent)) {
            return $noContent;
        }

        $data = $request->validate([
            'path' => ['required', 'string', 'max:300'],
            'referrer' => ['nullable', 'string', 'max:300'],
        ]);

        $path = $this->normalizePath($data['path']);
        if ($path === null) {
            return $noContent;
        }

        PageView::create([
            'visitor_hash' => $this->visitorHash($request->ip(), (string) $userAgent),
            'path' => $path,
            'referrer_host' => $this->referrerHost($data['referrer'] ?? null),
            'device' => UserAgent::device($userAgent),
            'viewed_at' => now(),
        ]);

        return $noContent;
    }

    /**
     * Keyed hash of IP + User-Agent + the current date. The date makes it rotate
     * every day, so a visitor cannot be followed across days; the IP is never stored.
     */
    private function visitorHash(?string $ip, string $userAgent): string
    {
        return substr(
            hash_hmac('sha256', $ip.'|'.$userAgent, config('app.key').'|bimrevo|'.now()->toDateString()),
            0,
            32,
        );
    }

    /** Path only: query strings and fragments can carry personal data, except the blog `slug`. */
    private function normalizePath(string $raw): ?string
    {
        $parts = parse_url($raw);
        $path = $parts['path'] ?? '';

        if (isset($parts['scheme']) || isset($parts['host']) || $path === '' || $path[0] !== '/' || str_contains($path, '//')) {
            return null;
        }

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            $slug = $query['slug'] ?? null;
            if (is_string($slug) && preg_match('/^[a-z0-9-]{1,100}$/', $slug)) {
                $path .= '?slug='.$slug;
            }
        }

        return mb_substr($path, 0, 200);
    }

    private function referrerHost(?string $referrer): ?string
    {
        if (! $referrer) {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST) ?: ($referrer);
        $host = strtolower(preg_replace('/^www\./i', '', (string) $host));

        if (! preg_match('/^[a-z0-9.-]{3,100}$/', $host)) {
            return null;
        }

        foreach (config('bimrevo.analytics.own_hosts') as $own) {
            if ($host === $own || str_ends_with($host, '.'.$own)) {
                return null;
            }
        }

        return $host;
    }
}
