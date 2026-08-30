import axios from "axios"

import api from "@/services/api"

import type { ApiResponse, ApiMessageResponse } from "@/types/api"
import type { LoginPayload, RegisterPayload, User } from "@/types/auth"

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL
const backendBaseUrl = apiBaseUrl.replace(/\/api\/?$/, '')

export const getCsrfCookie = async (): Promise<void> => {
    await axios.get(`${backendBaseUrl}/sanctum/csrf-cookie`, {
        withCredentials: true
    })
}

export const register = async (payload: RegisterPayload): Promise<ApiMessageResponse<User>> => {
    await getCsrfCookie()

    const response = await api.post<ApiMessageResponse<User>>('/register', payload)

    return response.data
}

export const login = async (payload: LoginPayload): Promise<ApiMessageResponse<User>> => {
    await getCsrfCookie()

    const response = await api.post<ApiMessageResponse<User>>('/login', payload)

    return response.data
}

export const getMe = async (): Promise<ApiResponse<User>> => {
    const response = await api.get<ApiResponse<User>>('/me')

    return response.data
}

export const logout = async (): Promise<void> => {
    await api.post('/logout')
}