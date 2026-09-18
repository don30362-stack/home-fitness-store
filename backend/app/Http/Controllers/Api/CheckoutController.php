<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __invoke(
        CheckoutRequest $request,
        CheckoutService $checkoutService
    ): JsonResponse {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401);
        }

        $order = $checkoutService->checkout(
            $user,
            $request->validated()
        );

        return (new OrderResource($order))
            ->additional([
                'message' => '訂單建立成功。',
            ])
            ->response()
            ->setStatusCode(201);
    }
}
