<script setup lang="ts">
import { onMounted, ref } from 'vue';

import { getProducts } from '@/services/productService';
import type { Product } from '@/types/product';

const products = ref<Product[]>([])
const isLoading = ref(true)
const errorMessage = ref('')

const fetchProducts = async () => {
    try{
        products.value = await getProducts()
    }catch(error) {
        console.error(error)
        errorMessage.value = '商品載入失敗'
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {fetchProducts()})

</script>

<template>
    <div class="container py-5">
        <h1>商品列表</h1>

        <p v-if="isLoading">商品載入中...</p>

        <p v-else-if="errorMessage" class="text-danger">{{ errorMessage }}</p>

        <div v-else>
            <ul>
                <li v-for="product in products" :key="product.id">
                    {{ product.name }}-NT${{ Number(product.price).toLocaleString('zh-TW') }}
                </li>
            </ul>
        </div>
    </div>
</template>