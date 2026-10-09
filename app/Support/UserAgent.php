<?php

namespace App\Support;

/** Classifies a User-Agent on the fly. The raw string is never stored. */
class UserAgent
{
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|scrape|fetch|monitor|uptime|headless|phantom|lighthouse|pagespeed|gtmetrix|pingdom|preview|facebookexternalhit|embedly|curl|wget|python-requests|python-urllib|httpclient|okhttp|go-http|java\/|libwww|axios|node-fetch|postman|insomnia|\bhttp\b/i';

    public static function isBot(?string $userAgent): bool
    {
        $userAgent = trim((string) $userAgent);

        return $userAgent === '' || preg_match(self::BOT_PATTERN, $userAgent) === 1;
    }

    /** @return 'mobile'|'tablet'|'desktop' */
    public static function device(?string $userAgent): string
    {
        $userAgent = (string) $userAgent;

        if (preg_match('/ipad|tablet|kindle|silk|playbook|(android(?!.*mobile))/i', $userAgent)) {
            return 'tablet';
        }

        if (preg_match('/mobi|iphone|ipod|android|blackberry|opera mini|iemobile|windows phone/i', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }
}
