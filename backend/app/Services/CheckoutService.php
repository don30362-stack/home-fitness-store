<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\District;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    private const SHIPPING_FEE_CENTS = 10_000;

    private function findLockedCart(User $user): Cart
    {
        $cart = Cart::query()
            ->where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if ($cart === null) {
            throw ValidationException::withMessages([
                'cart' => '購物車目前沒有商品，請先加入商品後再結帳。',
            ]);
        }

        return $cart;
    }

    /**
     * @return Collection<int, CartItem>
     */
    private function getLockedCartItems(Cart $cart): Collection
    {
        $items = CartItem::query()
            ->where('cart_id', $cart->id)
            ->orderBy('product_id')
            ->orderBy('product_variant_id')
            ->lockForUpdate()
            ->get();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => '購物車目前沒有商品，請先加入商品後再結帳。',
            ]);
        }

        return $items;
    }

    private function findLockedProduct(
        CartItem $cartItem
    ): Product {
        $product = Product::query()
            ->whereKey($cartItem->product_id)
            ->lockForUpdate()
            ->first();

        if ($product === null || $product->status !== 'active') {
            throw ValidationException::withMessages([
                'cart' => '購物車內有已下架或不存在的商品。',
            ]);
        }

        if (
            $cartItem->product_variant_id === null
            && $product->variants()->exists()
        ) {
            throw ValidationException::withMessages([
                'cart' => "商品「{$product->name}」需要重新選擇規格。",
            ]);
        }

        return $product;
    }

    private function findLockedVariant(
        CartItem $cartItem,
        Product $product
    ): ?ProductVariant {
        if ($cartItem->product_variant_id === null) {
            return null;
        }

        $variant = ProductVariant::query()
            ->whereKey($cartItem->product_variant_id)
            ->where('product_id', $product->id)
            ->lockForUpdate()
            ->first();

        if ($variant === null || $variant->status !== 'active') {
            throw ValidationException::withMessages([
                'cart' => "商品「{$product->name}」的規格目前無法購買。",
            ]);
        }

        return $variant;
    }

    private function ensureEnoughStock(
        CartItem $cartItem,
        Product $product,
        ?ProductVariant $variant
    ): void {
        $availableStock = $variant !== null
            ? (int) $variant->stock
            : (int) ($product->stock ?? 0);

        if ($cartItem->quantity > $availableStock) {
            throw ValidationException::withMessages([
                'cart' => "商品「{$product->name}」庫存不足，目前可購買數量為 {$availableStock}。",
            ]);
        }
    }

    private function findDistrict(int $districtId): District
    {
        $district = District::query()
            ->with('city')
            ->find($districtId);

        if ($district === null || $district->city === null) {
            throw ValidationException::withMessages([
                'recipient.district_id' => '選擇的行政區不存在。',
            ]);
        }

        return $district;
    }

    private function generateOrderNo(): string
    {
        return 'HF-' . Str::ulid();
    }

    private function moneyToCents(string $amount): int
    {
        [$whole, $fraction] = array_pad(
            explode('.', $amount, 2),
            2,
            '0'
        );

        $fraction = str_pad(
            substr($fraction, 0, 2),
            2,
            '0'
        );

        return ((int) $whole * 100) + (int) $fraction;
    }

    private function calculateItemSubtotalCents(
        Product $product,
        int $quantity
    ): int {
        return $this->moneyToCents(
            (string) $product->price
        ) * $quantity;
    }

    private function formatMoney(int $amountInCents): string
    {
        $whole = intdiv($amountInCents, 100);
        $fraction = $amountInCents % 100;

        return $whole . '.' . str_pad(
            (string) $fraction,
            2,
            '0',
            STR_PAD_LEFT
        );
    }
}
