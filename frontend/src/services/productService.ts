import api from '@/services/api'
import type { ApiResponse } from '@/types/api'
import type { Product } from '@/types/product'

export const getProducts = async (): Promise<Product[]> => {
    const response = await api.get<ApiResponse<Product[]>>('/products')
    return response.data.data
}

export const getProductById = async (id: number | string): Promise<Product> => {
    const response = await api.get<ApiResponse<Product>>(`/products/${id}`)
    return response.data.data
}