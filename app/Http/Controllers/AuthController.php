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
     * @return \Illuminate\Http\RedirectResponse
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
                // User not found - redirect to frontend with error
                $frontendUrl = env('FRONTEND_URL', 'http://localhost:8026');
                return redirect($frontendUrl . '/auth/callback?error=email_not_registered&email=' . urlencode($googleUser->getEmail()));
            }

            // Check if email is verified
            if (!$user->hasVerifiedEmail()) {
                // Email not verified - redirect to frontend with error
                $frontendUrl = env('FRONTEND_URL', 'http://localhost:8026');
                return redirect($frontendUrl . '/auth/callback?error=email_not_verified&email=' . urlencode($user->email) . '&user_id=' . $user->user_id);
            }

            // Update google_id if not set
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            // Create Sanctum token
            $token = $user->createToken('google-auth-token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:8026');
            return redirect($frontendUrl . '/auth/callback?token=' . urlencode($token) . '&user=' . urlencode(json_encode([
                'user_id' => $user->user_id,
                'name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'counter_id' => $user->counter_id
            ])));

        } catch (\Exception $e) {
            // Redirect to frontend with error
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:8026');
            return redirect($frontendUrl . '/auth/callback?error=login_failed&message=' . urlencode($e->getMessage()));
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
        $user = $request->user();
        
        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Data user berhasil diambil',
            'data' => [
                'user_id' => $user->user_id,
                'google_id' => $user->google_id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'role' => $user->role,
                'counter_id' => $user->counter_id,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ], 200);
    }
}
