<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserImages extends Model
{
    protected $fillable = [
        'user_id',
        'imagepath',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
