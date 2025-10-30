<?php

namespace App\Http\Controllers;

use App\Http\Requests\CounterRequest;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CounterController extends Controller
{
    /**
     * Display a simple listing for public access (no pagination).
     */
    public function listPublic()
    {
        $counters = Counter::select('counter_id', 'counter_name', 'description')
            ->orderBy('counter_name', 'asc')
            ->get();

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Data counter berhasil diambil',
            'data' => $counters
        ], 200);
    }

    /**
     * Display a listing of the resource (protected - with pagination).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Validate sort_by
        $allowedSortBy = ['counter_name', 'created_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';

        $query = Counter::query();

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('counter_name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $counters = $query->paginate($perPage);

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Data counter berhasil diambil',
            'data' => [
                'counters' => $counters->items(),
                'pagination' => [
                    'current_page' => $counters->currentPage(),
                    'per_page' => $counters->perPage(),
                    'total' => $counters->total(),
                    'last_page' => $counters->lastPage(),
                    'from' => $counters->firstItem(),
                    'to' => $counters->lastItem(),
                ]
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CounterRequest $request)
    {
        $validated = $request->validated();

        $counter = Counter::create([
            'counter_id' => (string) Str::ulid(),
            'counter_name' => $validated['counter_name'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'status_code' => 201,
            'success' => true,
            'message' => 'Counter berhasil dibuat',
            'data' => $counter
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $counter = Counter::find($id);

        if (!$counter) {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => 'Counter tidak ditemukan',
                'data' => null
            ], 404);
        }
        
        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Detail counter berhasil diambil',
            'data' => $counter
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CounterRequest $request, $id)
    {
        $counter = Counter::find($id);

        if (!$counter) {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => 'Counter tidak ditemukan',
                'data' => null
            ], 404);
        }

        $validated = $request->validated();
        $counter->update($validated);
        
        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Counter berhasil diupdate',
            'data' => $counter
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $counter = Counter::find($id);

        if (!$counter) {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => 'Counter tidak ditemukan',
                'data' => null
            ], 404);
        }

        $counter->delete();
        
        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Counter berhasil dihapus',
            'data' => null
        ], 200);
    }
}
