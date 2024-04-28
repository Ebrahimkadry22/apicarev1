<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\PostStatusRequest;
use App\Models\Post;
use App\Notifications\AdminPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class PostStatusController extends Controller
{
    public function changeStatus (Request $request) {
        $vaildator = Validator::make($request->all(),[
            'post_is'=> 'required|exists:posts,id',
            'status'=> 'required|in:approved,rejected',
            'rejected_reason'=> 'required_if:status,rejected',
        ]);
        $post = Post::find($request['post_id']);
        $post->update([
            'status'=>$request['status'],
        'rejected_reason'=>$request['rejected_reason']]
    );
        Notification::send($post->user,new AdminPost($post->user, $post));

        return response()->json(['message'=>'successfuly process post'], 200);
    }
}
