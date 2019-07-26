<?php

declare(strict_types=1);

namespace Modules\Educational\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Modules\Educational\Plugins\Agora\SimpleTokenBuilder;
use Modules\Educational\Plugins\Agora\SignalingToken;
use Modules\Educational\Entities\BiuniqueAppointment;

class LiveChatController extends Controller
{
    /**
     * @var integer
     */
    private $expireSeconds;

    /**
     * @var integer
     */
    private $msgExpireSeconds;

    /**
     * @var array
     */
    private $liveConfig;

    public function __construct()
    {
        $this->liveConfig = config('educational.live');

        $this->expireSeconds = 60 * 60; //Token有效期，一节课40分钟，有效设为60分钟先

        $this->msgExpireSeconds = 100000000; //Msg Token有效期
    }

    /**
     * 获取Token.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\BiuniqueAppointment $appointment
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function token(Request $request, BiuniqueAppointment $appointment): JsonResponse
    {
        $user = $request->user();
        $data = [
            'expire_timestamp' => Carbon::now()->addSeconds($this->expireSeconds)->timestamp,
            'app_id' => $this->liveConfig['app_id'],
            'channel_name' => $this->liveConfig['channel_prefix'] . $appointment->id . $request->input('channel_name', ''),
            'uid' => $user->id
        ];

        $builder = new SimpleTokenBuilder(
            $data['app_id'],
            $this->liveConfig['app_certificate'],
            $data['channel_name'],
            $user->id
        );

        $appointment->load([
            'teacherOfficeTime' => function ($query) {
                $query->select('id', 'user_id');
            }
        ]);
        if ($appointment->teacherOfficeTime && $appointment->teacherOfficeTime->user_id == $user->id) {
            $builder->initPrivilege(SimpleTokenBuilder::Role['kRolePublisher']);
            $data['role'] = 'publisher';
        } elseif ($appointment->user_id == $user->id) {
            $builder->initPrivilege(SimpleTokenBuilder::Role['kRoleAttendee']);
            $data['role'] = 'attendee';
        } else {
            return $this->response()->error('非法请求');
        }

        // 针对角色生成Token
        $data['token'] = $builder->buildToken();

        return $this->response()->success($data);
    }

    /**
     * 获取MsgToken.
     *
     * @param \Modules\Educational\Entities\BiuniqueAppointment $appointment
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function msgToken(BiuniqueAppointment $appointment): JsonResponse
    {
        $account = "{$appointment->id}_" . $this->user()->id;
        $data = [
            'app_id' => $this->liveConfig['app_id'],
            'token' => SignalingToken::getToken(
                $this->liveConfig['app_id'],
                $this->liveConfig['app_certificate'],
                $account,
                $this->msgExpireSeconds
            ),
            'expire_timestamp' => Carbon::now()->addSeconds($this->msgExpireSeconds)->timestamp,
            'account' => $account,
        ];

        return $this->response()->success($data);
    }
}
