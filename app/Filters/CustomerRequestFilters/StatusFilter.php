<?php


namespace App\Filters\CustomerRequestFilters;


class StatusFilter
{
    /**
     * Filtering by status.
     * @param $builder
     * @param $value
     * @return mixed
     */
    public function filter($builder, $value)
    {
        return $builder->where('status', $value);
    }
}
