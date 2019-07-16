<?php

namespace Modules\Examinee\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\User;
use Modules\Examination\Entities\Examination;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Modules\Examination\Entities\ExaminationExaminee;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examinee extends Authenticatable implements JWTSubject
{
    // use SoftDeletes;

    const CERTIFICATE_TYPE_SFZ = 1;
    const CERTIFICATE_TYPE_HZ = 2;
    const CERTIFICATE_TYPE_XZHM = 3;
    const CERTIFICATE_TYPE_HKB = 4;
    const SOURCE_LR = 1;
    const STATUS_OK = 1;

    public static $certificateTypes = [
        self::CERTIFICATE_TYPE_SFZ => '身份证',
        self::CERTIFICATE_TYPE_HZ => '护照',
        self::CERTIFICATE_TYPE_XZHM => '学籍号码',
        self::CERTIFICATE_TYPE_HKB => '户口本'
    ];

    public $fillable = [
        'status',
        'certificates'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 判断是否身份证.
     *
     * @return boolen
     */
    public function isCertificateTypeOne()
    {
        return $this->certificate_type == self::CERTIFICATE_TYPE_SFZ;
    }

    /**
     * 获取用户的证件.
     *
     * @param  string  $value
     * @return string
     */
    public function getCertificatesPhotosAttribute($value)
    {
        return json_decode($value, true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function examinations()
    {
        return $this->belongsToMany(Examination::class, 'examination_examinees')
            ->withTimestamps();
    }

    public function examinationPivots()
    {
        return $this->hasMany(ExaminationExaminee::class);
    }

    public function examineeExaminationTestingPushs()
    {
        return $this->hasMany(ExamineePush::class)
            ->where('pushtable_type', 'testing_status');
    }

    public function examineeDeviceProbings()
    {
        return $this->hasMany(ExamineeDeviceProbing::class);
    }
}
