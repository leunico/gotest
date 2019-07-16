<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection as BaseCollection;

trait Transform
{
    /**
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @param string $className
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function transformItem($model, string $className): ?JsonResource
    {
        if ($model instanceof Model) {
            return new $className($model);
        }

        return null;
    }

    /**
     * @param \Illuminate\Support\Collection|null $collection
     * @param string $className
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Support\Collection
     */
    public function transformCollection($collection, string $className)
    {
        if ($collection instanceof Collection) {
            return $className::collection($collection);
        }

        return collect([]);
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     * @param string $className
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function transformBaseCollection(BaseCollection $collection, string $className): AnonymousResourceCollection
    {
        return $className::collection($collection);
    }

    /**
     * @param \Illuminate\Pagination\AbstractPaginator $paginator
     * @param string $className
     * @param array $other
     * @return array
     */
    public function transformPaginator(AbstractPaginator $paginator, string $className = '', array $other = []): array
    {
        $response = [
            'pagination' => [
                'total' => (int) $paginator->total(),
                'current_page' => (int) $paginator->currentPage(),
                'last_page' => (int) $paginator->lastPage(),
                'per_page' => (int) $paginator->perPage(),
            ],
        ];

        $response['list'] = empty($className) ? $paginator->getCollection() : $this->transformCollection($paginator->getCollection(), $className);
        $response = ! empty($other) ? array_merge($response, $other) : $response;

        return $response;
    }
}
