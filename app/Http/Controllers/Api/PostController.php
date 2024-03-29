<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Version;


class PostController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id', 'title', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }
        $posts = Post::with('media', 'reviews')
            ->whereHas('categories', function ($query) {
                if (request('search_category')) {
                    $categories = explode(",", request('search_category'));
                    $query->whereIn('id', $categories);
                }
            })
            ->whereHas('versions', function ($query) {
                if (request('search_version')) {
                    $versions = explode(",", request('search_version'));
                    $query->whereIn('id', $versions);
                }
            })
            ->when(request('search_id'), function ($query) {
                $query->where('id', request('search_id'));
            })
            ->when(request('search_title'), function ($query) {
                $query->where('title', 'like', '%' . request('search_title') . '%');
            })
            ->when(request('search_content'), function ($query) {
                $query->where('content', 'like', '%' . request('search_content') . '%');
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id', request('search_global'))
                        ->orWhere('title', 'like', '%' . request('search_global') . '%')
                        ->orWhere('content', 'like', '%' . request('search_global') . '%');

                });
            })
            ->when(!auth()->user()->hasPermissionTo('post-all'), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50);
        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $this->authorize('post-create');

        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $post = Post::create($validatedData);

        $categories = explode(",", $request->categories);
        $versions = explode(",", $request->versions);
        $category = Category::findMany($categories);
        $version = Version::findMany($versions);
        $post->categories()->attach($category);
        $post->versions()->attach($version);
//        try {
        if ($request->hasFile('thumbnail')) {
            $post->addMediaFromRequest('thumbnail')->preservingOriginal()->toMediaCollection('thumbnail');
        }
//        } catch (Exception $e) {
//            error_log($e->getMessage());
//        }
        return new PostResource($post);
    }

    public function show(Post $post)
    {
        $this->authorize('post-edit');
        if ($post->user_id !== auth()->user()->id && !auth()->user()->hasPermissionTo('post-all')) {
            return response()->json(['status' => 405, 'success' => false, 'message' => 'You can only edit your own posts']);
        } else {
            return new PostResource($post);
        }
    }

    public function update($id, StorePostRequest $request)
    {
        $this->authorize('post-edit');
        $post = Post::findOrFail($id);

        if ($post->user_id !== auth()->id() && !auth()->user()->hasPermissionTo('post-all')) {
            return response()->json(['status' => 405, 'success' => false, 'message' => 'You can only edit your own posts']);
        } else {
            $post->update($request->validated());

            $category = Category::findMany($request->categories);
            $version = Version::findMany($request->versions);
            $post->categories()->sync($category);
            $post->versions()->sync($version);

            if ($request->hasFile('thumbnail')) {
                $post->addMediaFromRequest('thumbnail')->preservingOriginal()->toMediaCollection('thumbnail');
            }
            return new PostResource($post);
        }
    }

    public function destroy(Post $post)
    {
        $this->authorize('post-delete');
        if ($post->user_id !== auth()->id() && !auth()->user()->hasPermissionTo('post-all')) {
            return response()->json(['status' => 405, 'success' => false, 'message' => 'You can only delete your own posts']);
        } else {
            $post->delete();
            return response()->noContent();
        }
    }

    public function getPosts()
    {

        $posts = null;

        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id', 'title', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $posts = Post::withAvg('reviews', 'rating')
            ->whereHas('categories', function ($query) {
                if (request('search_category')) {
                    $categories = explode(",", request('search_category'));
                    $query->whereIn('categories.id', $categories);
                }
            })
            ->whereHas('versions', function ($query) {
                if (request('search_version')) {
                    $versions = explode(",", request('search_version'));
                    $query->whereIn('versions.id', $versions);
                }
            })
            ->when(request('search_id'), function ($query) {
                $query->where('id', request('search_id'));
            })
            ->when(request('search_title'), function ($query) {
                $query->where('title', 'like', '%' . request('search_title') . '%');
            })
            ->when(request('search_content'), function ($query) {
                $query->where('content', 'like', '%' . request('search_content') . '%');
            })
            ->when(request('search_user_id'), function ($query) {
                $query->where('user_id', request('search_user_id'));
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id', request('search_global'))
                        ->orWhere('title', 'like', '%' . request('search_global') . '%')
                        ->orWhere('content', 'like', '%' . request('search_global') . '%');

                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->latest()
            ->paginate(50);;
        if (request('search_rating')) {
            $posts = $posts->where('reviews_avg_rating', '>=', request('search_rating'))->where('reviews_avg_rating', '<=', (request('search_rating') + 1));
        }

        return PostResource::collection($posts);
    }

    public function getCategoryByPosts($id)
    {
        $posts = Post::whereRelation('categories', 'category_id', '=', $id)->paginate();

        return PostResource::collection($posts);
    }

    public function getVersionByPosts($id)
    {
        $posts = Post::whereRelation('versions', 'version_id', '=', $id)->paginate();
        return PostResource::collection($posts);
    }

    public function getPostsByUser($id)
    {
        $posts = Post::whereRelation('user', 'user_id', '=', $id)->paginate();
        return PostResource::collection($posts);
    }

    public function getPost($id)
    {
        $post = Post::withAvg('reviews', 'rating')->findOrFail($id);
        return new PostResource($post);
    }
}
