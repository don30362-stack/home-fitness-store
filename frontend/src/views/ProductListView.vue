<script setup lang="ts">
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router'

import ProductCard from '@/components/product/ProductCard.vue'
import { getProducts } from '@/services/productService'
import type { Product } from '@/types/product'

const products = ref<Product[]>([])
const isLoading = ref(false)
const errorMessage = ref('')
const route = useRoute()

const fetchProducts = async () => {
    isLoading.value = true
    errorMessage.value = ''

    try {
        const categoryId = Number(route.query.category_id)
        const parentCategoryId = Number(route.query.parent_category_id)

        products.value = await getProducts({
            category_id: Number.isNaN(categoryId) ? undefined : categoryId,
            parent_category_id: Number.isNaN(parentCategoryId)
                ? undefined
                : parentCategoryId,
        })
    } catch (error) {
        console.error(error)
        errorMessage.value = '商品載入失敗'
    } finally {
        isLoading.value = false
    }
}

watch(
    () => route.query,
    () => {
        fetchProducts()
    },
    {
        immediate: true,
    },
)

</script>

<template>
  <div class="container py-5">
    <h1>商品列表</h1>

    <p v-if="isLoading">商品載入中...</p>

    <p v-else-if="errorMessage" class="text-danger">{{ errorMessage }}</p>

        <div v-else-if="products.length === 0">
            <p class="text-muted">
                目前沒有商品。
            </p>
        </div>

        <div v-else class="row g-4">
            <div v-for="product in products" :key="product.id" class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <ProductCard :product="product" />
            </div>
        </div>
    </div>
</template>
