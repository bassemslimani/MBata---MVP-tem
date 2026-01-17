<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the client dashboard with statistics.
     */
    public function index(Request $request): \Inertia\Response
    {
        $user = $request->user();

        // Active reservations (pending, confirmed, or checked_in)
        $activeReservationsCount = $user
            ->reservations()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->count();

        // Total favorites count
        $totalFavoritesCount = $user->favorites()->count();

        // Upcoming check-ins (reservations with check-in date within next 30 days)
        $upcomingCheckIns = $user
            ->reservations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('check_in_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->with(['property.primaryImage', 'property.wilaya'])
            ->orderBy('check_in_date')
            ->limit(5)
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'property_id' => $reservation->property_id,
                    'property_title' => $reservation->property->title_fr,
                    'property_thumbnail' => $reservation->property->primaryImage?->image_url,
                    'check_in_date' => $reservation->check_in_date->toDateString(),
                    'check_out_date' => $reservation->check_out_date->toDateString(),
                    'status' => $reservation->status,
                    'days_until_check_in' => now()->diffInDays($reservation->check_in_date, false),
                ];
            });

        // Recent activity (last 7 days)
        $recentActivity = $this->getRecentActivity($user);

        // Stats summary
        $stats = [
            'active_reservations_count' => $activeReservationsCount,
            'total_favorites_count' => $totalFavoritesCount,
            'upcoming_check_ins_count' => $upcomingCheckIns->count(),
        ];

        return Inertia::render('Client/Dashboard', [
            'stats' => $stats,
            'upcomingCheckIns' => $upcomingCheckIns,
            'recentActivity' => $recentActivity,
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'locale' => $user->locale,
                ],
            ],
        ]);
    }

    /**
     * Get recent activity for the user.
     */
    protected function getRecentActivity($user): array
    {
        $activities = [];

        // Recent reservations (last 7 days)
        $recentReservations = $user
            ->reservations()
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->limit(3)
            ->get(['id', 'property_id', 'created_at', 'status']);

        foreach ($recentReservations as $reservation) {
            $activities[] = [
                'type' => 'reservation',
                'reservation_id' => $reservation->id,
                'property_id' => $reservation->property_id,
                'created_at' => $reservation->created_at->toIso8601String(),
                'status' => $reservation->status,
            ];
        }

        // Recent favorites (last 7 days)
        $recentFavorites = $user
            ->favorites()
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->limit(3)
            ->get(['id', 'property_id', 'created_at']);

        foreach ($recentFavorites as $favorite) {
            $activities[] = [
                'type' => 'favorite',
                'favorite_id' => $favorite->id,
                'property_id' => $favorite->property_id,
                'created_at' => $favorite->created_at->toIso8601String(),
            ];
        }

        // Sort by date
        usort($activities, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        return array_slice($activities, 0, 5);
    }
}
