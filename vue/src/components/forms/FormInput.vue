<template>
    <div class="flex flex-col">
        <div class="flex items-center" v-if="type === 'checkbox'">
            <Field :name :type="type" v-slot="{ field }" :disabled :unchecked-value="false">
                <label class="relative flex items-center cursor-pointer">
                    <input v-bind="field" type="checkbox" class="sr-only peer" :disabled="disabled" />
                    <div
                        class="w-5 h-5 border-2 border-neutral-600 rounded-md peer-focus:ring-2 peer-focus:ring-sky-500 peer-checked:bg-sky-500 peer-checked:border-sky-500 after:content-[''] after:absolute after:opacity-0 after:h-2.5 after:w-1.5 after:border-r-2 after:border-b-2 after:border-white after:rotate-45 after:left-[7px] after:top-[4px] peer-checked:after:opacity-100 transition-all duration-200 peer-disabled:bg-neutral-700 peer-disabled:border-neutral-700 peer-disabled:cursor-not-allowed"
                    ></div>
                    <span class="ml-3 text-neutral-300" :class="{ 'cursor-not-allowed': disabled }">
                        {{ label }}
                    </span>
                </label>
            </Field>
        </div>

        <div class="flex flex-col" v-else>
            <label class="block text-neutral-300 mb-2" :for="name">{{ label }}</label>
            <Field
                :name
                :type="type ?? 'text'"
                class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none transition-all duration-200"
                :class="{ 'cursor-not-allowed': disabled, 'opacity-60': disabled }"
                :placeholder="placeholder ?? `Enter your ${label.toLowerCase()}`"
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
