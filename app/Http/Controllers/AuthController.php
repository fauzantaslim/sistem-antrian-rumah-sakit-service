<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');
        return $driver->stateless()->redirect();
    }

    /**
     * Handle Google OAuth callback
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback()
    {
        try {
            /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
            $driver = Socialite::driver('google');
            $googleUser = $driver->stateless()->user();

            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // User not found - must be registered by admin first
                return view('email-not-registered', [
                    'email' => $googleUser->getEmail()
                ]);
            }

            // Check if email is verified
            if (!$user->hasVerifiedEmail()) {
                return view('email-not-verified', [
                    'email' => $user->email,
                    'user_id' => $user->user_id
                ]);
            }

            // Update google_id if not set
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            // Create Sanctum token
            $token = $user->createToken('google-auth-token')->plainTextToken;

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Login gagal',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Logout berhasil',
            'data' => null
        ], 200);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Data user berhasil diambil',
            'data' => $request->user()
        ], 200);
    }
}
