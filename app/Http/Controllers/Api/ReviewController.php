<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Post;
class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $review = Review::all();
        return ReviewResource::collection($review);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request, Post $post)
    {

        $this->authorize('review-create');
        $request->validate([
            'review' => 'required|string',
        ]);
        $review = new Review;
        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->user_id = auth()->user()->id;

        $post->reviews()->save($review);
        return response()->json(['message' => 'Review Added', 'review' => $review]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $this->authorize('review-edit');
        if ($review->user_id !== auth()->user()->id && !auth()->user()->hasPermissionTo('review-all')) {
            return response()->json(['status' => 405, 'success' => false, 'message' => 'You can only edit your own reviews']);
        } else {
            return new ReviewResource($review);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $this->authorize('review-edit');
        if (auth()->user()->id !== $review->user_id) {
            return response()->json(['message' => 'Action Forbidden']);
        }
        $request->validate([
            'review' => 'required|string',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->save();

        return response()->json(['message' => 'Review Updated', 'review' => $review]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $this->authorize('review-delete');
        if (auth()->user()->id !== $review->user_id) {
            return response()->json(['message' => 'Action Forbidden']);
        }
        $review->delete();
        return response()->json(null, 204);
    }
}
