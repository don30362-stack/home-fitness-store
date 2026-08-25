import api from '@/services/api'
import type { ApiResponse } from '@/types/api'
import type { Product, ProductQueryParams } from '@/types/product'

export const getProducts = async (
    params?: ProductQueryParams
): Promise<Product[]> => {
    const response = await api.get<ApiResponse<Product[]>>('/products', {
        params:params
    })

    return response.data.data
}

export const getProductById = async (id: number | string): Promise<Product> => {
    const response = await api.get<ApiResponse<Product>>(`/products/${id}`)
    return response.data.data
}