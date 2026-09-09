import api from "@/services/api";

import type { ApiResponse, ApiMessageResponse, ApiMessageOnlyResponse } from "@/types/api";

import type { UserAddress, StoreUserAddressPayload, UpdateUserAddressPayload } from "@/types/address";

export const getAddresses = async (): Promise<ApiResponse<UserAddress[]>> => {
    const response = await api.get<ApiResponse<UserAddress[]>>('/addresses')

    return response.data
}

export const storeAddress = async (payload: StoreUserAddressPayload): Promise<ApiMessageResponse<UserAddress>> => {
    const response = await api.post<ApiMessageResponse<UserAddress>>('/addresses', payload)

    return response.data
}

export const updateAddress = async (id: number, payload: UpdateUserAddressPayload): Promise<ApiMessageResponse<UserAddress>> => {
    const response = await api.patch<ApiMessageResponse<UserAddress>>(`/addresses/${id}`, payload)

    return response.data
}

export const deleteAddress = async (id: number): Promise<ApiMessageOnlyResponse> => {
    const response = await api.delete<ApiMessageOnlyResponse>(`/addresses/${id}`)

    return response.data
}

export const setDefaultAddress = async (id: number): Promise<ApiMessageResponse<UserAddress>> => {
    const response = await api.patch<ApiMessageResponse<UserAddress>>(`/addresses/${id}/default`)

    return response.data
}