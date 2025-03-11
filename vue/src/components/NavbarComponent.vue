<template>
    <nav class="flex justify-between m-12">
        <RouterLink to="/" class="mr-12 flex">
            <img src="@/assets/logo.svg" alt="Logo" class="w-8 h-8 mr-4" />
            <span class="text-2xl font-bold">PS Collector</span>
        </RouterLink>
        <div class="flex items-center">
            <RouterLink to="/" active-class="active" class="hovertransition mr-8 text-xl">Home</RouterLink>
            <div class="flex">
                <template v-if="userStore.isAuthenticated">
                    <NavbarDropdown />
                </template>
                <template v-else>
                    <RouterLink
                        to="/auth/login"
                        active-class="bg-neutral-500"
                        class="px-4 py-2 mr-4 text-xl bg-neutral-600 rounded hover:bg-neutral-500"
                        >{{ $t('auth.login') }}</RouterLink
                    >
                    <RouterLink
                        to="/auth/register"
                        active-class="bg-sky-500"
                        class="px-4 py-2 text-xl bg-sky-600 rounded hover:bg-sky-500"
                        >{{ $t('auth.register') }}</RouterLink
                    >
                </template>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router';
import NavbarDropdown from '@/components/NavbarDropdown.vue';
import { useUserStore } from '@/stores/user';

const userStore = useUserStore();
</script>
