<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

import ProductCard from '@/components/product/ProductCard.vue'
import ProductCategoryNav from '@/components/product/ProductCategoryNav.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import { getProducts } from '@/services/productService'
import type { ProductListItem, ProductSort } from '@/types/product'
import type { ApiErrorResponse, PaginationMeta } from '@/types/api'

const products = ref<ProductListItem[]>([])
const pagination = ref<PaginationMeta>({
  current_page: 1,
  last_page: 1,
  per_page: 8,
  total: 0,
})
const isLoading = ref(false)
const errorMessage = ref('')
const route = useRoute()
const router = useRouter()
const searchKeyword = ref('')
const sortOption = ref<ProductSort | ''>('')
const minPriceInput = ref<number | ''>('')
const maxPriceInput = ref<number | ''>('')
const priceFilterError = ref('')

const hasQueryConditions = computed(() => {
  return (
    route.query.search !== undefined ||
    route.query.category_id !== undefined ||
    route.query.parent_category_id !== undefined ||
    route.query.min_price !== undefined ||
    route.query.max_price !== undefined
  )
})

const fetchProducts = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const categoryId = Number(route.query.category_id)
    const parentCategoryId = Number(route.query.parent_category_id)
    const page = Number(route.query.page ?? 1)
    const search =
      typeof route.query.search === 'string' ? route.query.search.trim() || undefined : undefined
    const sort =
      route.query.sort === 'price_asc' || route.query.sort === 'price_desc'
        ? route.query.sort
        : undefined
    const minPrice =
      typeof route.query.min_price === 'string' && route.query.min_price !== ''
        ? Number(route.query.min_price)
        : undefined
    const maxPrice =
      typeof route.query.max_price === 'string' && route.query.max_price !== ''
        ? Number(route.query.max_price)
        : undefined

    // Number(undefined) 在 JavaScript 中會轉換成 0，所以下面不能用categoryId != undefined會變成true
    const hasInvalidCategoryId =
      route.query.category_id != undefined && (!Number.isInteger(categoryId) || categoryId <= 0)

    const hasInvalidParentCategoryId =
      route.query.parent_category_id != undefined &&
      (!Number.isInteger(parentCategoryId) || parentCategoryId <= 0)

    const hasInvalidPage = route.query.page != undefined && (!Number.isInteger(page) || page <= 0)

    const hasInvalidMinPrice =
      minPrice !== undefined && (!Number.isFinite(minPrice) || minPrice < 0)

    const hasInvalidMaxPrice =
      maxPrice !== undefined && (!Number.isFinite(maxPrice) || maxPrice < 0)

    const hasInvalidPriceRange =
      minPrice !== undefined && maxPrice !== undefined && minPrice > maxPrice

    const hasInvalidSort =
      route.query.sort !== undefined &&
      route.query.sort !== 'price_asc' &&
      route.query.sort !== 'price_desc'

    if (
      hasInvalidCategoryId ||
      hasInvalidParentCategoryId ||
      hasInvalidPage ||
      hasInvalidMinPrice ||
      hasInvalidMaxPrice ||
      hasInvalidPriceRange ||
      hasInvalidSort
    ) {
      products.value = []
      errorMessage.value = '查詢條件格式錯誤'
      return
    }

    const response = await getProducts({
      search,
      category_id: route.query.category_id !== undefined ? categoryId : undefined,
      parent_category_id:
        route.query.parent_category_id !== undefined ? parentCategoryId : undefined,
      min_price: minPrice,
      max_price: maxPrice,
      sort,
      page: route.query.page != undefined ? page : undefined,
    })

    if (page > response.meta.last_page) {
      const query = { ...route.query }

      if (response.meta.last_page === 1) {
        delete query.page
      } else {
        query.page = String(response.meta.last_page)
      }

      await router.replace({
        query,
      })

      return
    }

    products.value = response.data
    pagination.value = response.meta
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

const handlePageChange = (page: number) => {
  router.push({
    query: {
      ...route.query,
      page: page,
    },
  })
}

const handleSearch = () => {
  const query = { ...route.query }

  delete query.page

  const search = searchKeyword.value.trim()

  if (search) {
    query.search = search
  } else {
    delete query.search
  }

  router.push({
    query,
  })
}

