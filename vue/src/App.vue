<script setup lang="ts">
import { RouterView } from 'vue-router';
import { onBeforeMount } from 'vue';
import { useUserStore } from '@/stores/user';
import NavbarComponent from '@/components/NavbarComponent.vue';

onBeforeMount(() => {
    const userStore = useUserStore();
    userStore.autoLogin();
});
</script>

<template>
    <NavbarComponent />

    <RouterView />
</template>
