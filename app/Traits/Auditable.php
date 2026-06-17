<?php

namespace App\Traits;

use App\Services\SystemLogService;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::logAction($model, 'CREATED');
        });

        static::updated(function (Model $model) {
            self::logAction($model, 'UPDATED');
        });

        static::deleted(function (Model $model) {
            self::logAction($model, 'DELETED');
        });
    }

    protected static function logAction(Model $model, string $action)
    {
        $logService = app(SystemLogService::class);
        
        $oldData = $action === 'UPDATED' || $action === 'DELETED' ? $model->getOriginal() : null;
        $newData = $action === 'CREATED' || $action === 'UPDATED' ? $model->getAttributes() : null;

        // Filter out hidden or excluded fields if necessary
        $module = self::getAuditModule($model);

        $logService->log(
            action: strtoupper($module . '_' . $action),
            module: strtolower($module),
            refType: $model->getTable(),
            refId: $model->getKey(),
            oldData: $oldData,
            newData: $newData
        );
    }

    protected static function getAuditModule(Model $model): string
    {
        $className = class_basename($model);
        return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    }
}
