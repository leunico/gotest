<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class ArrayUnique implements Rule
{
    /**
     * model
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * validation type
     *
     * @var integer
     */
    protected $is_key;

    /**
     * model del ?
     *
     * @var integer
     */
    protected $is_del;

    /**
     * validation field
     *
     * @var string|null
     */
    protected $field;

    /**
     * construct
     *
     * @param \Illuminate\Database\Eloquent\Model|\Doctrine\DBAL\Query\QueryBuilder $model
     * @param string|null $field
     * @param boolean $is_key
     * @param boolean $is_del
     */
    public function __construct($model, ?string $field = null, bool $is_key = false, bool $is_del = false)
    {
        $this->is_key = $is_key;
        $this->field = $field;
        $this->model = $model;
        $this->is_del = $is_del;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! is_array($value)) {
            return false;
        }

        if (empty($value)) {
            return true;
        }

        return $this->model
            ->whereIn($this->field ?? $attribute, $this->is_key ? array_keys($value) : array_values($value))
            ->when($this->is_del, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get()
            ->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '模型有已经存在的数据';
    }
}
