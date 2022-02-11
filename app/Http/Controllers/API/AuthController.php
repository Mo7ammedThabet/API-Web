<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email'=>'required|email|max:191|unique:users,email',
            'password' => 'required',
        ]);
       $data['password']= bcrypt($data['password']);
        $user=User::create($data); 
        \request()->request->add(['username'=>$data['email']]);
        return $this->login();

       // dd($user);
        //$token = $user->createToken('authToken')->accessToken;

//return response()->json([
     // 'user' => $user , 'token' => $token ]
         //  , 200);

    }
    public function login()
    {
       //dd(\request()->all());
     
      
        $proxy = Request::create('oauth/token', 'POST');
$response = Route::dispatch($proxy);
$statusCode = $response->getStatusCode();
$response = json_decode($response->getContent());
if ($statusCode != 200)
    return $this->sendError($response->message);
$response_token = $response;
$token = $response->access_token;
\request()->headers->set('Authorization', 'Bearer ' . $token);

$proxy = Request::create('api/profile', 'GET');
$response = Route::dispatch($proxy);
$statusCode = $response->getStatusCode();
//dd(json_decode($response->getContent()));
//  dd($response->getContent());
$user = json_decode($response->getContent())->item;
return response(['status'=>true,'statusCode'=>200,'message'=>'Success','item'=>['user' => $user, 'access_token' => $response_token]]);
         



   }
   public function profile(){
        $user= auth()->user();

        $data=[
            'item'=>$user
        ];
        return response()->json($data);

   }

   public function logout(){
    if(auth()->user()){
        $user = auth()->user();
        $user->api_token = null ;
        
        return response()->json(['message' => 'user has been log out successfully']);
    }
    return response()->json([
        'error' => 'Unable to logout user',
        'code' => 401,
    ], 401);
      } 

 /*  public function login(Request $request)
    {
        $login= $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (!Auth::attempt($login)){
            return response(['message'=>'Invalid login credentials.']);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response(['user' => Auth::user(),'access_token'=>$accessToken]);

    }*/
}
