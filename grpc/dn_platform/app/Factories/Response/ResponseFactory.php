<?php

declare(strict_types=1);

namespace App\Factories\Response;

use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\AbstractPaginator;
use App\Traits\Transform;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection as BaseCollection;

class ResponseFactory
{
    use Transform;

    /**
     * @param  null|string|array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = null): JsonResponse
    {
        return response()->json($data, Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param  string $location 资源的地址
     * @param  null|array $content
     * @return \Illuminate\Http\JsonResponse
     */
    public function created(string $location = '', $content = null): JsonResponse
    {
        $headers = [];

        if ($location !== '') {
            $headers['Location'] = $location;
        }

        return response()->json($content, Response::HTTP_CREATED, $headers, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $location 资源的地址
     * @param null|array $content
     * @return \Illuminate\Http\JsonResponse
     */
    public function accepted(string $location = '', $content = null): JsonResponse
    {
        $headers = [];

        if ($location !== '') {
            $headers['Location'] = $location;
        }

        return response()->json($content, Response::HTTP_ACCEPTED, $headers, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @param string $className
     * @return \Illuminate\Http\JsonResponse
     */
    public function item(?Model $model, string $className): JsonResponse
    {
        if (is_null($model)) {
            return $this->success();
        }

        return $this->success($this->transformItem($model, $className));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @param string $className
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(Collection $collection, string $className): JsonResponse
    {
        return $this->success($this->transformCollection($collection, $className));
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     * @param string $className
     * @return \Illuminate\Http\JsonResponse
     */
    public function baseCollection(BaseCollection $collection, string $className): JsonResponse
    {
        return $this->success($this->transformBaseCollection($collection, $className));
    }

    /**
     * @param \Illuminate\Pagination\AbstractPaginator $paginator
     * @param string $className
     * @param array $other
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginator(AbstractPaginator $paginator, string $className, array $other = []): JsonResponse
    {
        return $this->success($this->transformPaginator($paginator, $className, $other));
    }

    /**
     * @param null|string|array $message
     * @param integer $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message = null, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json($message, $statusCode, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param null|string $message
     * @param integer $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorMsg($message = null, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(['message' => $message], $statusCode, [], JSON_UNESCAPED_UNICODE);
    }
    /**
    * @param null|string|array $message
    * @param integer $statusCode
    * @return \Illuminate\Http\JsonResponse
    */
    public function errorServer($message = null, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return response()->json($message, $statusCode, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorNotFound(string $message = ''): JsonResponse
    {
        return $this->error($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorBadRequest(string $message = ''): JsonResponse
    {
        return $this->error($message, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorForbidden($message = '', $data = ''): JsonResponse
    {
        $res = [
            'message' => $message,
            'data' => $data,
        ];

        return $this->error($res, Response::HTTP_FORBIDDEN);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorUnprocessableEntity($message = '', $data = ''): JsonResponse
    {
        $res = [
            'message' => $message,
            // 'data' => $data,
        ];

        return $this->error($res, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorInternal(string $message = ''): JsonResponse
    {
        return $this->error($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorUnauthorized(string $message = ''): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorMethodNotAllowed(string $message = ''): JsonResponse
    {
        return $this->error($message, Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
