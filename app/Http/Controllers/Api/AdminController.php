<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_donations' => Donation::where('status', Donation::STATUS_COMPLETED)->sum('amount'),
            'active_campaigns' => Campaign::where('status', Campaign::STATUS_ACTIVE)->count(),
            'total_donors' => Donation::where('status', Donation::STATUS_COMPLETED)->distinct('user_id')->count(),
            'average_donation' => Donation::where('status', Donation::STATUS_COMPLETED)->avg('amount'),
        ];

        $recentActivity = Donation::with(['donor', 'campaign'])
            ->where('status', Donation::STATUS_COMPLETED)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($donation) {
                return [
                    'id' => $donation->id,
                    'type' => 'donation',
                    'description' => "{$donation->donor->name} donated \${$donation->amount} to {$donation->campaign->title}",
                    'created_at' => $donation->created_at,
                    'icon' => 'CurrencyDollarIcon',
                ];
            });

        return response()->json([
            'stats' => $stats,
            'recent_activity' => $recentActivity,
        ]);
    }

    public function getSettings()
    {
        $settings = [
            'company_name' => config('app.company_name'),
            'contact_email' => config('app.contact_email'),
            'min_donation' => config('app.min_donation'),
            'max_campaign_duration' => config('app.max_campaign_duration'),
            'max_campaign_target' => config('app.max_campaign_target'),
            'campaign_categories' => config('app.campaign_categories'),
            'donation_confirmation_template' => config('app.donation_confirmation_template'),
            'campaign_approval_template' => config('app.campaign_approval_template'),
        ];

        return response()->json($settings);
    }

    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'min_donation' => 'required|numeric|min:0',
            'max_campaign_duration' => 'required|integer|min:1',
            'max_campaign_target' => 'required|numeric|min:0',
            'campaign_categories' => 'required|string',
            'donation_confirmation_template' => 'required|string',
            'campaign_approval_template' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update settings in config file or database
        // This is a simplified example - in a real application, you'd want to
        // store these settings in a database table
        foreach ($request->all() as $key => $value) {
            config(["app.{$key}" => $value]);
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    public function getUsers(Request $request)
    {
        $query = User::query()
            ->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });

        if ($request->sort) {
            [$field, $direction] = explode(':', $request->sort);
            $query->orderBy($field, $direction);
        } else {
            $query->latest();
        }

        $users = $query->paginate($request->per_page ?? 10);

        return response()->json($users);
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:user,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update(['role' => $request->role]);

        return response()->json($user);
    }
} 