<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuditService
{
    /**
     * Registra uma ação no log de auditoria
     */
    public static function log(
        string $action,
        string $resourceType,
        ?int $resourceId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null
    ): AuditLog {
        $user = Auth::user();
        $request = request();

        return AuditLog::create([
            'user_id' => $user?->id,
            'user_email' => $user?->email ?? 'system',
            'user_role' => $user?->role,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Registra criação de recurso
     */
    public static function logCreate(string $resourceType, int $resourceId, array $data): AuditLog
    {
        return self::log(
            'create',
            $resourceType,
            $resourceId,
            "Criado {$resourceType} #{$resourceId}",
            null,
            $data
        );
    }

    /**
     * Registra atualização de recurso
     */
    public static function logUpdate(string $resourceType, int $resourceId, array $old, array $new): AuditLog
    {
        return self::log(
            'update',
            $resourceType,
            $resourceId,
            "Atualizado {$resourceType} #{$resourceId}",
            $old,
            $new
        );
    }

    /**
     * Retorna logs de um usuário específico
     */
    public static function getByUser(int $userId)
    {
        return AuditLog::byUser($userId)->get();
    }

    /**
     * Retorna logs filtrados por ação
     */
    public static function getByAction(string $action)
    {
        return AuditLog::byAction($action)->get();
    }
}
