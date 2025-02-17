<template>
    <button
        class="hovertransition relative px-4 py-2 mr-4 text-xl bg-neutral-600 rounded hover:brightness-[1.2]"
        :class="{ 'active brightness-[1.3]!': open }"
        @click="toggleOpen"
        tabindex="0"
        ref="dropdown"
    >
        <div id="userdisplay" class="flex items-center">
            <FontAwesomeIcon :icon="faUser" class="mr-2" />
            <span>{{ userStore.username }}</span>
        </div>

        <div
            v-if="open"
            id="dropdownitems"
            class="absolute top-full right-0 bg-neutral-600 p-2 rounded-b-lg w-full flex flex-col divide-y-1 divide-gray-500"
        >
            <RouterLink to="/account" class="block text-xl text-white no-underline py-1">Account</RouterLink>
            <button class="hover:cursor-pointer block text-xl text-white no-underline py-1" @click="logout">
                Logout
            </button>
        </div>
    </button>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { onClickOutside } from '@vueuse/core';
import { faUser } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useUserStore } from '@/stores/user';
import { useRouter } from 'vue-router';

const dropdown = ref(null);
const open = ref(false);
const userStore = useUserStore();
const router = useRouter();

const toggleOpen = () => {
    open.value = !open.value;
};

onClickOutside(dropdown, () => (open.value = false));

const logout = () => {
    userStore.logout();
    open.value = false;
    router.push('/');
};
</script>

<style scoped>
.hovertransition {
    &::before {
        background-color: #0ea5e9; /* sky-500 */
    }
}
</style>
