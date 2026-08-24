<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { isAxiosError } from 'axios'

import { getProductById } from '@/services/productService'
import type { Product } from '@/types/product'

const route = useRoute()

const product = ref<Product | null>(null)
const isLoading = ref(true)
const errorMessage = ref('')

const fetchProduct = async () => {
    try {
        const id = route.params.id

        if (typeof id !== 'string') {
            errorMessage.value = '無效的商品 ID'
            return
        }

        product.value = await getProductById(id)
    } catch (error) {
        console.error(error)
        
        if (isAxiosError(error) && error.response?.status === 404) {
            errorMessage.value = '找不到此商品'
        } else{
            errorMessage.value = '商品資料載入失敗'
        }
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    fetchProduct()
})
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
            <h1>{{ product.name }}</h1>

            <p>
                商品編號：{{ product.product_code }}
            </p>

            <p>
                分類：{{ product.category.name }}
            </p>

            <p>
                NT$ {{ Number(product.price).toLocaleString('zh-TW') }}
            </p>

            <p>
                庫存：{{ product.stock ?? '未設定' }}
            </p>

            <p v-if="product.short_description">
                {{ product.short_description }}
            </p>
        </div>
    </div>
</template>