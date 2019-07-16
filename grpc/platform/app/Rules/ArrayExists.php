<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class ArrayExists implements Rule
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
     * @var string
     */
    protected $field;

    /**
     * Create a new rule instance.
     *
     * @param \Illuminate\Database\Eloquent\Model|\Doctrine\DBAL\Query\QueryBuilder $model
     * @param boolean $is_key
     * @param boolean $is_del
     * @param string $field
     * @return void
     */
    public function __construct($model, bool $is_key = false, bool $is_del = false, string $field = 'id')
    {
        $this->is_key = $is_key;
        $this->is_del = $is_del;
        $this->field = $field;
        $this->model = $model;
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

        $value = collect($value);
        if (is_array($value->first()) && $value->pluck(0)->isNotEmpty() && ! $this->is_key) {
            $value = $value->pluck(0);
        }

        $validation = $this->model
            ->whereIn($this->field, $this->is_key ? $value->keys() : $value->values())
            ->when($this->is_del, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get();

        return $value->count() === $validation->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ' :attribute 对应模型有不存在的数据';
    }
}
