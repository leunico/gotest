<?php

declare(strict_types=1);

namespace App\Factories\Face\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Log;
use App\Factories\Face\Face;

trait FaceClient
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * @var \GuzzleHttp\HandlerStack
     */
    protected $handlerStack;

    /**
     * @var array
     */
    public $configs;

    /**
     * @var array
     */
    public $defaults;

    /**
     * BaseClient constructor.
     *
     * @author lizx
     */
    public function __construct()
    {
        $this->configs = [
            'api_key' => config('face.face_api_key'),
            'api_secret' => config('face.face_api_secret')
        ];
        $this->defaults = [
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
        ];
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array  $query
     * @return array|object|string
     */
    public function httpGet(string $url, array $query = [])
    {
        return $this->request($url, 'GET', ['query' => array_merge($query, $this->configs)]);
    }

    /**
     * POST request.
     *
     * @param string $url
     * @param array  $data
     * @return array|object|string
     */
    public function httpPost(string $url, array $data = [])
    {
        return $this->request($url, 'POST', ['form_params' => array_merge($data, $this->configs)]);
    }

    /**
     * JSON request.
     *
     * @param string       $url
     * @param string|array $data
     * @param array        $query
     * @return array|object|string
     */
    public function httpPostJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => array_merge($data, $this->configs)]);
    }

    /**
     * Upload file.
     *
     * @param string $url
     * @param array  $files
     * @param array  $form
     * @param array  $query
     * @return array|object|string
     */
    public function httpUpload(string $url, array $files = [], array $form = [], array $query = [])
    {
        $multipart = [];

        foreach ($files as $name => $path) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path, 'r'),
            ];
        }

        foreach (array_merge($form, $this->configs) as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        return $this->request($url, 'POST', ['query' => $query, 'multipart' => $multipart, 'connect_timeout' => 30, 'timeout' => 30, 'read_timeout' => 30]);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     * @param bool   $returnRaw
     * @return \Psr\Http\Message\ResponseInterface|array|object|string
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function request(string $url, string $method = 'GET', array $options = [], bool $returnRaw = false)
    {
        $options = array_merge($this->defaults, $options, ['handler' => $this->getHandlerStack()]);
        $options = $this->fixJsonIssue($options);

        try {
            $response = $this->getHttpClient()->request(strtoupper($method), $url, $options);
            $response->getBody()->rewind();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return ['status' => Face::HTTP_REQUEST_GUZZLE, 'error_message' => $e->getMessage()]; // 请求过程出错
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            return ['status' => Face::HTTP_REQUEST_ERROR, 'error_message' => $e->getMessage()];  // 其它出错
        }

        return $returnRaw ? $response : json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Return GuzzleHttp\ClientInterface instance.
     *
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        if (!($this->httpClient instanceof ClientInterface)) {
            $this->httpClient = $this->app['http_client'] ?? new Client();
        }

        return $this->httpClient;
    }

    /**
     * Build a handler stack.
     *
     * @return \GuzzleHttp\HandlerStack
     */
    public function getHandlerStack(): HandlerStack
    {
        return $this->handlerStack ?? HandlerStack::create();
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function fixJsonIssue(array $options): array
    {
        if (isset($options['json']) && is_array($options['json'])) {
            $options['headers'] = array_merge($options['headers'] ?? [], ['Content-Type' => 'application/json']);
            if (empty($options['json'])) {
                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_FORCE_OBJECT);
            } else {
                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_UNESCAPED_UNICODE);
            }
            unset($options['json']);
        }

        return $options;
    }
}
