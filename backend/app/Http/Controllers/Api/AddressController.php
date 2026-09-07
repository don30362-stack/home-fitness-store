<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserAddressRequest;
use App\Http\Requests\UpdateUserAddressRequest;
use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
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
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateUserAddressRequest $request, int $id): UserAddressResource
    {
        $address = $this->findUserAddress($request, $id);

        $address->update($request->validated());
        $address->load('district.city');

        return (new UserAddressResource($address))
            ->additional([
                'message' => '收件地址更新成功',
            ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        DB::transaction(function () use ($request, $id) {
            $address = $this->findUserAddress($request, $id);
            $wasDefault = $address->is_default;

            $address->delete();

            if ($wasDefault) {
                $nextDefault = $request->user()
                    ->userAddresses()
                    ->orderBy('id')
                    ->first();

                $nextDefault?->update([
                    'is_default' => true,
                ]);
            }
        });

        return response()->json([
            'message' => '收件地址刪除成功'
        ]);
    }

    public function setDefault(Request $request, int $id): UserAddressResource
    {
        $address = DB::transaction(function () use ($request, $id) {
            $user = $request->user();
            $address = $this->findUserAddress($request, $id);

            $user->userAddresses()->update([
                'is_default' => false,
            ]);

            $address->update([
                'is_default' => true,
            ]);

            return $address;
        });

        return (new UserAddressResource($address))
            ->additional([
                'message' => '預設收件地址設定成功',
            ]);
    }

    private function findUserAddress(Request $request, int $id): UserAddress
    {
        return $request->user()
            ->userAddresses()
            ->findOrFail($id);
    }
}
