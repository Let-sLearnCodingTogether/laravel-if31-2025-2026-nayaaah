<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spot extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'picture'
    ];

    public function user() {
        return $this->belogsTo(User::class);
    }

    public function category() {
        return $this->hasMany(Categories::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

}
