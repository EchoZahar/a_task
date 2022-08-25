<?php

namespace App\Models;

use App\Filters\CustomerRequestFilters\CustomerRequestsFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CustomerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'status', 'message', 'comment'
    ];

    const ACTIVE = 'Active';
    const RESOLVED = 'Resolved';
    public static $status = [self::ACTIVE, self::RESOLVED];

    /**
     * filter function
     * @param Builder $builder
     * @param $request
     * @return Builder
     */
    public function scopeFilter(Builder $builder, $request): Builder
    {
        return (new CustomerRequestsFilter($request))->filter($builder);
    }
}
