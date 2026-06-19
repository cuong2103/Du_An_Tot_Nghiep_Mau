<?php

namespace App\Observers;

use App\Services\SystemLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class GenericModelObserver
{
    protected $logService;

    public function __construct(SystemLogService $logService)
    {
        $this->logService = $logService;
    }

    public function created(Model $model)
    {
        $this->logChange($model, 'CREATED');
    }

    public function updated(Model $model)
    {
        $this->logChange($model, 'UPDATED');
    }

    public function deleted(Model $model)
    {
        $this->logChange($model, 'DELETED');
    }

    protected function logChange(Model $model, $actionSuffix)
    {
        $modelName = class_basename($model); // e.g., 'User', 'SystemSetting'
        $tableName = $model->getTable();
        
        $action = strtoupper($modelName) . '_' . $actionSuffix;
        
        // Define module mapping
        $moduleMap = [
            'users' => 'users',
            'appointments' => 'appointments',
            'doctor_profiles' => 'doctors',
            'system_settings' => 'settings',
            'posts' => 'posts',
        ];
        
        $module = $moduleMap[$tableName] ?? 'system';

        $oldData = [];
        $newData = [];

        if ($actionSuffix === 'CREATED') {
            $newData = $model->toArray();
        } elseif ($actionSuffix === 'UPDATED') {
            $oldData = $model->getOriginal();
            $newData = $model->getChanges();
            
            // Only keep the changed attributes in oldData to save space
            $oldData = array_intersect_key($oldData, $newData);
        } elseif ($actionSuffix === 'DELETED') {
            $oldData = $model->toArray();
        }

        // Clean sensitive data
        $oldData = $this->cleanSensitiveData($oldData);
        $newData = $this->cleanSensitiveData($newData);

        // Call the SystemLogService
        $this->logService->log(
            action: $action,
            module: $module,
            oldData: empty($oldData) ? null : $oldData,
            newData: empty($newData) ? null : $newData,
            refType: $tableName,
            refId: $model->getKey()
        );
    }

    protected function cleanSensitiveData(array $data)
    {
        $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];
        foreach ($hidden as $field) {
            if (isset($data[$field])) {
                $data[$field] = '********';
            }
        }
        return $data;
    }
}
