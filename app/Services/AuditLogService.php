<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public static function log(string $action, ?string $description, ?Model $auditable = null, array $meta = []): AuditLog
    {
        $log = new AuditLog;
        $log->user_id = auth()->id();
        $log->action = $action;
        $log->description = $description;

        if ($auditable) {
            $log->auditable_type = $auditable->getMorphClass();
            $log->auditable_id = $auditable->getKey();
        }

        $log->ip_address = Request::ip();
        $log->user_agent = Request::userAgent();
        $log->meta = $meta ?: null;
        $log->save();

        return $log;
    }
}
