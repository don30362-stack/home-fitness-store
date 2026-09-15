import type {
    Product,
    ProductImage,
    ProductVariant,
} from '@/types/product'

export interface CartItemProduct {
    id: number
    product_code: string
    name: string
    price: string
    status: string
    primary_image: ProductImage | null
}

export interface CartItem {
    id: number
    product_id: number
    product_variant_id: number | null
    product: CartItemProduct
    variant: ProductVariant | null
    quantity: number
    unit_price: string
    subtotal: string
    available_stock: number | null
    is_available: boolean
    unavailable_reason: string | null
}

export interface Cart {
    id: number
    items: CartItem[]
    item_count: number
    subtotal: string
    has_unavailable_items: boolean
}

export interface StoreCartItemPayload {
    product_id: number
    product_variant_id?: number | null
    quantity: number
}

export interface UpdateCartItemPayload {
    quantity: number
}

export interface MergeCartPayload {
    items: StoreCartItemPayload[]
}

export interface CartResponse {
    data: Cart
    message?: string
}

export interface GuestCartItem
    extends Omit<CartItem, 'id'> {
    key: string
}

export interface AddGuestCartItemPayload {
    product: Product
    variant: ProductVariant | null
    quantity: number
}