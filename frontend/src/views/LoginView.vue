<script setup lang="ts">
import axios from 'axios';
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');

const isSubmitting = ref(false);
const errorMessage = ref('');

const isRegistered = computed(() => {
    return route.query.registered === '1'
});

const handleLogin = async () => {
    if (isSubmitting.value) {
        return
    }

    errorMessage.value = ''
    isSubmitting.value = false

    try {
        await authStore.login({
            email: email.value,
            password: password.value
        });

        const redirect = typeof route.query.redirect === 'string' ? route.query.redirect : '/member';

        await router.push(redirect)
    } catch (error) {
        if (axios.isAxiosError(error)) {
            errorMessage.value = error.response?.data?.mseeage ?? '登入失敗，請稍後再試'
        } else {
            errorMessage.value = '登入失敗，請稍後再試'
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>

<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <h1 class="h2 mb-4 text-center">會員登入</h1>

                <div v-if="isRegistered" class="alert alert-success" role="alert">會員註冊成功，請登入。</div>

                <div v-if="errorMessage" class="alert alert-danger" role="alert">
                    {{ errorMessage }}
                </div>

                <form @submit.prevent="handleLogin">
                    <div class="mb-3">
                        <label for="email" class="form-label">電子郵件</label>
                        <input v-model.trim="email" type="email" id="email" class="form-control" autocomplete="email"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">密碼</label>
                        <input v-model="password" type="password" id="password" class="form-control"
                            autocomplete="current-password" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">
                        {{ isSubmitting ? '登入中...' : '登入' }}
                    </button>
                </form>

                <p class="text-center mt-4 mb-0">
                    還沒有會員帳號？
                    <RouterLink :to="{ name: 'register' }">
                        立即註冊
                    </RouterLink>
                </p>
            </div>
        </div>
    </div>
</template>