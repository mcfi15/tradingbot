<?php

namespace App\Services\Background;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * Market Rates & Sitemap Updater Worker
 *
 * Automatically refreshes dynamic search engine sitemaps, updates robots.txt,
 * and maintains asset indexing freshness.
 */
class ResourceIndexWorker
{
    /**
     * Unique identifier for health tracking in cron_jobs table.
     */
    public const IDENTIFIER = 'resource_indexer';

    /**
     * Generate sitemap and update search engine indexing directives.
     */
    public function execute(): void
    {
        $routes = Route::getRoutes();
        $urls = [];

        foreach ($routes as $route) {
            if (!in_array('GET', $route->methods())) {
                continue;
            }

            $uri = $route->uri();
            $name = $route->getName();

            // Exclude internal/framework routes
            if (
                str_starts_with($uri, '_') ||
                str_starts_with($uri, 'sanctum') ||
                str_starts_with($uri, 'api') ||
                str_starts_with($uri, 'telescope') ||
                str_starts_with($uri, 'horizon') ||
                str_starts_with($uri, 'up')
            ) {
                continue;
            }

            // Exclude protected / admin / user / utility paths
            if (
                str_starts_with($uri, 'admin') ||
                str_starts_with($uri, 'user') ||
                str_starts_with($uri, 'utils') ||
                str_starts_with($uri, 'storage') ||
                ($name && (
                    str_starts_with($name, 'admin.') ||
                    str_starts_with($name, 'user.') ||
                    str_starts_with($name, 'utils.') ||
                    str_starts_with($name, 'api.')
                ))
            ) {
                continue;
            }

            if ($uri === 'effects-preview' || $uri === 'lang/{locale}') {
                continue;
            }

            $cleanUri = preg_replace('/\{[^\}]+\?\}/', '', $uri);
            $cleanUri = rtrim($cleanUri, '/');
            $url = url($cleanUri);

            if (!in_array($url, $urls)) {
                $urls[] = $url;
            }
        }

        if (empty($urls)) {
            return;
        }

        $xml = $this->buildXml($urls);
        $path = public_path('sitemap.xml');

        if (file_put_contents($path, $xml) !== false) {
            $this->updateRobotsTxt();

            if (function_exists('updateLastCronJob')) {
                updateLastCronJob(self::IDENTIFIER);
            }
        }
    }

    /**
     * Update robots.txt with sitemap reference.
     */
    protected function updateRobotsTxt(): void
    {
        $path = public_path('robots.txt');
        $sitemapUrl = url('sitemap.xml');

        $content = 'User-agent: *' . PHP_EOL;
        $content .= 'Disallow: /admin' . PHP_EOL;
        $content .= 'Disallow: /user' . PHP_EOL;
        $content .= 'Disallow: /api' . PHP_EOL;
        $content .= 'Disallow: /utils' . PHP_EOL;
        $content .= 'Disallow: /storage' . PHP_EOL . PHP_EOL;
        $content .= "Sitemap: {$sitemapUrl}" . PHP_EOL;

        file_put_contents($path, $content);
    }

    /**
     * Build the sitemap XML string.
     */
    protected function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . now()->toAtomString() . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . ($url === url('/') ? '1.0' : '0.8') . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
