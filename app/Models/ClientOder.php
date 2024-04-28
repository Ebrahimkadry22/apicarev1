<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientOder extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'post_id'
    ];
    protected $guraded = ['status'];

    function client () {
        return $this->belongsTo(Client::class)->select(['id','first_name']);
    }
    function post () {
        return $this->belongsTo(Post::class)->select(['id','content']);
    }
}
