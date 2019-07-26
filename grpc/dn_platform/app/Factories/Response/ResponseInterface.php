<?php

declare(strict_types=1);

namespace App\Factories\Response;

use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\JsonResponse;

interface ResponseInterface
{
    public function success($data = null): JsonResponse;

    public function created(string $location = '', $content = null): JsonResponse;

    public function accepted(string $location = '', $content = null): JsonResponse;

    public function noContent(): JsonResponse;

    public function item(Model $model, string $className): JsonResponse;

    public function collection(Collection $collection, string $className): JsonResponse;

    public function paginator(AbstractPaginator $paginator, string $className): JsonResponse;

    public function error($message = null, int $status = Response::HTTP_BAD_REQUEST): JsonResponse;

    public function errorNotFound(string $message = ''): JsonResponse;

    public function errorBadRequest(string $message = ''): JsonResponse;

    public function errorForbidden(string $message = ''): JsonResponse;

    public function errorInternal(string $message = ''): JsonResponse;

    public function errorUnauthorized(string $message = ''): JsonResponse;

    public function errorMethodNotAllowed(string $message = ''): JsonResponse;
}
