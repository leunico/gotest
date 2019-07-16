<?php

namespace Modules\Examinee\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Examinee\Entities\Examinee;
use Modules\Examination\Entities\ExaminationExaminee;
use Illuminate\Support\Facades\DB;
use function App\validateChinaPhoneNumber;
use Modules\Examinee\Exceptions\ExcelImportExamineeException;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExamineeImport implements ToCollection
{
    /**
     * 考试数据
     *
     * @var \Modules\Examination\Entities\ExaminationExaminee
     */
    protected $eexaminee;

    /**
     * 考试数据
     *
     * @var integer
     */
    protected $examinationID;

    public function __construct(ExaminationExaminee $eexaminee, int $examinationID)
    {
        $this->eexaminee = $eexaminee;
        $this->examinationID = $examinationID;
    }

    /**
     * @param array $row
     * @return Examinee|null
     * @throws ExcelImportExamineeException
     */
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        $rows->shift();
        foreach ($rows as $row) {
            if (empty($row[3])) {
                throw new ExcelImportExamineeException('添加失败，有证件号码不存在的数据，请检查后重新上传！');
            }
            if ($rows->where(3, trim($row[3]))->count() > 1) {
                throw new ExcelImportExamineeException($row[3] . '证件重复，请检查后重新上传！');
            }
            $examinee = Examinee::firstOrNew(['certificates' => $this->handleCertificates((string) $row[3])])
                ->load([
                    'examinationPivots:id,examinee_id,examination_id,status'
                ]);
            if ($examinee->examinationPivots->isNotEmpty() && 
                $examinee->examinationPivots->where('status', ExaminationExaminee::STATUS_OK)->contains('examination_id', $this->examinationID)) {
                throw new ExcelImportExamineeException($row[3] . '证件号已存在，并且已确认。请检查后重新上传！');
            }
            
            $examinee->name = $this->handleName((string) $row[0]);
            $examinee->sex = $this->handleSex((string) $row[1]);
            $examinee->contacts = $row[5] ?? '';
            $examinee->phone = $this->handlePhone($row[6]);
            $examinee->email = $this->handleEmail($row[7]);
            $examinee->certificate_type = $this->handleCertificateType((string) $row[2]);
            $examinee->certificates_photos = $this->handleCertificatesPhotos((string) $row[9], (string) $row[10]);
            $examinee->photo = $this->handlePhoto((string) $row[8]);
            $examinee->birth = $this->handleBirth($row[4]);
            $examinee->remarks = $this->handleRemark($row[11]);
            $examinee->creator_id = Auth::user()->id;
            $examinee->password = Hash::make(substr($row[3], -6));
            if ($examinee->save()) {
                if (! $examinee->examinationPivots->contains('examination_id', $this->examinationID)) {
                    $examinee->examinations()->attach($this->examinationID, [
                        'admission_ticket' => $this->eexaminee->getOnlyAdmissionTicket($this->examinationID)
                    ]);
                }
            } else {
                // DB::rollBack();
                throw new ExcelImportExamineeException($row[3] . '添加失败，请检查后重新上传！');
            }
        }
        DB::commit();
    }

    /**
     * @param mixed $val
     * @return mixed
     * @throws ExcelImportExamineeException
     */
    public function handleBirth($val)
    {
        if (is_numeric($val)) {
            return gmdate('Y-m-d', intval(($val - 25569) * 3600 * 24));
        } else {
            return trim($val);
        }
    }

    /**
     * @param string $val
     * @return string
     * @throws ExcelImportExamineeException
     */
    public function handleRemark(string $val)
    {
        if (empty($val) || strlen($val) > 255) {
            throw new ExcelImportExamineeException($val . '：考试事务咨询联络老师为空或者太长');
        }

        return trim($val);
    }

    /**
     * @param string $val1
     * @param string $val2
     * @return string
     * @throws ExcelImportExamineeException
     */
    public function handleCertificatesPhotos(string $val1, string $val2)
    {
        if (empty($val1) || empty($val2)) {
            throw new ExcelImportExamineeException('证件照片不全，请检查后重新上传！');
        }

        return json_encode([trim($val1), trim($val2)]);
    }

    /**
     * @param string $val
     * @return string
     * @throws ExcelImportExamineeException
     */
    public function handlePhoto(string $val)
    {
        if (empty($val) || ! preg_match('/.*(\.png|\.jpg|\.jpeg|\.gif)$/', $val)) {
            throw new ExcelImportExamineeException($val . '：考生照片不规范，请检查后重新上传！');
        }

        return trim($val);
    }

    /**
     * @param string $val
     * @return intger
     * @throws ExcelImportExamineeException
     */
    public function handleCertificates(string $val)
    {
        if (strlen($val) > 30 || strlen($val) < 6 ) {
            throw new ExcelImportExamineeException($val . '：证件号码不规范，请检查后重新上传！');
        }

        return trim($val);
    }

    /**
     * @param string $val
     * @return string
     * @throws ExcelImportExamineeException
     */
    public function handleName(string $val)
    {
        if (empty($val) || strlen($val) > 100) {
            throw new ExcelImportExamineeException($val . '：名字为空或者不合规范，请检查后重新上传！');
        }

        return trim($val);
    }

    /**
     * @param string $val
     * @return intger
     */
    public function handleSex(string $val)
    {
        return $val == '男' ? 1 : ($val == '女' ? 2 : 0);
    }

    /**
     * @param string $val
     * @return intger|void
     * @throws ExcelImportExamineeException
     */
    public function handleCertificateType(string $val)
    {
        switch ($val) {
            case '身份证':
                return 1;
                break;
            case '护照':
                return 2;
                break;
            case '学籍号码':
                return 3;
                break;
            case '户口本':
                return 4;
                break;
            default:
                throw new ExcelImportExamineeException($val . '：不存在的证件类型，请检查后重新上传！');
                break;
        }
    }

    /**
     * @param string|int $val
     * @return intger
     * @throws ExcelImportExamineeException
     */
    public function handlePhone($val)
    {
        if (! validateChinaPhoneNumber($val)) {
            throw new ExcelImportExamineeException($val . '：不是标准手机号码，请检查后重新上传！');
        }

        return (string) $val;
    }

    /**
     * @param string|int $val
     * @return intger
     * @throws ExcelImportExamineeException
     */
    public function handleEmail($val)
    {
        if (! (new EmailValidator)->isValid($val, new RFCValidation)) {
            throw new ExcelImportExamineeException($val . '：不是标准邮箱，请检查后重新上传！');
        }

        return (string) $val;
    }
}
