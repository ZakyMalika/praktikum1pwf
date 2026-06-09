<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AuthApiController extends Controller
{
    /**
     * Get API token for authentication.
     * POST /api/login
     */
    public function getToken(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (! Auth::attempt($data)) {
                Log::info('[Auth - API] Email atau password salah');

                return response()->json([
                    'message' => 'Email atau password salah',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('api_token')->plainTextToken;

            Log::info('[Auth - API] User berhasil login', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat login via API', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error saat login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout and revoke token.
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            Log::info('[Auth - API] User berhasil logout');

            return response()->json([
                'message' => 'Logout berhasil'
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat logout via API', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error saat logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
