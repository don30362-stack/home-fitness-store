<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserAddressRequest;
use App\Http\Resources\UserAddressResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $addresses = $request->user()
            ->userAddresses()
            ->with('district.city')
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get();

        return UserAddressResource::collection($addresses);
    }

    public function store(StoreUserAddressRequest $request): JsonResponse
    {
        $address = DB::transaction(function () use ($request) {
            $user = $request->user();
            $validated = $request->validated();

            $hasAddresses = $user
                ->userAddresses()
                ->exists();

            $shouldBeDefault =
                !$hasAddresses ||
                (bool) ($validated['is_default'] ?? false);

            if ($shouldBeDefault) {
                $user->userAddresses()->update([
                    'is_default' => false,
                ]);
            }

            $validated['is_default'] = $shouldBeDefault;

            return $user()
                ->userAddresses()
                ->create($validated);
        });

        $address->load('district.city');

        return (new UserAddressResource($address))
            ->additional([
                'message' => '收件地址新增成功',
            ])
            ->setStatusCode(201);
    }
}
