<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'status', 'message', 'comment'
    ];

    const ACTIVE = 'Active';
    const RESOLVED = 'Resolved';
    public static $status = [self::ACTIVE, self::RESOLVED];

    public function comments()
    {
        return $this->morphToMany(Comment::class, 'commentable');
    }
}
