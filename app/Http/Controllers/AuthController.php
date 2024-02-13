<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try{
            $validatedData = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|unique:users,email|max:255',
                'password' => 'required|string|confirmed'
            ]);
            if($validatedData->fails()){
                return response()->json([
                    'status' =>'false',
                    'message' =>'validation error',
                    'errors' => $validatedData->errors()
                ],401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);


            $token = $user->createToken('authToken')->plainTextToken;

            $response =[
                'status' => 'true',
                'message' =>'User Created successfully',
                'user' =>$user,
                'token' => $token,
            ];
            return response()->json($response,200);
        } catch(Exception $e) {
            Log::error($e->getMessage(),[]);
        }


    }

    public function login(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(),[
                'email' => 'required|email',
                'password' => 'required'
            ]);
            if($validateData->fails()){
                return response()->json([
                    'status' => 'false',
                    'message' => 'validation error',
                    'errors'=> $validateData->errors(),
                ]);
            }
            if(!Auth::attempt($request->only(['email','password']))){
                return response()->json([
                    'status'=> 'false',
                    'message' => 'Email $ password does not match'
                ],401);
            }
            $user = User::where('email',$request->email)->first();
            $token = $user->createToken('authToken')->plainTextToken;
            $response = [
                'status'=> 'true',
                'message'=> 'User logged in successfully',
                'user'=>$user,
                'token'=> $token,
            ];
            return response()->json($response,200);
        }catch(Exception $e) {
            Log::error($e->getMessage(),[]);
        }
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
