export interface ApiResponse<T> {
  data: T
}

export interface PaginationMeta {
    current_page: number
    last_page: number
    per_page: number
    total: number
}

export interface PaginatedApiResponse<T> {
    data: T[]
    meta: PaginationMeta
}

export interface ApiErrorResponse {
  message: string
  errors?: Record<string, string[]>
}