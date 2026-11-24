<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $media = Media::query()
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $query->where('title', 'like', '%'.$request->string('search')->toString().'%');
            })
            ->when($request->boolean('include_users'), fn (Builder $query) => $query->with('users'))
            ->orderBy('title')
            ->paginate(15);

        return MediaResource::collection($media);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        return (new MediaResource(Media::create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Media $media): MediaResource
    {
        return new MediaResource($media->load('users'));
    }

    public function update(UpdateMediaRequest $request, Media $media): MediaResource
    {
        $media->update($request->validated());

        return new MediaResource($media->refresh());
    }

    public function destroy(Media $media): Response
    {
        $media->delete();

        return response()->noContent();
    }
}
