<?php

declare(strict_types=1);

namespace App\Traits;

use App\OperationLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait OperationLogEnable
{
    public static $description = '表%s触发了%s事件';

    private static $logModel = null;

    /**
     * 要记录的事件
     *
     * @return void
     * @author lizx
     */
    public static function boot()
    {
        self::$logModel = new OperationLog;

        parent::boot();

        static::updated(function ($model) {
            self::notice($model, 'updated');
        });

        static::saved(function ($model) {
            self::notice($model, 'saved');
        });

        static::deleted(function ($model) {
            self::notice($model, 'deleted');
        });
    }

    /**
     * 添加记录
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $event
     * @return boolean
     * @author lizx
     */
    private static function notice(Model $model, $event = 'updated')
    {
        if ((isset(self::$logModel->id) && !empty(self::$logModel->id)) || $model instanceof OperationLog) {
            return false;
        }

        self::$logModel->setAttribute('table_name', $model->getTable());
        self::$logModel->user_name = Auth::user()->name ?? 'CLI';
        self::$logModel->user_id = Auth::id() ?? 0;
        self::$logModel->route = request()->path();
        self::$logModel->event = $event; // todo 是否多个事件更新 self::$logModel->event ?? $event
        self::$logModel->old = $model->getOriginal() ?? [];
        self::$logModel->new = $model->toArray();
        self::$logModel->model_id = $model->id;
        self::$logModel->description = $model->logNotice ?? sprintf(self::$description, $model->getTable(), $event);

        return self::$logModel->save();
    }
}
