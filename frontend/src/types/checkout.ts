export type ShippingMethod = 'home_delivery'

export type PaymentMethod =
    | 'cod'
    | 'mock_credit_card'

export interface CheckoutPurchaserForm {
    name: string
    phone: string
    email: string
}

export interface CheckoutRecipientForm {
    name: string
    phone: string
    district_id: number | null
    address: string
}

export interface CheckoutForm {
    purchaser: CheckoutPurchaserForm
    recipient: CheckoutRecipientForm
    shipping_method: ShippingMethod
    payment_method: PaymentMethod | ''
}

export interface CheckoutPayload {
    purchaser: {
        name: string
        phone: string
        email: string
    }
    recipient: {
        name: string
        phone: string
        district_id: number
        address: string
    }
    shipping_method: ShippingMethod
    payment_method: PaymentMethod
}