<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faHouse } from '@fortawesome/free-solid-svg-icons';

const props = defineProps<{
    title: string;
    message: string;
}>();
</script>

<template>
    <div class="text-center">
        <h1 class="text-9xl font-extrabold">{{ props.title }}</h1>
        <p class="text-2xl font-semibold text-gray-400 mt-4">{{ props.message }}</p>
        <RouterLink
            to="/"
            class="bg-sky-600 mt-4 p-2 inline-block rounded-lg text-white font-semibold hover:bg-sky-500 border-2 border-sky-600"
            ><FontAwesomeIcon :icon="faHouse" class="mr-2" />{{ $t('status.back_to_home') }}</RouterLink
        >
    </div>
</template>
