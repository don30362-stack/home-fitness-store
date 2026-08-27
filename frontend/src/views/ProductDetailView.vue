<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { isAxiosError } from 'axios'

import { getProductById, getRelatedProducts } from '@/services/productService'
import type { Product, ProductListItem, ProductVariant } from '@/types/product'
import type { ApiErrorResponse } from '@/types/api'
import ProductGallery from '@/components/product/ProductGallery.vue'
import ProductCard from '@/components/product/ProductCard.vue'

const route = useRoute()

const product = ref<Product | null>(null)
const relatedProducts = ref<ProductListItem[]>([])
const selectedVariant = ref<ProductVariant | null>(null)
const isLoading = ref(false)
const errorMessage = ref('')
const quantity = ref(1)

const hasVariants = computed(() => {
    return (product.value?.variants.length ?? 0) > 0
})

const availableStock = computed(() => {
    if (!product.value) {
        return 0
    }

    if (hasVariants.value) {
        return selectedVariant.value?.stock ?? 0
    }

    return product.value.stock ?? 0
})

const isOutOfStock = computed(() => {
    return availableStock.value === 0
})

const fetchProduct = async () => {
    isLoading.value = true
    errorMessage.value = ''
    product.value = null
    relatedProducts.value = []

    try {
        const id = route.params.id

        if (typeof id !== 'string') {
            errorMessage.value = '無效的商品 ID'
            return
        }

        const productId = Number(id)

        if (!Number.isInteger(productId) || productId <= 0) {
            errorMessage.value = '無效的商品 ID'
            return
        }

        product.value = await getProductById(productId)
        relatedProducts.value = await getRelatedProducts(productId)
    } catch (error) {
        console.error(error)

        if (isAxiosError<ApiErrorResponse>(error) && error.response?.status === 404) {
            errorMessage.value = error.response.data?.message || '找不到此商品'
        } else {
            errorMessage.value = '商品資料載入失敗'
        }
    } finally {
        isLoading.value = false
    }
}

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--
    }
}

const increaseQuantity = () => {
    if (quantity.value < availableStock.value) {
        quantity.value++
    }
}

watch(
    () => route.params.id,
    () => {
        fetchProduct()
    },
    {
        immediate: true
    }
)

watch(
    availableStock,
    (stock) => {
        if (stock <= 0) {
            quantity.value = 1
            return
        }

        if (quantity.value > stock) {
            quantity.value = stock
        }
    }
)
</script>

<template>
    <div class="container py-5">
        <p v-if="isLoading">
            商品載入中...
        </p>

        <p v-else-if="errorMessage" class="text-danger">
            {{ errorMessage }}
        </p>

        <div v-else-if="product">
            <div class="row g-5">
                <div class="col-12 col-lg-6">
                    <ProductGallery :images="product.images" :product-name="product.name" />
                </div>

                <div class="col-12 col-lg-6">
                    <p class="text-muted mb-2"> {{ product.category.name }}</p>

                    <h1 class="mb-3">{{ product.name }}</h1>

                    <p class="text-muted">
                        商品編號：{{ product.product_code }}
                    </p>

                    <p class="fs-3 fw-bold">
                        NT$ {{ Number(product.price).toLocaleString('zh-TW') }}
                    </p>

                    <div v-if="hasVariants" class="mb-4">
                        <p class="fw-semibold mb-2">
                            {{ product.variants[0]?.option_name }}
                        </p>

                        <div class="d-flex flex-wrap gap-2">
                            <button v-for="variant in product.variants" :key="variant.id" type="button" class="btn"
                                :class="selectedVariant?.id === variant.id ? 'btn-dark' : 'btn-outline-dark'"
                                :disabled="variant.status !== 'active'" @click="selectedVariant = variant">
                                {{ variant.option_value }}
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="me-2">庫存：</span>

                        <span v-if="hasVariants && !selectedVariant" class="text-muted">請先選擇規格</span>

                        <span v-else-if="isOutOfStock" class="badge text-bg-secondary">缺貨</span>

                        <span v-else class="text-success">尚有 {{ availableStock }} 件</span>
                    </div>

                    <p v-if="product.short_description" class="text-muted">
                        {{ product.short_description }}
                    </p>

                    <div v-if="!hasVariants || selectedVariant" class="mb-4">
                        <label class="form-label fw-semibold">購買數量</label>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary"
                                :disabled="quantity <= 1 || isOutOfStock" @click="decreaseQuantity">
                                -
                            </button>

                            <span class="text-center" style="min-width: 40px;">
                                {{ quantity }}
                            </span>

                            <button type="button" class="btn btn-outline-secondary"
                                :disabled="quantity >= availableStock || isOutOfStock" @click="increaseQuantity">
                                +
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn btn-dark btn-lg w-100"
                            :disabled="isOutOfStock || (hasVariants && !selectedVariant)">
                            {{ hasVariants && !selectedVariant ? '請先選擇規格' : isOutOfStock ? '商品缺貨' : '加入購物車' }}
                        </button>
                    </div>
                </div>
            </div>

            <section class="mt-5 pt-4 border-top">
                <h2 class="h4 fw-bold mb-4">商品詳情</h2>

                <p v-if="product.description" class="lh-lg mb-0" style="white-space: pre-line;">
                    {{ product.description }}
                </p>

                <p v-else class="text-muted mb-0">暫無商品詳細說明。</p>
            </section>

            <section v-if="product.specifications.length > 0" class="mt-5 pt-4 border-top">
                <h2 class="h4 fw-bold mb-4">商品規格</h2>

                <dl class="row mb-0">
                    <template v-for="specification in product.specifications" :key="specification.id">
                        <dt class="col-sm-3 py-2">
                            {{ specification.spec_name }}
                        </dt>

                        <dd class="col-sm-9 py-2 mb-0">
                            {{ specification.spec_value }}
                        </dd>
                    </template>
                </dl>
            </section>

            <section v-if="relatedProducts.length > 0" class="mt-5 pt-4 border-top">
                <h2 class="h4 fw-bold mb-4">相關商品</h2>

                <div class="row g-4">
                    <div v-for="relatedProduct in relatedProducts" :key="relatedProduct.id"
                        class="col-12 col-sm-6 col-lg-3">
                        <ProductCard :product="relatedProduct" />
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>