<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    use SpatieLogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => $this->getActivityDescription($eventName));
    }

    protected function getActivityDescription(string $eventName): string
    {
        $modelName = class_basename($this);
        $userName = Auth::check() ? Auth::user()->name : __('activity.system');
        
        $descriptions = [
            'created' => __('activity.created') . " {$modelName} " . __('by') . " {$userName}",
            'updated' => __('activity.updated') . " {$modelName} " . __('by') . " {$userName}",
            'deleted' => __('activity.deleted') . " {$modelName} " . __('by') . " {$userName}",
        ];

        return $descriptions[$eventName] ?? "{$eventName} {$modelName} " . __('by') . " {$userName}";
    }
}