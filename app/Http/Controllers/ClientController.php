<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Client;
use App\services\UserServices\UserLoginServices\UserLoginServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;


class ClientController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:client', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->guard('client')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);

    	// return (new UserLoginServices('client'))->login($request);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        try{
            DB::beginTransaction();
            $token = substr(md5(rand(0,9) . $request['email'] . time()),0,32);
        $validator = FacadesValidator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'location' => 'required|string|between:2,200',
            'phone' => 'required|string|max:19',
            'photo' => 'required',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'verification_token' => 'string'


        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        DB::commit();
        // Mail::to($request['email'])->send(new VerificationEmail($request,$token));
        $user = Client::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password),
                    'photo' => $request->file('photo')->store('user'),
                    'verification_token' => $token

                    ]
                ));
        return response()->json([
            'message' => 'user successfully registered',
        ], 201);
        }catch (Exception $e) {
            return $e->getMessage();
            DB::rollback();
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->guard('client')->logout();
        return response()->json(['message' => 'client successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->guard('client')->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->guard('client')->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'client' => auth()->guard('client')->user()
        ]);
    }
}
