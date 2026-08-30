import { computed, ref } from "vue";
import { defineStore } from "pinia";

import { getMe, login as loginApi, logout as logoutApi, register as registerApi } from "@/services/authService";

import type { LoginPayload, RegisterPayload, User } from "@/types/auth";

export const useAuthStore = defineStore('auth', () => {
    const currentUser = ref<User | null>(null)
    const isAuthInitialized = ref(false)

    const isAuthenticated = computed(() => {
        return currentUser.value !== null
    })

    const register = async (payload: RegisterPayload) => {
        const response = await registerApi(payload)

        return response
    }

    const login = async (payload: LoginPayload) => {
        const response = await loginApi(payload)

        currentUser.value = response.data

        return response
    }

    const restoreAuth = async () => {
        try {
            const response = await getMe()
            currentUser.value = response.data
        } catch {
            currentUser.value = null
        } finally {
            isAuthInitialized.value = true
        }
    }

    const logout = async () => {
        await logoutApi()
        currentUser.value = null
    }

    return {
        currentUser,
        isAuthInitialized,
        isAuthenticated,
        register,
        login,
        restoreAuth,
        logout,
    }
})