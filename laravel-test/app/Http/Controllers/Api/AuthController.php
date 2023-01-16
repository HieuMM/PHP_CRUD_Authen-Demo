<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Nette\Utils\Type;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password, [])) {
            return response()->json([
                'mess' => "User not exits"

            ], 404);
        }
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json(
            [
                'access_token' => $token,
                'type_token' => "Bearer"
            ]
        );
    }

    public function register(Request $request)
    {
        $mess = [
            'email.email' => "Loi dinh dang email"
        ];

        $validate = Validator::make($request->all(), [
            'email' => 'required'
        ], $mess);
        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()
            ], 404);
        }
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

    }

    public function user(Request $request)
    {
        $request->user();
    }

    public function logout()
    {
        // Revoke all tokens...
        //xóa tất cả token của user

        auth()->user()->tokens()->delete();

        // Revoke the token that was used to authenticate the current request...
        //chỉ xóa token đang sử dụng
        //   $request->user()->currentAccessToken()->delete();

        // Revoke a specific token...
        //xóa id token muốn xóa
        //  $user->tokens()->where('id', $tokenId)->delete();
        return response()->json([
            'mess' => "logout",
            'data' =>auth()->user()->currentAccessToken()
        ], 200);
    }
}
