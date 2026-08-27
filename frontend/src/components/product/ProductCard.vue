<script setup lang="ts">
import { computed, ref } from 'vue';

import type { ProductListItem } from '@/types/product';

const props = defineProps<{ product: ProductListItem }>()

const imageLoadFailed = ref(false)

const primaryImage = computed(() => {
    return props.product.images.find((image) => image.is_primary)
})
</script>

<template>
    <div class="card h-100">
        <img v-if="primaryImage && !imageLoadFailed" :src="primaryImage.image_url" :alt="product.name"
            class="card-img-top" style="height: 240px; object-fit: contain;" @error="imageLoadFailed = true">

        <div v-else class="bg-light d-flex align-items-center justify-content-center" style="height: 240px;">
            <span class="text-muted">
                商品圖片準備中
            </span>
        </div>

        <div class="card-body d-flex flex-column">
            <h2 class="h5 card-title">
                {{ product.name }}
            </h2>

            <p class="fw-bold mb-3">
                NT$ {{ Number(product.price).toLocaleString('zh-TW') }}
            </p>

            <RouterLink class="btn btn-dark mt-auto" :to="{ name: 'product-detail', params: { id: product.id } }">
                查看商品
            </RouterLink>
        </div>
    </div>
</template>