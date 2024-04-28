<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoringPostRequest;
use App\Models\Admin;
use App\Models\Photo;
use App\Models\Post;
use App\Notifications\AdminPost;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::all();
        return response()->json([
            'posts' => $posts
        ],200);
    }

    public function showpost($id)
    {
        $post = Post::where('id', $id);
        return response()->json([
            'posts' => $post->get()
        ],200);
    }

    public function deletepost($id)
    {
        $post = Post::where('id', $id)->delete();
        return response()->json([
            'message' => 'delete post successfuly'
        ],200);
    }

    public function adminpercent ($price) {
        $dicount =$price * 0.05 ;
        $priceAfterDiscount = $price - $dicount;
        return $priceAfterDiscount ;
    }

    public function store(Request $request)
    {
        $id = $request['user_id'] = Auth()->guard('user')->id();
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string',
                'price' => 'required',
                'photo' => "nullable|array|image|mimes:png,jpg,jpeg",
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $price = $this->adminpercent($request->price);
            $post = Post::create([
                'user_id' => $id,
                'content' => $request->content,
                'price' => $price,
            ]);
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $postPhotos = new Photo();
                    $postPhotos->post_id = $post->id;
                    $postPhotos->photo = $photo->store('posts');
                    $postPhotos->save();
                }
            }
            $admins = Admin::get();
            Notification::send($admins, new AdminPost(auth()->guard('user')->user(), $post));
            return response()->json([
                'message' => "Post has been created successfuly , your price after discount {$price}",
            ], 201);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }





    public function approved () {
        $posts = Post::where('status','approved')->get()->makeHidden(['rejected_reason','status']);
        if(!$posts) {
            return response()->json([
                'posts' => 'I have no posts now'
            ],200);
        }
        return response()->json([
            'posts' => $posts
        ],200);
    }

}
