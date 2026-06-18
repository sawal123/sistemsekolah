<?php

namespace App\Http\Middleware;

use App\Models\WebsiteVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackWebsiteVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request)) {
            [$isSuspicious, $type, $reason] = $this->detectThreat($request);

            WebsiteVisit::create([
                'ip_address' => $request->ip(),
                'method' => $request->method(),
                'path' => '/' . ltrim($request->path(), '/'),
                'full_url' => $request->fullUrl(),
                'referer' => $request->headers->get('referer'),
                'user_agent' => $request->userAgent(),
                'status_code' => $response->getStatusCode(),
                'is_suspicious' => $isSuspicious,
                'threat_type' => $type,
                'threat_reason' => $reason,
                'visited_at' => now(),
            ]);
        }

        return $response;
    }

    private function shouldTrack(Request $request): bool
    {
        if (app()->runningUnitTests()) {
            return false;
        }

        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->is('livewire*') || $request->is('storage*') || $request->is('build*')) {
            return false;
        }

        if ($request->is('favicon.ico') || $request->is('robots.txt')) {
            return false;
        }

        return true;
    }

    private function detectThreat(Request $request): array
    {
        $target = strtolower(urldecode($request->fullUrl() . ' ' . ($request->userAgent() ?? '')));

        $patterns = [
            'SQL Injection' => ["' or ", '" or ', ' union select', 'information_schema', 'sleep(', 'benchmark(', 'drop table', '--'],
            'XSS' => ['<script', '%3cscript', 'javascript:', 'onerror=', 'onload='],
            'Path Traversal' => ['../', '..\\', '%2e%2e', '/etc/passwd', 'boot.ini'],
            'Sensitive File Probe' => ['/.env', '.git/config', 'composer.json', 'wp-config.php', 'config.php'],
            'Admin Probe' => ['wp-admin', 'wp-login', '/phpmyadmin', '/adminer', '/cpanel'],
            'Scanner/Bot' => ['sqlmap', 'nikto', 'acunetix', 'nessus', 'masscan', 'nmap scripting engine'],
        ];

        foreach ($patterns as $type => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($target, $needle)) {
                    return [true, $type, "Terdeteksi pola {$type}: {$needle}"];
                }
            }
        }

        return [false, null, null];
    }
}
