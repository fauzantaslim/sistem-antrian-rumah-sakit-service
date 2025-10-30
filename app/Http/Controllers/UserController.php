<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Validate sort_by
        $allowedSortBy = ['full_name', 'email', 'role', 'email_verified_at', 'created_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';

        $query = User::query()
            ->with(['counter' => function ($q) {
                $q->select('counter_id', 'counter_name');
            }])
            ->leftJoin('counters', 'users.counter_id', '=', 'counters.counter_id')
            ->select('users.*');

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.full_name', 'ILIKE', "%{$search}%")
                  ->orWhere('users.email', 'ILIKE', "%{$search}%")
                  ->orWhere('counters.counter_name', 'ILIKE', "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy('users.' . $sortBy, $sortOrder);

        // Pagination
        $users = $query->paginate($perPage);

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Data user berhasil diambil',
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ]
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'user_id' => (string) Str::ulid(),
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'counter_id' => $validated['counter_id'],
        ]);

        // Trigger email verification
        event(new Registered($user));

        return response()->json([
            'status_code' => 201,
            'success' => true,
            'message' => 'User berhasil dibuat. Email verifikasi telah dikirim.',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with(['counter' => function ($q) {
            $q->select('counter_id', 'counter_name');
        }])->find($id);

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => 'User tidak ditemukan',
                'data' => null
            ], 404);
        }
        
        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Detail user berhasil diambil',
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => 'User tidak ditemukan',
                'data' => null
            ], 404);
        }

        $validated = $request->validated();
        $user->update($validated);
        
        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => 'User tidak ditemukan',
                'data' => null
            ], 404);
        }

        $user->delete();
        
        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'User berhasil dihapus',
            'data' => null
        ], 200);
    }

    /**
     * Verify user email
     */
    public function verify(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return view('email-verified', [
                'status' => 'already_verified',
                'user' => $user
            ]);
        }

        if ($user->markEmailAsVerified()) {
            return view('email-verified', [
                'status' => 'success',
                'user' => $user
            ]);
        }

        return view('email-verified', [
            'status' => 'error',
            'user' => $user
        ]);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => 'Email sudah terverifikasi',
                'data' => [
                    'user_id' => $user->user_id,
                    'email' => $user->email,
                    'verified_at' => $user->email_verified_at
                ]
            ], 200);
        }

        event(new Registered($user));

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Email verifikasi telah dikirim ulang',
            'data' => [
                'user_id' => $user->user_id,
                'email' => $user->email
            ]
        ], 200);
    }
}
