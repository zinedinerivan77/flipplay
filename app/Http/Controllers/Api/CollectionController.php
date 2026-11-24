<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CollectionController extends Controller
{
    public function index(User $user)
    {
        return MediaResource::collection(
            $user->media()->orderBy('title')->paginate(15)
        );
    }

    public function store(User $user, Media $media): JsonResponse
    {
        if ($user->media()->whereKey($media->getKey())->exists()) {
            return response()->json([
                'message' => 'Media is already in this user\'s collection.',
            ], 409);
        }

        $user->media()->attach($media->getKey());

        return (new MediaResource($media))->response()->setStatusCode(201);
    }

    public function destroy(User $user, Media $media): Response|JsonResponse
    {
        if ($user->media()->detach($media->getKey()) === 0) {
            return response()->json([
                'message' => 'Media was not found in this user\'s collection.',
            ], 404);
        }

        return response()->noContent();
    }
}
