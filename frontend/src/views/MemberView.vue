<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
    await authStore.logout()

    await router.push({ name: 'login' })
}
</script>

<template>
    <div class="container py-5">
        <h1>會員中心</h1>
    </div>

    <button type="button" class="btn btn-outline-dark mt-3" @click="handleLogout">
        登出
    </button>
</template>