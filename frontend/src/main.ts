import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import { useAuthStore } from '@/stores/auth'

const bootstrap = async () => {
    const app = createApp(App)
    const pinia = createPinia()

    app.use(pinia)

    const authStore = useAuthStore(pinia)

    await authStore.restoreAuth()

    app.use(router)

    app.mount('#app')
}

void bootstrap()
