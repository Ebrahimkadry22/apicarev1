<?php

namespace App\services\UserServices\UserRegisterServices  ;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserRegisterServices {

    protected $model ;



     function __construct()
     {
        $this->model = new User() ;
     }

     function validation($request) {

        $validator = Validator::make($request->all(), $request->rules());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        return $validator;
    }




    function generateToken ($email) {
        $token = substr(md5(rand(0,9) . $email . time()),0,32);
        $user = $this->model->whereEmail($email)->first();

         $user->model->verification_token = $token;
         $user->save();
         return $user;

    }

    function store ($data,$request) {
        $user = $this->model->create(
            array_merge(
                $data->validated(),
                ['password' => bcrypt($request->password),
                'photo' => $request->file('photo')->store('user')
                ]
            )
        );
        return $user->email;
    }

    function sendEmail () {

    }


    function registe ($request) {
        $data = $this->validation($request);
        $email = $this->store($data,$request);
        $storeToken = $this->generateToken($email);
        return response()->json([
            'message' => 'account has been creeated please check your email'
        ],409);


    }









}



?>
