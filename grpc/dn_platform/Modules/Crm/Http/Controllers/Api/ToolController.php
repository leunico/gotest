<?php

declare(strict_types=1);

namespace Modules\Crm\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Crm\Entities\Channel;
use Modules\Personal\Http\Resources\ChannelResource;

/**
 * 工具类，提供给其他模块的前端使用
 */
class ToolController extends Controller
{
    /**
     * 渠道来源
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function channel(): JsonResponse
    {
        $channels = Channel::get();

        return $this->response()->collection($channels, ChannelResource::class);
    }
}
