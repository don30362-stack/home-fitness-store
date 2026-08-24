<script setup lang="ts"></script>

<template>
    <div class="container py-5 text-center">
        <h1 class="display-4 fw-bold">404</h1>

        <p class="mb-4">
            找不到你要瀏覽的頁面
        </p>

        <RouterLink class="btn btn-dark" :to="{ name: 'home' }">
            返回首頁
        </RouterLink>
    </div>
</template>