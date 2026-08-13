<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
    <header class="border-bottom">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <RouterLink class="navbar-brand fw-bold" :to="{ name: 'home' }">
                    Home Fitness
                </RouterLink>

                <div class="navbar-nav ms-auto">
                    <RouterLink class="nav-link" :to="{name:'home'}">
                        首頁
                    </RouterLink>
                    <RouterLink class="nav-link" :to="{name:'products'}">
                        商品
                    </RouterLink>
                    <RouterLink class="nav-link" :to="{name:'about'}">
                        品牌介紹
                    </RouterLink>
                    <RouterLink class="nav-link" :to="{name:'cart'}">
                        購物車
                    </RouterLink>
                    <RouterLink class="nav-link" :to="{name:'login'}">
                        登入
                    </RouterLink>
                </div>
            </div>
        </nav>
    </header>
</template>