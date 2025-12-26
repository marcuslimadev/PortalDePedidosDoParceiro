<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    public function handle(Request $request, Closure $next, string $action, string $resourceType): Response
    {
        $response = $next($request);

        // Registrar apenas se a requisição foi bem-sucedida
        if ($response->isSuccessful() && $request->user()) {
            AuditService::log(
                $action,
                $resourceType,
                $request->route('id'),
                $request->method() . ' ' . $request->path()
            );
        }

        return $response;
    }
}

