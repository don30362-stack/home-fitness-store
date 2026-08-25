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

export interface Product {
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

export interface ProductQueryParams {
    category_id?: number
    parent_category_id?: number
}