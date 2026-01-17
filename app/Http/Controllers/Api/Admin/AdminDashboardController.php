<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    use ApiResponder;

    /**
     * Get admin dashboard statistics.
     */
    public function index(Request $request): JsonResponse
    {
        // Total counts
        $totalUsers = User::count();
        $totalProperties = Property::count();
        $totalReservations = Reservation::count();
        $totalReviews = Review::count();

        // Pending properties (not verified)
        $pendingProperties = Property::where('is_verified', false)
            ->where('is_active', true)
            ->count();

        // Pending payments count (reservations without payment confirmation)
        $pendingPayments = Reservation::whereIn('status', ['pending', 'confirmed'])
            ->whereDoesntHave('payment')
            ->count();

        // Recent registrations (last 7 days)
        $recentRegistrations = User::where('created_at', '>=', now()->subDays(7))
            ->count();

        // Active reservations count
        $activeReservations = Reservation::whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->count();

        // Reservations by status
        $reservationsByStatus = [
            'pending' => Reservation::where('status', 'pending')->count(),
            'confirmed' => Reservation::where('status', 'confirmed')->count(),
            'checked_in' => Reservation::where('status', 'checked_in')->count(),
            'completed' => Reservation::where('status', 'completed')->count(),
            'cancelled' => Reservation::where('status', 'cancelled')->count(),
        ];

        // Recent activity summary
        $recentActivity = [
            'new_users_today' => User::where('created_at', '>=', now()->startOfDay())->count(),
            'new_properties_today' => Property::where('created_at', '>=', now()->startOfDay())->count(),
            'new_reservations_today' => Reservation::where('created_at', '>=', now()->startOfDay())->count(),
            'new_reviews_today' => Review::where('created_at', '>=', now()->startOfDay())->count(),
        ];

        // Monthly statistics (last 6 months)
        $monthlyStats = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats->push([
                'month' => $month->format('Y-m'),
                'month_name' => $month->format('M Y'),
                'reservations' => Reservation::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'new_users' => User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'new_properties' => Property::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ]);
        }

        return $this->successResponse(
            'dashboard_stats',
            [
                'summary' => [
                    'total_users' => $totalUsers,
                    'total_properties' => $totalProperties,
                    'total_reservations' => $totalReservations,
                    'total_reviews' => $totalReviews,
                    'pending_properties' => $pendingProperties,
                    'pending_payments' => $pendingPayments,
                    'recent_registrations' => $recentRegistrations,
                    'active_reservations' => $activeReservations,
                ],
                'reservations_by_status' => $reservationsByStatus,
                'recent_activity' => $recentActivity,
                'monthly_stats' => $monthlyStats,
            ],
            200,
            $request->header('Accept-Language')
        );
    }
}
