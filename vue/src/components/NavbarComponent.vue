<template>
    <nav class="flex flex-col md:flex-row justify-between m-4 md:m-12">
        <RouterLink to="/" class="mb-4 md:mt-0 mx-auto md:mx-0 md:mr-12 flex">
            <img src="@/assets/logo.svg" alt="Logo" class="w-8 h-8 mr-4" />
            <span class="text-2xl font-bold">PS Collector</span>
        </RouterLink>
        <div class="flex flex-col gap-x-8 gap-y-4 md:flex-row items-center">
            <RouterLink to="/" active-class="active" class="hovertransition text-xl">{{
                $t('common.home')
            }}</RouterLink>
            <select
                name="language"
                id="language"
                class="w-fit p-3 mx-auto bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none"
                @change="onChangeLanguage"
                v-model="$i18n.locale"
            >
                <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">
                    {{ locale }}
                </option>
            </select>
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

const onChangeLanguage = (event: Event) => {
    const selectedLanguage = (event.target as HTMLSelectElement).value;
    localStorage.setItem('language', selectedLanguage);
};
</script>
