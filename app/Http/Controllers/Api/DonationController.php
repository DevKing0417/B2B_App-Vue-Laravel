<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DonationRequest;
use App\Http\Resources\DonationResource;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Donation::with(['donor', 'campaign'])
            ->when($request->campaign_id, function ($q) use ($request) {
                return $q->where('campaign_id', $request->campaign_id);
            })
            ->when($request->user_id, function ($q) use ($request) {
                return $q->where('user_id', $request->user_id);
            })
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            });

        if ($request->sort) {
            [$field, $direction] = explode(':', $request->sort);
            $query->orderBy($field, $direction);
        } else {
            $query->latest();
        }

        $donations = $query->paginate($request->per_page ?? 10);

        return response()->json($donations);
    }

    public function store(Request $request, Campaign $campaign): JsonResponse
    {
        if (!$campaign->isActive()) {
            return response()->json(['message' => 'Campaign is not active'], 422);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'message' => 'nullable|string|max:500',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $donation = new Donation([
            'amount' => $request->amount,
            'message' => $request->message,
            'payment_method' => $request->payment_method,
            'user_id' => auth()->id(),
            'status' => Donation::STATUS_PENDING,
        ]);

        $campaign->donations()->save($donation);

        // Process payment (to be implemented based on chosen payment provider)
        try {
            // $payment = PaymentService::process($donation);
            // $donation->update([
            //     'payment_id' => $payment->id,
            //     'status' => Donation::STATUS_COMPLETED
            // ]);
            
            // Update campaign amount
            $campaign->increment('current_amount', $donation->amount);
            $campaign->updateStatus();

            // Send confirmation email
            // Mail::to($donation->donor->email)->send(new DonationConfirmation($donation));

            return response()->json($donation, 201);
        } catch (\Exception $e) {
            $donation->update(['status' => Donation::STATUS_FAILED]);
            return response()->json(['message' => 'Payment failed'], 500);
        }
    }

    public function show(Donation $donation): DonationResource
    {
        $this->authorize('view', $donation);

        return new DonationResource($donation->load(['campaign', 'user']));
    }
} 