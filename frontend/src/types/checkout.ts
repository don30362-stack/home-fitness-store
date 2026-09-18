import type {
    ApiMessageResponse,
} from '@/types/api'

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

export type CheckoutPaymentStatus =
    | 'unpaid'
    | 'paid'

export type CheckoutOrderStatus = 'pending'

export interface CheckoutOrderItem {
    id: number
    product_id: number
    product_variant_id: number | null
    product_code: string
    product_name: string
    variant: string | null
    unit_price: string
    quantity: number
    subtotal: string
}

export interface CheckoutOrder {
    id: number
    order_no: string
    purchaser: {
        name: string
        phone: string
        email: string
    }
    recipient: {
        name: string
        phone: string
        postal_code: string
        city: string
        district: string
        address: string
    }
    shipping_method: ShippingMethod
    shipping_fee: string
    payment_method: PaymentMethod
    payment_status: CheckoutPaymentStatus
    order_status: CheckoutOrderStatus
    subtotal: string
    total_amount: string
    logistics_company: string | null
    tracking_number: string | null
    items: CheckoutOrderItem[]
    created_at: string | null
}

export type CheckoutResponse =
    ApiMessageResponse<CheckoutOrder>