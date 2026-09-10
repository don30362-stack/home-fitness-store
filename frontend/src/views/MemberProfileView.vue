<script setup lang="ts">
import axios from 'axios';
import { reactive, ref, watch } from 'vue';

import { useAuthStore } from '@/stores/auth';

import type { ApiErrorResponse } from '@/types/api';
import type { UpdateProfilePayload } from '@/types/auth';

const authStore = useAuthStore()

const profileForm = reactive<UpdateProfilePayload>({
    name: '',
    email: '',
    phone: '',
})

const profileMessage = ref('')
const profileErrorMessage = ref('')
const isProfileSubmitting = ref(false)
const profileFieldErrors = ref<Record<string, string[]>>({})

watch(
    () => authStore.currentUser,
    (user) => {
        if (!user) {
            return
        }

        profileForm.name = user.name
        profileForm.email = user.email
        profileForm.phone = user.phone
    },
    {
        immediate: true,
    }
)

const handleProfileUpdate = async () => {
    if (isProfileSubmitting.value) {
        return
    }

    isProfileSubmitting.value = true
    profileMessage.value = ''
    profileErrorMessage.value = ''
    profileFieldErrors.value = {}

    try {
        const response = await authStore.updateProfile({
            name: profileForm.name.trim(),
            email: profileForm.email.trim(),
            phone: profileForm.phone.trim(),
        })

        profileMessage.value = response.message
    } catch (error) {
        if (axios.isAxiosError<ApiErrorResponse>(error)) {
            if (error.response?.status === 422) {
                profileFieldErrors.value = error.response.data.errors ?? {}
            }

            profileErrorMessage.value = error.response?.data.message ?? '會員資料更新失敗，請稍後再試'
        } else {
            profileErrorMessage.value = '會員資料更新失敗，請稍後再試'
        }
    } finally {
        isProfileSubmitting.value = false
    }
}
</script>

<template>
    <section>
        <h2 class="h4 mb-4">基本資料</h2>

        <div v-if="profileMessage" class="alert alert-success" role="alert">
            {{ profileMessage }}
        </div>

        <div v-if="profileErrorMessage" class="alert alert-danger" role="alert">
            {{ profileErrorMessage }}
        </div>

        <div class="card">
            <div class="card-body p-4">
                <form @submit.prevent="handleProfileUpdate">
                    <div class="mb-3">
                        <label for="profileName" class="form-label">姓名</label>

                        <input v-model="profileForm.name" type="text" id="profileName" class="form-control"
                            :class="{ 'is-invalid': profileFieldErrors.name }" maxlength="50" autocomplete="name" required>

                        <div v-if="profileFieldErrors.name" class="invalid-feedback">
                            {{ profileFieldErrors.name[0] }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="profileEmail" class="form-label">電子郵件</label>

                        <input v-model="profileForm.email" type="email" id="profileEmail" class="form-control"
                            :class="{ 'is-invalid': profileFieldErrors.email }" maxlength="255" autocomplete="email" required>

                        <div v-if="profileFieldErrors.email" class="invalid-feedback">
                            {{ profileFieldErrors.email[0] }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="profilePhone" class="form-label">手機號碼</label>

                        <input v-model="profileForm.phone" type="tel" id="profilePhone" class="form-control"
                            :class="{ 'is-invalid': profileFieldErrors.phone }" maxlength="10" pattern="09[0-9]{8}"
                            inputmode="numeric" autocomplete="tel" required>

                        <div v-if="profileFieldErrors.phone" class="invalid-feedback">
                            {{ profileFieldErrors.phone[0] }}
                        </div>

                        <div class="form-text">請輸入 09 開頭的 10 碼手機號碼</div>
                    </div>

                    <button type="submit" class="btn btn-dark" :disabled="isProfileSubmitting">
                        {{ isProfileSubmitting ? '儲存中...' : '儲存基本資料' }}
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>