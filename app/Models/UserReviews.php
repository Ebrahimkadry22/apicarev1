<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReviews extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'client_id',
        'comment',
        'rate',
    ];

    

}
