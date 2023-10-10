<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVersionRequest;
use App\Http\Resources\VersionResource;
use App\Models\Version;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id', 'name', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }
        $versions = Version::
            when(request('search_id'), function ($query) {
                $query->where('id', request('search_id'));
            })
            ->when(request('search_title'), function ($query) {
                $query->where('name', 'like', '%'.request('search_title').'%');
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function($q) {
                    $q->where('id', request('search_global'))
                        ->orWhere('name', 'like', '%'.request('search_global').'%');

                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50);
        return VersionResource::collection($versions);
    }

    public function store(StoreVersionRequest $request)
    {
        $this->authorize('version-create');
        $version = Version::create($request->validated());

        return new VersionResource($version);
    }

    public function show(Version $version)
    {
        $this->authorize('version-edit');
        return new VersionResource($version);
    }

    public function update(Version $version, StoreVersionRequest $request)
    {
        $this->authorize('version-edit');
        $version->update($request->validated());

        return new VersionResource($version);
    }

    public function destroy(Version $version) {
        $this->authorize('version-delete');
        $version->delete();

        return response()->noContent();
    }

    public function getList()
    {
        return VersionResource::collection(Version::all());
    }
}
