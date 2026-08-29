import api from '@/services/api'
import type { ApiResponse, PaginatedApiResponse } from '@/types/api'
import type { Product, ProductListItem, ProductQueryParams } from '@/types/product'

export const getProducts = async (
    params?: ProductQueryParams
): Promise<PaginatedApiResponse<ProductListItem>> => {
    const response = await api.get<PaginatedApiResponse<ProductListItem>>('/products', {
        params:params
    })

    return response.data
}

export const getProductById = async (id: number | string): Promise<Product> => {
    const response = await api.get<ApiResponse<Product>>(`/products/${id}`)
    return response.data.data
}

export const getRelatedProducts = async (id: number | string):Promise<ProductListItem[]> => {
    const response = await api.get<ApiResponse<ProductListItem[]>>(`/products/${id}/related`)

    return response.data.data
}