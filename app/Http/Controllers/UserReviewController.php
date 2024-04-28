<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserReviewResources;
use App\Models\UserReviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserReviewController extends Controller
{
    function store (Request $request) {
        $idClient = auth()->guard('client')->id();
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'comment' => 'nullable|string',
            'rate' => 'required|integer|max:5',

        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $reviews = UserReviews::create([
            'client_id'=> $idClient,
            'post_id'=> $request->post_id,
            'rate'=>$request->rate
        ]);

        return response()->json(
            [
                'data' => $reviews
            ], 200);

    }



    function postRate ($id) {
        $review =UserReviews::wherePostId('post_id',$id);
        if($review) {
            // $avarage = $review->sum('rate') / $review->count() ;
            return response()->json(
                [
                    // 'total_rate' =>round($avarage , 1),
                    'data' => UserReviewResources::collection($review->get())
                ], 200);
        }
        return response()->json(
            [
                'data' => 'not rate post'
            ], 200);
    }




 }

