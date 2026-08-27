<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

import ProductCard from '@/components/product/ProductCard.vue'
import ProductCategoryNav from '@/components/product/ProductCategoryNav.vue'
import { getProducts } from '@/services/productService'
import type { ProductListItem } from '@/types/product'
import type { ApiErrorResponse } from '@/types/api'

const products = ref<ProductListItem[]>([])
const isLoading = ref(false)
const errorMessage = ref('')
const route = useRoute()

const fetchProducts = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const categoryId = Number(route.query.category_id)
    const parentCategoryId = Number(route.query.parent_category_id)

    const hasInvalidCategoryId =
      route.query.category_id != undefined && (!Number.isInteger(categoryId) || categoryId <= 0)

    const hasInvalidParentCategoryId =
      route.query.parent_category_id != undefined &&
      (!Number.isInteger(parentCategoryId) || parentCategoryId <= 0)

    if (hasInvalidCategoryId || hasInvalidParentCategoryId) {
      products.value = []
      isLoading.value = false
      errorMessage.value = '找不到此商品分類'
      return
    }

    products.value = await getProducts({
      category_id: route.query.category_id !== undefined ? categoryId : undefined,
      parent_category_id:
        route.query.parent_category_id !== undefined ? parentCategoryId : undefined,
    })
  } catch (error) {
    console.error(error)

    if (axios.isAxiosError<ApiErrorResponse>(error) && error.response?.status === 404) {
      errorMessage.value = error.response.data?.message || '找不到此商品分類'
    } else {
      errorMessage.value = '商品載入失敗'
    }
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
    <div class="row g-4">
      <aside class="col-12 col-lg-3">
        <ProductCategoryNav />
      </aside>

      <section class="col-12 col-lg-9">
        <h1 class="mb-4">商品列表</h1>

        <p v-if="isLoading">商品載入中...</p>

        <p v-else-if="errorMessage" class="text-danger">{{ errorMessage }}</p>

        <div v-else-if="products.length === 0">
          <p class="text-muted">目前沒有商品。</p>
        </div>

        <div v-else class="row g-4">
          <div v-for="product in products" :key="product.id" class="col-12 col-sm-6 col-xl-4">
            <ProductCard :product="product" />
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
