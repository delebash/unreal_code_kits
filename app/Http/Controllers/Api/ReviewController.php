<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

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
     * Display the specified resource.
     *
     * @param int $id
     * @return ReviewResource
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

    public function update(StoreReviewRequest $request, Post $post, Review $review)
    {
        $this->authorize('review-edit');
        if ($review->user_id !== auth()->user()->id && !auth()->user()->hasPermissionTo('review-all')) {
            return response()->json(['message' => 'Action Forbidden']);
        }
        $request->validate([
            'review' => 'required|string'
        ]);

        $review->review = $request->review;
        $review->rating = $request->ratings;
        $review->save();

        return response()->json(['message' => 'Review Updated', 'review' => $review]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $this->authorize('review-delete');
        if ($review->user_id !== auth()->user()->id && !auth()->user()->hasPermissionTo('review-all')) {
            return response()->json(['message' => 'Action Forbidden']);
        }
        $review->delete();
        return response()->json(null, 204);
    }

    public function getReview($id)
    {

        $review = Review::with('user')->findOrFail($id);
        return new ReviewResource($review);
    }
}
