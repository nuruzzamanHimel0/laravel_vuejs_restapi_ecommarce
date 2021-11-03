<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function register(Request $request){


        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);



        // $user =  User::firstOrNew(['email' =>  $request->email]);;
        // $user->name = $request->name;
        // $user->email = $request->email;
        // $user->password =  Hash::make($request->password);
        // $user->save();

        $user = User::firstOrCreate(
            ['email' =>  $request->email],
            ['name' => $request->name, 'password' =>  Hash::make($request->password)]
        );

        $http = new Client;

        $response = $http->post('http://localhost/laravel_vuejs_restapi_ecommarce/public/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '3',
                'client_secret' => 'uScbirqduPCmgixgvm5Cuz26HRMgJhKRJgr91qAT',
                'username' =>$request->email,
                'password' =>$request->password,
                'scope' => '',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);

    }

    public function login(Request $request){

        // dd($request->all());

        $validatedData = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

         $user =  User::where('email',$request->email)->first();

         if($user){
             if(Hash::check($request->password, $user->password)){

                $http = new Client;

                $response = $http->post(url('oauth/token'), [
                    'form_params' => [
                        'grant_type' => 'password',
                        'client_id' => '3',
                        'client_secret' => 'uScbirqduPCmgixgvm5Cuz26HRMgJhKRJgr91qAT',
                        'username' =>$request->email,
                        'password' =>$request->password,
                        'scope' => '',
                    ],
                ]);
                 return response()->json([
                    'data' => json_decode((string) $response->getBody(), true),
                    'user' => $user
                ]);
                // return json_decode((string) $response->getBody(), true);
             }




         }

    }
}
