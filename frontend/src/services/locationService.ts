import api from "@/services/api";

import type { ApiResponse } from "@/types/api";
import type { City, District } from "@/types/location";

export const getCities = async (): Promise<ApiResponse<City[]>> => {
    const response = await api.get<ApiResponse<City[]>>('cities')

    return response.data
}

export const getDistricts = async (cityId: number): Promise<ApiResponse<District[]>> => {
    const response = await api.get<ApiResponse<District[]>>(`/cities/${cityId}/districts`)

    return response.data
}
