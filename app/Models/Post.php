<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'content',
        'price',
        'rejected_reason',
        'user_id'
    ];

    public function user () {
        return $this->belongsTo(User::class);
    }
    public function reviews () {
        return $this->hasMany(UserReviews::class);
    }
}