const handleSortChange = () => {
  const query = { ...route.query }

  delete query.page

  if (sortOption.value) {
    query.sort = sortOption.value
  } else {
    delete query.sort
  }

  router.push({
    query,
  })
}

const handlePriceFilter = () => {
  priceFilterError.value = ''

  const minPrice = minPriceInput.value === '' ? undefined : minPriceInput.value
  const maxPrice = maxPriceInput.value === '' ? undefined : maxPriceInput.value

  if (
    (minPrice !== undefined && (!Number.isFinite(minPrice) || minPrice < 0)) ||
    (maxPrice !== undefined && (!Number.isFinite(maxPrice) || maxPrice < 0))
  ) {
    priceFilterError.value = '請輸入有效的價格'
    return
  }

  if (minPrice !== undefined && maxPrice !== undefined && minPrice > maxPrice) {
    priceFilterError.value = '最低價格不得高於最高價格'
    return
  }

  const query = { ...route.query }

  delete query.page

  if (minPrice !== undefined) {
    query.min_price = String(minPrice)
  } else {
    delete query.min_price
  }

  if (maxPrice !== undefined) {
    query.max_price = String(maxPrice)
  } else {
    delete query.max_price
  }

  router.push({
    query,
  })
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

watch(
  () => route.query.search,
  (search) => {
    searchKeyword.value = typeof search === 'string' ? search : ''
  },
  {
    immediate: true,
  },
)

watch(
  () => route.query.sort,
  (sort) => {
    if (sort === 'price_asc' || sort === 'price_desc') {
      sortOption.value = sort
    } else {
      sortOption.value = ''
    }
  },
  {
    immediate: true,
  },
)

watch(
  () => [route.query.min_price, route.query.max_price],
  ([minprice, maxprice]) => {
    minPriceInput.value = typeof minprice === 'string' && minprice !== '' ? Number(minprice) : ''
    maxPriceInput.value = typeof maxprice === 'string' && maxprice !== '' ? Number(maxprice) : ''
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

        <form class="mb-4" @submit.prevent="handleSearch">
          <div class="input-group">
            <input
              v-model="searchKeyword"
              type="search"
              class="form-control"
              placeholder="搜尋商品名稱"
              aria-label="搜尋商品名稱"
            />
            <button class="btn btn-dark" type="submit">搜尋</button>
          </div>
        </form>

        <div class="d-flex justify-content-end mb-4">
          <select
            v-model="sortOption"
            class="form-select w-auto"
            aria-label="商品排序"
            @change="handleSortChange"
          >
            <option value="">預設排序</option>
            <option value="price_asc">價格低到高</option>
            <option value="price_desc">價格高到低</option>
          </select>
        </div>

        <form class="mb-4" @submit.prevent="handlePriceFilter">
          <div class="row align-items-end">
            <div class="col-12 col-md">
              <label for="min-price" class="form-label"> 最低價格 </label>
              <input
                id="min-price"
                v-model="minPriceInput"
                type="number"
                min="0"
                class="form-control"
                placeholder="最低價格"
              />
            </div>

            <div class="col-12 col-md">
              <label for="max-price" class="form-label"> 最高價格 </label>
              <input
                id="max-price"
                v-model="maxPriceInput"
                type="number"
                min="0"
                class="form-control"
                placeholder="最高價格"
              />
            </div>

            <div class="col-12 col-md-auto">
              <button type="submit" class="btn btn-outline-dark">套用價格</button>
            </div>
          </div>

          <p v-if="priceFilterError" class="text-danger mt-2 mb-0">
            {{ priceFilterError }}
          </p>
        </form>

        <p v-if="isLoading">商品載入中...</p>

        <p v-else-if="errorMessage" class="text-danger">{{ errorMessage }}</p>

        <div v-else-if="products.length === 0">
          <p class="text-muted">
            {{ hasQueryConditions ? '找不到符合條件的商品。' : '目前沒有商品。' }}
          </p>
        </div>

        <template v-else>
          <div class="row g-4">
            <div v-for="product in products" :key="product.id" class="col-12 col-sm-6 col-xl-4">
              <ProductCard :product="product" />
            </div>
          </div>

          <AppPagination
            :current-page="pagination.current_page"
            :last-page="pagination.last_page"
            @change-page="handlePageChange"
          />
        </template>
      </section>
    </div>
  </div>
</template>
