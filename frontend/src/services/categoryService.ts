import api from '@/services/api'
import type { ApiResponse } from '@/types/api'
import type { Category } from '@/types/category'

export const getCategories = async (): Promise<Category[]> => {
    const response = await api.get<ApiResponse<Category[]>>('/categories')
    return response.data.data
}