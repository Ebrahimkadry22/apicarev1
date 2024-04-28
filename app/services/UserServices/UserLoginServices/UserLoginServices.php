<?php

namespace App\services\UserServices\UserLoginServices ;

use App\Models\Admin;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserLoginServices {
    protected $model ;

    function __construct($namemodel)
    {
        if($namemodel == 'admin') {
            $this->model = new Admin();
        }
        elseif($namemodel == 'user') {
            $this->model = new User();
        }
        elseif($namemodel == 'client') {
            $this->model = new Client();
        }
    }


    function validation($request) {

        $validator = Validator::make($request->all(), $request->rules());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        return $validator;
    }


    function isValiDate ($data) {
        if (! $token = auth()->guard('admin')->attempt($data->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $token;

    }


    function getStatus ($email) {
        $user = $this->model->whereEmail($email)->first();
        $status = $user->status;

        return $status;
    }
    function isVerified ($email) {
        $user = $this->model->whereEmail($email)->first();
        $verified = $user->verified_at;

        return $verified;
    }


    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'admin' => auth()->guard('admin')->user()
        ]);
    }


    function login ($request) {
        $data = $this->validation($request);
        $token = $this->isValiDate($data);
       if($this->isVerified($request->email) == null) {
            return response()->json([
                'message'=> 'Your account is verified'

            ],);

        }
        elseif($this->getStatus($request->email) == 0) {
            return response()->json([
                'message'=> 'Your account is pending'

            ],422);
        }
        return $this->createNewToken($token);
    }



}



?>




