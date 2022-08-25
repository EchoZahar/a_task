<?php


namespace App\Filters\CustomerRequestFilters;


use App\Filters\AbstractFilter;

class CustomerRequestsFilter extends AbstractFilter
{
    protected $filters = [
        'status' => StatusFilter::class,
        'date' => DateFilter::class,
    ];
}
