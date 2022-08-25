<?php


namespace App\Filters\CustomerRequestFilters;


class DateFilter
{
    /**
     * Date filter return records for a specific day
     * @param $builder
     * @param $value
     * @return mixed
     */
    public function filter($builder, $value)
    {
        return $builder->whereDate('created_at', $value);
    }
}
