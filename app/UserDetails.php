<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetails extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'country',
        'city',
        'state',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
