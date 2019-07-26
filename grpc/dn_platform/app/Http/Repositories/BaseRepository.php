<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository
{
    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $model;

    public function __construct()
    {
        $this->model = $this->model();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        return null;
    }

    /**
     * @param  string|array|\Closure  $column
     * @param  mixed   $operator
     * @param  mixed   $value
     * @param  string  $boolean
     * @return \App\Http\Repositories\BaseRepository
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->model->where($column, $operator, $value, $boolean);

        return $this;
    }

    /**
     * @param  string  $column
     * @param  mixed   $values
     * @param  string  $boolean
     * @param  bool    $not
     * @return \App\Http\Repositories\BaseRepository
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->model->whereIn($column, $values, $boolean, $not);

        return $this;
    }

    /**
     * @param  string  $column
     * @param  string  $direction
     * @return \App\Http\Repositories\BaseRepository
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * @param  array|string  $relations
     * @return \App\Http\Repositories\BaseRepository
     */
    public function with($relations)
    {
        $this->model->with($relations);

        return $this;
    }

    /**
     * Add subselect queries to count the relations.
     *
     * @param  mixed  $relations
     * @return \App\Http\Repositories\BaseRepository
     */
    public function withCount($relations)
    {
        $this->model->withCount($relations);

        return $this;
    }

    /**
     * Set the columns to be selected.
     *
     * @param  array|mixed  $columns
     * @return \App\Http\Repositories\BaseRepository
     */
    public function select($columns = ['*'])
    {
        $this->model->select($columns);

        return $this;
    }

    /**
     * @param  array|string  $relations
     * @return \App\Http\Repositories\BaseRepository
     */
    public function distinct()
    {
        $this->model->distinct();

        return $this;
    }

    /**
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function first($columns = ['*']): ?Model
    {
        return $this->model->first($columns);
    }

    /**
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    public function get(array $columns = ['*']): Collection
    {
        return $this->model->get($columns);
    }

    /**
     * @param integer|null $perPage
     * @param string $columns
     * @param string $pageName
     * @param integer|null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = '*', string $pageName = 'page', $page = null)
    {
        $count = $this->model->count($columns);

        $paginate = $this->model->paginate($perPage, ['*'], $pageName, $page);

        return new LengthAwarePaginator(
            $paginate->getCollection(),
            $count,
            $paginate->perPage(),
            $paginate->currentPage()
        );
    }

    /**
     * @param  string  $columns
     * @return int
     */
    public function count($columns = '*'): int
    {
        return $this->model->count($columns);
    }
}
