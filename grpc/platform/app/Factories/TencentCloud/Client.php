<?php

declare(strict_types=1);

namespace App\Factories\TencentCloud;

use TencentCloud\Common\Credential;
use TencentCloud\Faceid\V20180301\FaceidClient;
use Modules\Examinee\Entities\Examinee;
use TencentCloud\Common\Profile\ClientProfile;

abstract class Client
{
    /**
     * The TencentCloud client.
     *
     * @var \TencentCloud\Faceid\V20180301\FaceidClient
     */
    protected $client;

    /**
     * The TencentCloud region name.
     *
     * @var string|null
     */
    protected $region;

    abstract public function handle(string $videoFile, Examinee $user);

    /**
     * get client instance.
     *
     * @param  string  $region
     * @return mixed
     */
    public function client()
    {
        if (! $this->client instanceof \TencentCloud\Faceid\V20180301\FaceidClient || $this->client->getRegion() != $this->region) {
            $cred = new Credential(
                config('face.tencent_could_api_key'),
                config('face.tencent_could_secret')
            );

            $clientProfile = new ClientProfile();
            $clientProfile->setSignMethod('TC3-HMAC-SHA256');

            $this->client = new FaceidClient($cred, $this->region ?? 'ap-guangzhou', $clientProfile);
        }

        return $this->client;
    }

    /**
     * Get the region.
     *
     * @return string|null
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set the region.
     *
     * @param  string  $region
     * @return $this
     */
    public function setRegion(string $region)
    {
        $this->region = $region;

        return $this;
    }
}
