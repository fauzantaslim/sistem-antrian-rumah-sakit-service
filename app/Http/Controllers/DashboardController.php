<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     *
     * Returns:
     * - Total Antrian Hari Ini
     * - Total Loket
     * - Total User
     * - Rata-rata Waktu Tunggu
     */
    public function stats()
    {
        // 1. Total Antrian Hari Ini
        $totalAntrianHariIni = Queue::whereDate('created_at', now()->toDateString())->count();

        // 2. Total Loket
        $totalLoket = Counter::count();

        // 3. Total User
        $totalUser = User::count();

        // 4. Rata-rata Waktu Tunggu (dalam menit)
        // Hitung dari created_at sampai called_at untuk antrian yang sudah dipanggil hari ini
        $avgWaitingTime = Queue::whereDate('created_at', now()->toDateString())
            ->whereNotNull('called_at')
            ->select(DB::raw('AVG(EXTRACT(EPOCH FROM (called_at - created_at)) / 60) as avg_minutes'))
            ->value('avg_minutes');

        // Round to 2 decimal places, default to 0 if null
        $avgWaitingTime = $avgWaitingTime ? round($avgWaitingTime, 2) : 0;

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Statistik dashboard berhasil diambil',
            'data' => [
                'total_queues_today' => $totalAntrianHariIni,
                'total_counters' => $totalLoket,
                'total_users' => $totalUser,
                'average_waiting_time' => [
                    'value' => $avgWaitingTime,
                    'unit' => 'menit'
                ]
            ]
        ], 200);
    }

    /**
     * Get dashboard charts data.
     *
     * Returns:
     * 1. Traffic Chart - Antrian per periode waktu
     * 2. Queue per Counter Chart - Banyaknya antrian per loket
     */
    public function charts()
    {
        // 1. Traffic Chart (antrian per jam hari ini)
        $trafficChart = Queue::whereDate('created_at', now()->toDateString())
            ->select(
                DB::raw("TO_CHAR(created_at, 'HH24:00') as period"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->period,
                    'total' => (int) $item->total
                ];
            });

        // 2. Queue per Counter Chart (antrian per loket hari ini)
        $queuePerCounterChart = Queue::with('counter:counter_id,counter_name')
            ->whereDate('created_at', now()->toDateString())
            ->select('counter_id', DB::raw('COUNT(*) as total'))
            ->groupBy('counter_id')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'counter_id' => $item->counter_id,
                    'counter_name' => $item->counter ? $item->counter->counter_name : 'Unknown',
                    'total' => (int) $item->total
                ];
            });

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Data chart dashboard (harian) berhasil diambil',
            'data' => [
                'traffic_chart' => $trafficChart,
                'queue_per_counter_chart' => $queuePerCounterChart
            ]
        ], 200);
    }

    /**
     * Get queue status distribution per counter for today.
     * 
     * Returns the distribution of queue statuses (waiting, called, done) 
     * for each counter today.
     */
    public function statusDistribution()
    {
        $today = now()->toDateString();

        // Get all counters with their queue status counts for today
        $distribution = Counter::with(['queues' => function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            }])
            ->get()
            ->map(function ($counter) {
                $queues = $counter->queues;
                
                return [
                    'counter_id' => $counter->counter_id,
                    'counter_name' => $counter->counter_name,
                    'waiting' => $queues->where('status', 'waiting')->count(),
                    'called' => $queues->where('status', 'called')->count(),
                    'done' => $queues->where('status', 'done')->count(),
                    'total' => $queues->count()
                ];
            })
            ->sortByDesc('total')
            ->values();

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Queue status distribution retrieved successfully',
            'data' => $distribution
        ], 200);
    }
}
