<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\APIHelpers;

class AuthController extends Controller
{
    public function register(Request $request){
        $params = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
        ]);

        $user = User::create([
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => bcrypt($params['password'])
        ]);
        
        if($user){
            $token = $user->createToken('homebudgetapptoken')->plainTextToken;
            $content = [
                'user' => $user,
                'token' => $token
            ];

            $response = APIHelpers::createAPIResponse(false, 201, '', $content);
            return response()->json($response, 201);
        } else{
            $response = APIHelpers::createAPIResponse(true, 400, 'User could not be registred!', null);
            return response()->json($response, 400);
        }

        return response()->json($response);
    }

    public function login(Request $request){
        $params = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $params['email'])->first();

        if(!$user || !Hash::check($params['password'], $user->password)){
            $response = APIHelpers::createAPIResponse(true, 401, 'Bad credentials!', null);
            return response()->json($response, 401);
        } else{
            $token = $user->createToken('homebudgetapptoken')->plainTextToken;
            $content = [
                'user' => $user,
                'token' => $token
            ];

            $response = APIHelpers::createAPIResponse(false, 200, 'Login successfull!', $content);

            return response()->json($response, 200);
        }

    }

    public function logout(Request $request){
        $logout = auth()->user()->tokens()->delete();

        if($logout){
            $response = APIHelpers::createAPIResponse(false, 200, 'Logout successfull!', null);
            return response()->json($response, 200);
        } else{
            $response = APIHelpers::createAPIResponse(true, 400, 'Logout unsuccessfull!', null);
            return response()->json($response, 400);
        }
    }
}
