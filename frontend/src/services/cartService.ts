import api from '@/services/api'

import type {
    CartResponse,
    MergeCartPayload,
    StoreCartItemPayload,
    UpdateCartItemPayload,
} from '@/types/cart'

export const getCart = async (): Promise<CartResponse> => {
    const response = await api.get<CartResponse>('/cart')

    return response.data
}

export const addCartItem = async (
    payload: StoreCartItemPayload,
): Promise<CartResponse> => {
    const response = await api.post<CartResponse>(
        '/cart/items',
        payload,
    )

    return response.data
}

export const updateCartItem = async (
    itemId: number,
    payload: UpdateCartItemPayload,
): Promise<CartResponse> => {
    const response = await api.patch<CartResponse>(
        `/cart/items/${itemId}`,
        payload,
    )

    return response.data
}

export const removeCartItem = async (
    itemId: number,
): Promise<CartResponse> => {
    const response = await api.delete<CartResponse>(
        `/cart/items/${itemId}`,
    )

    return response.data
}

export const clearCart = async (): Promise<CartResponse> => {
    const response = await api.delete<CartResponse>('/cart')

    return response.data
}

export const mergeCart = async (
    payload: MergeCartPayload,
): Promise<CartResponse> => {
    const response = await api.post<CartResponse>(
        '/cart/merge',
        payload,
    )

    return response.data
}