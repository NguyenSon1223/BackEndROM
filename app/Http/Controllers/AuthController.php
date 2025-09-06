<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user,
        ], 201);
    }

    /**
     * Login và trả JWT token
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        // 🔑 dùng guard api thay vì mặc định web
        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized - Invalid email or password'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function profile()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Logout (invalidate token)
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    /**
     * Trả response với token
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
