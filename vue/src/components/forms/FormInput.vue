<template>
    <label class="block text-neutral-300 mb-2" :for="name">{{ label }}</label>
    <Field
        :name
        :type
        class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none"
        :placeholder="`Enter your ${label.toLowerCase()}`"
    />
    <ErrorMessage :name class="text-red-400 text-sm mt-1" />
</template>

<script setup lang="ts">
import { Field, ErrorMessage } from 'vee-validate';

defineProps({
    name: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
});
</script>
