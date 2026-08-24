<script setup lang="ts">
import { onMounted, ref } from 'vue'

import { getCategories } from '@/services/categoryService'
import type { Category } from '@/types/category'

const categories = ref<Category[]>([])
const isLoading = ref(false)
const errorMessage = ref('')

const fetchCategories = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    categories.value = await getCategories()
  } catch (error) {
    console.error(error)
    errorMessage.value = '分類載入失敗'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchCategories()
})
</script>

<template>
  <div>
    <h2 class="h5 mb-3">商品分類</h2>

    <p v-if="isLoading" class="text-muted">分類載入中...</p>

    <p v-else-if="errorMessage" class="text-danger">
      {{ errorMessage }}
    </p>

    <ul v-else class="list-unstyled">
      <li class="mb-3">
        <Routerlink :to="{ name: 'products' }" class="text-decoration-none"> 全部商品 </Routerlink>
      </li>

      <li v-for="category in categories" :key="category.id" class="mb-3">
        <Routerlink
          :to="{
            name: 'products',
            query: { parent_category_id: category.id },
          }"
          class="fw-bold text-decoration-none"
        >
          {{ category.name }}
        </Routerlink>

        <ul class="list-unstyled ps-3 mt-2">
          <li v-for="subcategory in category.children" :key="subcategory.id" class="mb-2">
            <RouterLink
              :to="{
                name: 'product',
                query: {
                  category_id: subcategory.id,
                },
              }"
              class="text-decoration-none"
            >
              {{ subcategory.name }}
            </RouterLink>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>
