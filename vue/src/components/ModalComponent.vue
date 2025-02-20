<template>
    <div
        v-if="visible"
        class="fixed top-0 left-0 w-full h-full bg-neutral-900/50 backdrop-blur-xs flex items-center justify-center"
    >
        <div class="relative bg-neutral-600 rounded max-w-xl w-full mx-auto shadow-lg text-left" ref="modal">
            <div class="flex p-6 pb-0 overflow-y-auto">
                <div class="flex-1 max-h-[70vh] min-w-0">
                    <div class="flex items-center">
                        <div>
                            <h2 class="font-header text-xl font-medium mb-2 text-gray-50 pr-4">{{ title }}</h2>
                            <p>{{ description }}</p>
                        </div>
                    </div>
                    <slot />
                    <div class="invisible h-6" />
                </div>
            </div>

            <div className="px-6 py-3 bg-neutral-700 flex items-center justify-end space-x-3 rounded-b">
                <slot name="footer" />
            </div>

            <div className="absolute right-0 top-0 m-4">
                <StyledButton variant="text" size="small" shape="iconSquare" @click="emit('close')" class="group">
                    <FontAwesomeIcon
                        :icon="faXmark"
                        class="w-5 h-5 group-hover:rotate-90 transition-transform duration-100"
                    />
                </StyledButton>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onClickOutside } from '@vueuse/core';
import { ref } from 'vue';
import StyledButton from '@/components/StyledButton.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faXmark } from '@fortawesome/free-solid-svg-icons';

defineProps<{
    visible: boolean;
    title: string;
    description?: string;
}>();
const emit = defineEmits(['close']);

const modal = ref(null);

onClickOutside(modal, () => emit('close'));
</script>
