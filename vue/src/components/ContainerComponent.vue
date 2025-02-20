<template>
    <div class="py-4 px-12">
        <div v-if="props.loading" class="flex justify-center">
            <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-white"></div>
        </div>
        <slot v-else />
    </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps<{
    loading?: boolean;
}>();
</script>
