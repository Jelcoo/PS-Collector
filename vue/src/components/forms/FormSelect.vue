<template>
    <label class="block text-neutral-300 mb-2" :for="name">{{ label }}</label>
    <Field :name="name" v-slot="{ field }">
        <select
            v-bind="field"
            class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none"
            :class="{ 'cursor-not-allowed': disabled }"
            :disabled
        >
            <option value="" disabled selected>Select your {{ label.toLowerCase() }}</option>
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </select>
    </Field>
    <ErrorMessage :name="name" class="text-red-400 text-sm mt-1" />
</template>

<script setup lang="ts">
import { Field, ErrorMessage } from 'vee-validate';

defineProps<{
    name: string;
    label: string;
    options: { value: string | number; label: string }[];
    disabled?: boolean;
}>();
</script>
