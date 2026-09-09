import type { DistrictWithCity } from "./location"

export interface UserAddress {
    id: number,
    label: string,
    recipient_name: string,
    recipient_phone: string,
    address: string,
    is_default: boolean,
    district: DistrictWithCity,
}

export interface StoreUserAddressPayload {
    district_id: number,
    label: string,
    recipient_name: string,
    recipient_phone: string,
    address: string,
    is_default?: boolean,
}

export interface UpdateUserAddressPayload {
    district_id?: number,
    label?: string,
    recipient_name?: string,
    recipient_phone?: string,
    address?: string,
}