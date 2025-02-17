<script setup lang="ts">
import { RouterView } from 'vue-router';
import { onMounted } from 'vue';
import { useUserStore } from '@/stores/user';
import NavbarComponent from '@/components/NavbarComponent.vue';
import { useAppStore } from '@/stores/app';

onMounted(() => {
    const userStore = useUserStore();
    const appStore = useAppStore();
    userStore.autoLogin();
    appStore.init();
});
</script>

<template>
    <NavbarComponent />

    <RouterView />
</template>
