<template>
    <div class="flex flex-col">
        <div class="flex flex-col">
            <label class="block text-neutral-300 mb-2" :for="name">{{ label }}</label>
            <Field
                :name
                :type="type ?? 'text'"
                class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none transition-all duration-200"
                :class="{ 'cursor-not-allowed': disabled, 'opacity-60': disabled }"
                :placeholder="placeholder ?? $t('forms.enter_your', { field: label.toLowerCase() })"
                :disabled
            />
        </div>
        <ErrorMessage :name class="text-red-400 text-sm mt-1" />
    </div>
</template>

<script setup lang="ts">
import { Field, ErrorMessage } from 'vee-validate';

defineProps<{
    name: string;
    label: string;
    type?: string;
    placeholder?: string;
    disabled?: boolean;
}>();
</script>
