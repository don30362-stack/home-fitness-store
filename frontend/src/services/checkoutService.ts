import api from '@/services/api'

import type {
    CheckoutPayload,
    CheckoutResponse,
} from '@/types/checkout'

export const checkout = async (
    payload: CheckoutPayload,
): Promise<CheckoutResponse> => {
    const response = await api.post<CheckoutResponse>(
        '/checkout',
        payload,
    )

    return response.data
}