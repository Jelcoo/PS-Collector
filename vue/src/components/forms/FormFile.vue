<template>
    <div class="flex flex-col">
        <div class="flex flex-col">
            <label class="block text-neutral-300 mb-2" :for="name">{{ label }}</label>
            <Field :name type="file" v-slot="{ handleBlur, handleChange }">
                <div class="relative w-full" :class="{ 'cursor-not-allowed opacity-60': disabled }">
                    <input
                        type="file"
                        class="sr-only"
                        :id="name"
                        :disabled="disabled"
                        :accept="accept"
                        @change="
                            (e) => {
                                handleChange(e);
                                selectedFile = (e.target as HTMLInputElement).files?.[0]?.name || '';
                                $emit('change', e);
                            }
                        "
                        @blur="handleBlur"
                    />
                    <label
                        :for="name"
                        class="flex items-center w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 hover:border-neutral-500 focus-within:ring-2 focus-within:ring-sky-500 transition-all duration-200"
                        :class="{ 'cursor-not-allowed': disabled }"
                    >
                        <span class="flex-1 truncate">
                            {{ selectedFile || placeholder || $t('forms.choose_a_file') }}
                        </span>
                        <span
                            class="ml-2 px-3 py-1 bg-neutral-700 rounded-md text-sm text-neutral-300"
                            :class="{ 'cursor-not-allowed': disabled }"
                        >
                            {{ $t('common.choose') }}
                        </span>
                    </label>
                </div>
            </Field>
        </div>
        <ErrorMessage :name class="text-red-400 text-sm mt-1" />
    </div>
</template>

<script setup lang="ts">
import { Field, ErrorMessage } from 'vee-validate';
import { ref } from 'vue';

defineEmits(['change']);

defineProps<{
    name: string;
    label: string;
    placeholder?: string;
    disabled?: boolean;
    accept?: string;
}>();

const selectedFile = ref<string>('');
</script>
