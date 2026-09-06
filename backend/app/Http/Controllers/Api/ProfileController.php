<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function update(UpdateProfileRequest $request): UserResource
    {
        $user = $request->user();

        $user->update($request->validated());
        $user->refresh();

        return (new UserResource($user))
            ->additional([
                'message' => '會員資料更新成功',
            ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $user->update([
            'password' => $validated['password'],
        ]);

        return response()->json([
            'message' => '密碼更新成功'
        ]);
    }
}
