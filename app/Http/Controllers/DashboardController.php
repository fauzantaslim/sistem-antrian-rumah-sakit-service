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
     * Query Parameters:
     * - filter: daily, weekly, monthly, yearly, all (default: daily)
     *
     * Returns:
     * 1. Traffic Chart - Antrian per periode waktu
     * 2. Queue per Counter Chart - Banyaknya antrian per loket
     */
    public function charts(Request $request)
    {
        $filter = $request->input('filter', 'daily');

        // Validate filter
        $allowedFilters = ['daily', 'weekly', 'monthly', 'yearly', 'all'];
        if (!in_array($filter, $allowedFilters)) {
            return response()->json([
                'status_code' => 400,
                'success' => false,
                'message' => 'Filter tidak valid. Gunakan: daily, weekly, monthly, yearly, atau all',
                'data' => null
            ], 400);
        }

        // 1. Traffic Chart - Antrian per periode waktu
        $trafficChart = $this->getTrafficChart($filter);

        // 2. Queue per Counter Chart - Banyaknya antrian per loket
        $queuePerCounterChart = $this->getQueuePerCounterChart($filter);

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => 'Data chart dashboard berhasil diambil',
            'data' => [
                'filter' => $filter,
                'traffic_chart' => $trafficChart,
                'queue_per_counter_chart' => $queuePerCounterChart
            ]
        ], 200);
    }

    /**
     * Get traffic chart data based on filter.
     */
    private function getTrafficChart($filter)
    {
        switch ($filter) {
            case 'daily':
                // Antrian per jam hari ini (00:00 - 23:00)
                return Queue::whereDate('created_at', now()->toDateString())
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

            case 'weekly':
                // Antrian per hari dalam 7 hari terakhir
                return Queue::where('created_at', '>=', now()->subDays(7))
                    ->select(
                        DB::raw("TO_CHAR(created_at, 'YYYY-MM-DD') as period"),
                        DB::raw("TO_CHAR(created_at, 'Day') as day_name"),
                        DB::raw('COUNT(*) as total')
                    )
                    ->groupBy('period', 'day_name')
                    ->orderBy('period')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'period' => $item->period,
                            'day_name' => trim($item->day_name),
                            'total' => (int) $item->total
                        ];
                    });

            case 'monthly':
                // Antrian per hari dalam bulan ini
                return Queue::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->select(
                        DB::raw("TO_CHAR(created_at, 'YYYY-MM-DD') as period"),
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

            case 'yearly':
                // Antrian per bulan dalam tahun ini
                return Queue::whereYear('created_at', now()->year)
                    ->select(
                        DB::raw("TO_CHAR(created_at, 'YYYY-MM') as period"),
                        DB::raw("TO_CHAR(created_at, 'Month') as month_name"),
                        DB::raw('COUNT(*) as total')
                    )
                    ->groupBy('period', 'month_name')
                    ->orderBy('period')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'period' => $item->period,
                            'month_name' => trim($item->month_name),
                            'total' => (int) $item->total
                        ];
                    });

            case 'all':
                // Antrian per bulan sepanjang masa
                return Queue::select(
                    DB::raw("TO_CHAR(created_at, 'YYYY-MM') as period"),
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

            default:
                return [];
        }
    }

    /**
     * Get queue per counter chart data based on filter.
     */
    private function getQueuePerCounterChart($filter)
    {
        $query = Queue::with('counter:counter_id,counter_name');

        switch ($filter) {
            case 'daily':
                $query->whereDate('created_at', now()->toDateString());
                break;

            case 'weekly':
                $query->where('created_at', '>=', now()->subDays(7));
                break;

            case 'monthly':
                $query->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month);
                break;

            case 'yearly':
                $query->whereYear('created_at', now()->year);
                break;

            case 'all':
                // No filter, get all data
                break;
        }

        return $query->select(
            'counter_id',
            DB::raw('COUNT(*) as total')
        )
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
