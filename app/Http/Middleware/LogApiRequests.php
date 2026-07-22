<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = round((microtime(true) - $start) * 1000, 2);

        $method = $request->method();
        $uri = $request->path();
        $status = $response->getStatusCode();

        $body = null;
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $input = $request->except(['password', 'password_confirmation', 'current_password']);
            $body = json_encode($input);
        }

        $arrow = match (true) {
            $status >= 500 => "\033[31m{$status}\033[0m",
            $status >= 400 => "\033[33m{$status}\033[0m",
            $status >= 300 => "\033[36m{$status}\033[0m",
            default => "\033[32m{$status}\033[0m",
        };

        $methodColor = match ($method) {
            'GET' => "\033[36mGET\033[0m",
            'POST' => "\033[32mPOST\033[0m",
            'PUT' => "\033[33mPUT\033[0m",
            'PATCH' => "\033[35mPATCH\033[0m",
            'DELETE' => "\033[31mDELETE\033[0m",
            default => $method,
        };

        $line = "  {$methodColor}  /{$uri}  {$arrow}  {$duration}ms";

        if ($body) {
            $line .= "  \033[90m{$body}\033[0m";
        }

        Log::channel('stderr')->info($line);

        return $response;
    }
}
