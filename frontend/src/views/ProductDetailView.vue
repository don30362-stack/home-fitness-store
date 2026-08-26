<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { isAxiosError } from 'axios'

import { getProductById } from '@/services/productService'
import type { Product } from '@/types/product'
import type { ApiErrorResponse } from '@/types/api'
import ProductGallery from '@/components/product/ProductGallery.vue'

const route = useRoute()

const product = ref<Product | null>(null)
const isLoading = ref(false)
const errorMessage = ref('')

const isOutOfStock = computed(() => {
    return product.value?.stock === 0
})

const fetchProduct = async () => {
    isLoading.value = true
    errorMessage.value = ''
    product.value = null

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

watch(
    () => route.params.id,
    () => {
        fetchProduct()
    },
    {
        immediate: true
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
                    <ProductGallery
                        :images="product.images"
                        :product-name="product.name"
                    />
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

                    <div class="mb-4">
                        <span class="me-2">庫存：</span>

                        <span v-if="product.stock === null" class="text-muted">未設定</span>

                        <span v-else-if="isOutOfStock" class="badge text-bg-secondary">缺貨</span>

                        <span v-else class="text-success">尚有 {{ product.stock }} 件</span>
                    </div>

                    <p v-if="product.short_description" class="text-muted">
                        {{ product.short_description }}
                    </p>

                    <div class="mt-4">
                        <button type="button" class="btn btn-dark btn-lg w-100" :disabled="isOutOfStock">
                            {{ isOutOfStock ? '商品缺貨' : '加入購物車' }}
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
        </div>
    </div>
</template>