<template>
    <div class="text-center text-green-500 mb-4" v-if="props.message">
        {{ props.message }}
    </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps<{
    message?: string;
}>();
</script>
