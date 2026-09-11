<script setup lang="ts">
import { getAddresses } from '@/services/addressService';
import type { UserAddress } from '@/types/address';
import { ref, onMounted } from 'vue';

const addresses = ref<UserAddress[]>([])
const isLoading = ref(true)
const errorMessage = ref('')

const loadAddresses = async () => {
    isLoading.value = true
    errorMessage.value = ''

    try {
        const response = await getAddresses()
        addresses.value = response.data
    } catch {
        errorMessage.value = '地址資料載入失敗，請稍後再試'
    } finally {
        isLoading.value = false
    }
}

onMounted(() => { loadAddresses() })
</script>

<template>
    <section>
        <div class="d-flex justify-content-between align-content-center mb-4">
            <h2 class="h4 mb-0">地址簿</h2>
        </div>

        <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border text-dark" role="status" aria-label="地址資料載入中"></div>
            <p class="text-muted mt-3 mb-0">地址資料載入中...</p>
        </div>

        <div v-else-if="errorMessage" class="alert alert-danger" role="alert">
            <p class="mb-2">{{ errorMessage }}</p>
            <button @click="loadAddresses" type="button" class="btn btn-sm btn-outline-danger">
                重新載入
            </button>
        </div>

        <div v-else-if="addresses.length === 0" class="card">
            <div class="card-body py-5 text-center">
                <p class="text-muted mb-0">尚未建立收件地址</p>
            </div>
        </div>

        <div v-else class="row g-3">
            <div v-for="address in addresses" :key="address.id" class="col-12">
                <artical class="card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-self-start gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h3 class="h6 mb-0">{{ address.label }}</h3>
                                    <span v-if="address.is_default" class="badge text-bg-dark">預設地址</span>
                                </div>

                                <p class="mb-1">
                                    {{ address.recipient_name }}
                                    <span class="text-muted ms-2">{{ address.recipient_phone }}</span>
                                </p>

                                <address class="text-muted mb-0">
                                    {{ address.district.postal_code }}
                                    {{ address.district.city.name }}
                                    {{ address.district.name }}
                                    {{ address.address }}
                                </address>
                            </div>
                        </div>
                    </div>
                </artical>
            </div>
        </div>
    </section>
</template>