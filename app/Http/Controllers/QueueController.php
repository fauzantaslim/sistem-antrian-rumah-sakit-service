<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueueRequest;
use App\Models\Counter;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QueueController extends Controller
{
    /**
     * Display a listing of the resource.
     * Required: counter_id
     * Auto-filter: Only show 'waiting' and 'called' status (exclude 'done')
     * Returns: currently_called object + waiting queues list
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'queue_number');
        $sortOrder = $request->input('sort_order', 'asc');
        $counterId = $request->input('counter_id');

        // Validate counter_id is required
        if (!$counterId) {
            return response()->json([
                'status_code' => 400,
                'success' => false,
                'message' => 'Counter ID wajib diisi',
                'data' => null
            ], 400);
        }

        // Validate sort_by
        $allowedSortBy = ['queue_number', 'status', 'created_at', 'called_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'asc';

        // Get currently called queue for this counter
        $currentlyCalled = Queue::with([
            'counter' => function ($q) {
                $q->select('counter_id', 'counter_name');
            },
            'calledBy' => function ($q) {
                $q->select('user_id', 'full_name');
            }
        ])
            ->where('counter_id', $counterId)
            ->where('status', 'called')
            ->orderBy('called_at', 'asc') //first in first out
            ->first();

        // Get waiting queues (exclude 'done' status)
        $query = Queue::query()
            ->with([
                'counter' => function ($q) {
                    $q->select('counter_id', 'counter_name');
                },
                'calledBy' => function ($q) {
                    $q->select('user_id', 'full_name');
                }
            ])
            ->where('counter_id', $counterId)
            ->whereIn('status', ['waiting', 'called']); // Only waiting and called, exclude done

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('queue_number', 'ILIKE', "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $queues = $query->paginate($perPage);

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Data antrian berhasil diambil',
            'data' => [
                'currently_called' => $currentlyCalled,
                'queues' => $queues->items(),
                'pagination' => [
                    'current_page' => $queues->currentPage(),
                    'per_page' => $queues->perPage(),
                    'total' => $queues->total(),
                    'last_page' => $queues->lastPage(),
                    'from' => $queues->firstItem(),
                    'to' => $queues->lastItem(),
                ]
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * PUBLIC - No authentication required for patients to take queue number
     */
    public function store(QueueRequest $request)
    {
        $validated = $request->validated();

        // Get counter to determine letter code based on creation order
        $counter = Counter::find($validated['counter_id']);

        if (!$counter) {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => 'Counter tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Get letter code based on counter creation order (A, B, C, ...)
        // Counter with earliest created_at gets 'A', second gets 'B', etc.
        $counterOrder = Counter::where('created_at', '<=', $counter->created_at)
            ->orderBy('created_at', 'asc')
            ->pluck('counter_id')
            ->toArray();
        
        $counterIndex = array_search($counter->counter_id, $counterOrder);
        $letterCode = chr(65 + $counterIndex); // 65 = ASCII 'A'

        // Get last queue number for today for this counter
        $today = now()->startOfDay();
        $lastQueue = Queue::where('counter_id', $validated['counter_id'])
            ->whereDate('created_at', $today)
            ->orderBy('queue_number', 'desc')
            ->first();

        // Generate queue number
        if ($lastQueue) {
            // Extract number from last queue (e.g., "A001" -> 1)
            $lastNumber = (int) substr($lastQueue->queue_number, 1);
            $newNumber = $lastNumber + 1;
        } else {
            // First queue of the day
            $newNumber = 1;
        }

        // Format: A001, A002, etc.
        $queueNumber = $letterCode . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $queue = Queue::create([
            'queue_id' => (string) Str::ulid(),
            'counter_id' => $validated['counter_id'],
            'queue_number' => $queueNumber,
            'status' => 'waiting',
        ]);

        // Load relationships
        $queue->load(['counter:counter_id,counter_name']);

        return response()->json([
            'status_code' => 201,
            'success' => true,
            'message' => 'Nomor antrian berhasil dibuat',
            'data' => $queue
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $queue = Queue::with([
            'counter' => function ($q) {
                $q->select('counter_id', 'counter_name');
            },
            'calledBy' => function ($q) {
                $q->select('user_id', 'full_name');
            }
        ])->find($id);

        if (!$queue) {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => 'Antrian tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Detail antrian berhasil diambil',
            'data' => $queue
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * Used to update status, called_at, called_by
     * Status transition rules:
     * - waiting -> called (only)
     * - called -> done (only)
     * - done -> cannot be updated
     */
    public function update(QueueRequest $request, $id)
    {
        $queue = Queue::find($id);

        if (!$queue) {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => 'Antrian tidak ditemukan',
                'data' => null
            ], 404);
        }

        $validated = $request->validated();

        // Validate status transition if status is being updated
        if (isset($validated['status'])) {
            $currentStatus = $queue->status;
            $newStatus = $validated['status'];

            // Check if status is already 'done'
            if ($currentStatus === 'done') {
                return response()->json([
                    'status_code' => 400,
                    'success' => false,
                    'message' => 'Antrian yang sudah selesai tidak dapat diubah statusnya',
                    'data' => null
                ], 400);
            }

            // Validate status transition
            if ($currentStatus === 'waiting' && $newStatus !== 'called') {
                return response()->json([
                    'status_code' => 400,
                    'success' => false,
                    'message' => 'Status waiting hanya bisa diubah ke called',
                    'data' => null
                ], 400);
            }

            if ($currentStatus === 'called' && $newStatus !== 'done') {
                return response()->json([
                    'status_code' => 400,
                    'success' => false,
                    'message' => 'Status called hanya bisa diubah ke done',
                    'data' => null
                ], 400);
            }

            // Auto-set called_at and called_by when status changes to 'called'
            if ($newStatus === 'called' && $currentStatus === 'waiting') {
                // Check if counter already has a queue with 'called' status
                $existingCalledQueue = Queue::where('counter_id', $queue->counter_id)
                    ->where('status', 'called')
                    ->where('queue_id', '!=', $queue->queue_id)
                    ->first();
                
                if ($existingCalledQueue) {
                    return response()->json([
                        'status_code' => 400,
                        'success' => false,
                        'message' => 'Sudah ada antrian yang sedang dipanggil di counter ini. Selesaikan antrian ' . $existingCalledQueue->queue_number . ' terlebih dahulu.',
                        'data' => [
                            'existing_called_queue' => [
                                'queue_id' => $existingCalledQueue->queue_id,
                                'queue_number' => $existingCalledQueue->queue_number,
                                'called_at' => $existingCalledQueue->called_at
                            ]
                        ]
                    ], 400);
                }
                
                $validated['called_at'] = now();
                $user = auth('sanctum')->user();
                $validated['called_by'] = $user ? $user->user_id : null;
            }
        }

        $queue->update($validated);

        // Load relationships
        $queue->load([
            'counter:counter_id,counter_name',
            'calledBy:user_id,full_name'
        ]);

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Antrian berhasil diupdate',
            'data' => $queue
        ], 200);
    }

    /**
     * Display public queue board
     * PUBLIC - Shows currently called queues from all counters + max 8 waiting queues
     * Returns: called_queues (all counters) + waiting_queues (max 8, sorted by oldest first)
     */
    public function display()
    {
        // Get all currently called queues from all counters
        $calledQueues = Queue::with([
            'counter' => function ($q) {
                $q->select('counter_id', 'counter_name');
            },
            'calledBy' => function ($q) {
                $q->select('user_id', 'full_name');
            }
        ])
            ->where('status', 'called')
            ->orderBy('called_at', 'desc')
            ->get();

        // Get waiting queues (max 8, oldest first)
        $waitingQueues = Queue::with([
            'counter' => function ($q) {
                $q->select('counter_id', 'counter_name');
            }
        ])
            ->where('status', 'waiting')
            ->orderBy('created_at', 'asc') // Oldest first (FIFO)
            ->limit(8)
            ->get();

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Data display antrian berhasil diambil',
            'data' => [
                'called_queues' => $calledQueues,
                'waiting_queues' => $waitingQueues,
                'total_waiting' => Queue::where('status', 'waiting')->count(),
            ]
        ], 200);
    }
}
