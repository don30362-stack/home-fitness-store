export interface ProductCategory {
    id: number | null
    name: string | null
}

export interface ProductImage {
    id: number
    image_path: string
    image_url: string
    image_type: string
    is_primary: boolean
    sort_order: number
}

export interface ProductSpecification {
    id: number
    spec_name: string
    spec_value: string
    sort_order: number
}

export interface ProductVariant {
    id: number
    option_name: string
    option_value: string
    stock: number
    status: string
}

export interface ProductListItem {
    id: number
    product_code: string
    name: string
    price: string
    short_description: string | null
    description: string | null
    stock: number | null
    status: string
    category: ProductCategory
    images: ProductImage[]
}

export interface Product extends ProductListItem {
    specifications: ProductSpecification[]
    variants: ProductVariant[]
}

export type ProductSort = 'price_asc' | 'price_desc'

export interface ProductQueryParams {
    search?: string
    category_id?: number
    parent_category_id?: number
    min_price?: number
    max_price?: number
    sort?: ProductSort
    page?: number
}


