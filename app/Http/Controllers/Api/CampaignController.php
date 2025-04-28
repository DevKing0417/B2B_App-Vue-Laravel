<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with('creator')
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->category, function ($q) use ($request) {
                return $q->where('category', $request->category);
            })
            ->when($request->search, function ($q) use ($request) {
                return $q->where(function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                });
            });

        // Apply sorting
        if ($request->sort) {
            [$field, $direction] = explode(':', $request->sort);
            $query->orderBy($field, $direction);
        } else {
            $query->latest();
        }

        $campaigns = $query->paginate($request->per_page ?? 10);

        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:1',
            'end_date' => 'required|date|after:today',
            'image' => 'nullable|image|max:10240', // 10MB max
            'category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('image');
        $data['user_id'] = auth()->id();
        $data['status'] = Campaign::STATUS_PENDING;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('campaigns', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $campaign = Campaign::create($data);

        return response()->json($campaign, 201);
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['creator', 'donations.donor']);
        return response()->json($campaign);
    }

    public function update(Request $request, Campaign $campaign)
    {
        if ($campaign->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'target_amount' => 'sometimes|required|numeric|min:1',
            'end_date' => 'sometimes|required|date|after:today',
            'image' => 'nullable|image|max:10240',
            'category' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($campaign->image_url) {
                Storage::disk('public')->delete($campaign->image_url);
            }
            $path = $request->file('image')->store('campaigns', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $campaign->update($data);

        return response()->json($campaign);
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($campaign->image_url) {
            Storage::disk('public')->delete($campaign->image_url);
        }

        $campaign->delete();

        return response()->json(null, 204);
    }

    public function approve(Campaign $campaign)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $campaign->update(['status' => Campaign::STATUS_ACTIVE]);

        // Send approval email to campaign creator
        // Mail::to($campaign->creator->email)->send(new CampaignApproved($campaign));

        return response()->json($campaign);
    }

    public function cancel(Campaign $campaign)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $campaign->update(['status' => Campaign::STATUS_CANCELLED]);

        return response()->json($campaign);
    }
} 