<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { RouterLink } from 'vue-router'

import { getAddresses } from '@/services/addressService'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

import type { UserAddress } from '@/types/address'
import type { CheckoutForm } from '@/types/checkout'

const authStore = useAuthStore()
const cartStore = useCartStore()

const addresses = ref<UserAddress[]>([])
const isLoading = ref(false)
const errorMessage = ref('')

const form = reactive<CheckoutForm>({
    purchaser: {
        name: '',
        phone: '',
        email: '',
    },
    recipient: {
        name: '',
        phone: '',
        district_id: null,
        address: '',
    },
    shipping_method: 'home_delivery',
    payment_method: '',
})

const applyAddress = (address: UserAddress) => {
    form.recipient.name = address.recipient_name
    form.recipient.phone = address.recipient_phone
    form.recipient.district_id = address.district.id
    form.recipient.address = address.address
}

const loadCheckoutData = async () => {
    errorMessage.value = ''

    const currentUser = authStore.currentUser

    if (currentUser === null) {
        errorMessage.value = '無法取得會員資料，請重新登入。'
        return
    }

    form.purchaser.name = currentUser.name
    form.purchaser.phone = currentUser.phone
    form.purchaser.email = currentUser.email

    isLoading.value = true

    try {
        const [cartResponse, addressResponse] =
            await Promise.all([
                cartStore.fetchMemberCart(),
                getAddresses(),
            ])

        addresses.value = addressResponse.data

        if (cartResponse.data.items.length === 0) {
            errorMessage.value =
                '購物車目前沒有商品，請先加入商品後再結帳。'
            return
        }

        if (cartResponse.data.has_unavailable_items) {
            errorMessage.value =
                '購物車內有無法購買的商品，請先回購物車處理。'
            return
        }

        const defaultAddress =
            addresses.value.find(
                (address) => address.is_default,
            ) ??
            addresses.value[0] ??
            null

        if (defaultAddress !== null) {
            applyAddress(defaultAddress)
        }
    } catch {
        errorMessage.value =
            '結帳資料載入失敗，請稍後再試。'
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    loadCheckoutData()
})
</script>

<template>
    <div class="container py-5">
        <h1 class="mb-4">
            結帳
        </h1>

        <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border" role="status" aria-label="載入中"></div>

            <p class="text-muted mt-3 mb-0">
                正在載入結帳資料...
            </p>
        </div>

        <div v-else-if="errorMessage" class="alert alert-warning" role="alert">
            <p class="mb-3">
                {{ errorMessage }}
            </p>

            <RouterLink class="btn btn-outline-dark" :to="{ name: 'cart' }">
                返回購物車
            </RouterLink>
        </div>

        <div v-else class="card">
            <div class="card-body">
                <h2 class="h5">
                    結帳資料載入完成
                </h2>

                <p class="mb-1">
                    訂購人：{{ form.purchaser.name }}
                </p>

                <p class="mb-1">
                    購物車數量：{{ cartStore.memberItemCount }}
                </p>

                <p class="mb-0">
                    地址簿：{{ addresses.length }} 筆
                </p>
            </div>
        </div>
    </div>
</template>