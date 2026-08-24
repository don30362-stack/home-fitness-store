<script setup lang="ts">
import { onMounted, ref } from 'vue';

import ProductCard from '@/components/product/ProductCard.vue';
import { getProducts } from '@/services/productService'
import type { Product } from '@/types/product'

const products = ref<Product[]>([])
const isLoading = ref(true)
const errorMessage = ref('')

const fetchProducts = async () => {
    try {
        products.value = await getProducts()
    } catch (error) {
        console.error(error)
        errorMessage.value = '商品載入失敗'
    } finally {
        isLoading.value = false
    }
}

onMounted(() => { fetchProducts() })

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
                <ProductCard :product="product"/>
            </div>
        </div>
    </div>
</template>