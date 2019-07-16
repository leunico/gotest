<?php

namespace Modules\Examination\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ExaminationPaper\Entities\MarkingRecord;
use Modules\Examinee\Entities\Examinee;
use Modules\Examinee\Entities\ExamineeDeviceProbing;
use Modules\Examinee\Entities\ExamineeTencentFace;
use Modules\Examinee\Entities\ExamineeOperation;
use Modules\Examinee\Entities\FaceUser;
use Modules\Examinee\Entities\ExamineeVideo;

class ExaminationExaminee extends Model
{
    use SoftDeletes;

    const IS_HAND_OK = 1;
    const STATUS_OK = 1;
    const STATUS_OFF = 0;
    const TESTING_STATUS_OK = 1;
    const TESTING_STATUS_OFF = 2;
    const ACHIEVEMENT_STATUS_MARKING = 2;
    const ACHIEVEMENT_STATUS_OK = 1;
    const ACHIEVEMENT_STATUS_OFF = 0;
    const TESTING_STATUS_NO = 0;
    const IS_HAND_OFF = 0;

    public $fillable = [
        'is_hand',
        'admission_ticket',
        'testing_status'
    ];

    /**
     * 获取唯一准考证
     *
     * @param int $examinationId
     * @return integer
     */
    public function getOnlyAdmissionTicket(int $examinationId)
    {
        $ticket = str_pad($examinationId, 5, "0") . substr(time(), -2) . substr(microtime(), 2, 4);

        if ($this->where('admission_ticket', $ticket)->first()) {
            return $this->getOnlyAdmissionTicket($examinationId);
        } else {
            return $ticket;
        }
    }

    /**
     * 获取一场考试的统计
     *
     * @param int $examinationId
     * @return array
     */
    public function getExaminationtaStatistics(int $examinationId)
    {
        $eexaminee = $this->select('id', 'status', 'testing_status', 'achievement_status', 'is_hand')
            ->where('examination_id', $examinationId)
            ->get();

        $eexamineeStatus = $eexaminee->where('status', ExaminationExaminee::STATUS_OK);
        $statistics['status_ok'] = $eexaminee->isNotEmpty() ? $eexamineeStatus->count() :0;
        $statistics['status_off'] = $eexaminee->count() - $statistics['status_ok'];
        $statistics['testing_status_ok'] = $eexamineeStatus->isNotEmpty() ?
            $eexamineeStatus->where('testing_status', ExaminationExaminee::TESTING_STATUS_OK)->count() : 0;
        $statistics['testing_status_off'] = $eexamineeStatus->isNotEmpty() ?
            $eexamineeStatus->where('testing_status', ExaminationExaminee::TESTING_STATUS_OFF)->count() : 0;
        $statistics['testing_status_no'] = $statistics['status_ok'] - ($statistics['testing_status_ok'] + $statistics['testing_status_off']);
        $statistics['is_hand_ok'] = $eexamineeStatus->isNotEmpty() ?
            $eexamineeStatus->where('is_hand', ExaminationExaminee::IS_HAND_OK)->count() : 0;
        $statistics['is_hand_off'] = $statistics['status_ok'] - $statistics['is_hand_ok'];
        $statistics['achievement_status_ok'] = $eexamineeStatus->isNotEmpty() ?
            $eexamineeStatus->where('is_hand', ExaminationExaminee::IS_HAND_OK)
                ->where('achievement_status', ExaminationExaminee::ACHIEVEMENT_STATUS_OK)->count() : 0;
        $statistics['achievement_status_marking'] = $eexamineeStatus->isNotEmpty() ?
            $eexamineeStatus->where('is_hand', ExaminationExaminee::IS_HAND_OK)
                ->where('achievement_status', ExaminationExaminee::ACHIEVEMENT_STATUS_MARKING)->count() : 0;
        $statistics['achievement_status_off'] = $statistics['is_hand_ok'] - ($statistics['achievement_status_ok'] + $statistics['achievement_status_marking']);

        return $statistics;
    }

    public function markingRecord()
    {
        return $this->hasMany(MarkingRecord::class);
    }

    public function examinee()
    {
        return $this->belongsTo(Examinee::class);
    }

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function examineeDeviceProbings()
    {
        return $this->hasMany(ExamineeDeviceProbing::class);
    }

    public function examineeTencentFaces()
    {
        return $this->hasMany(ExamineeTencentFace::class);
    }

    public function examineeOperations()
    {
        return $this->hasMany(ExamineeOperation::class);
    }

    public function faceUsers()
    {
        return $this->hasMany(FaceUser::class);
    }

    public function examineeVideos()
    {
        return $this->hasMany(ExamineeVideo::class);
    }
}
