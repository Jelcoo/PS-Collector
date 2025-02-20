<script setup lang="ts">
import { defineProps, defineEmits, computed } from 'vue';

export type ButtonProps = {
    shape?: 'iconSquare';
    size?: 'small' | 'large';
    variant?: 'primary' | 'secondary' | 'text' | 'danger';
    disabled?: boolean;
};

const props = defineProps<ButtonProps>();
const emit = defineEmits(['click']);

const buttonClasses = computed(() =>
    [
        'button',
        'primary',
        props.variant === 'secondary' && 'secondary',
        props.variant === 'text' && 'text',
        props.variant === 'danger' && 'danger',
        props.shape === 'iconSquare' && 'square',
        props.size === 'small' && 'small',
        props.size === 'large' && 'large',
    ]
        .filter(Boolean)
        .join(' '),
);
</script>

<template>
    <button :class="buttonClasses" :disabled="props.disabled" @click="emit('click')">
        <slot />
    </button>
</template>

<style scoped lang="postcss">
@reference '../assets/main.css';

.button {
    @apply px-4 py-2 inline-flex items-center justify-center;
    @apply cursor-pointer rounded text-base font-semibold transition-all duration-100;

    /* Sizing Controls */
    &.small {
        @apply px-4 py-0 h-8 font-normal text-sm;
    }

    &.large {
        @apply px-5 py-3;
    }

    &.secondary {
        @apply text-gray-50 bg-transparent;

        &:disabled {
            background: transparent !important;
        }
    }

    &:disabled {
        @apply cursor-not-allowed;
    }

    &.square {
        @apply p-0 w-12 h-12;

        &.small {
            @apply w-8 h-8;
        }
    }
}

.primary {
    @apply bg-sky-600 text-sky-50;
    @apply hover:bg-sky-500 active:bg-sky-500;

    &.secondary {
        @apply hover:bg-sky-600 active:bg-sky-600;
    }

    &:disabled {
        @apply bg-sky-500/75 text-sky-200/75;
    }
}

.text {
    @apply bg-neutral-500 text-neutral-50;
    @apply hover:bg-neutral-400 active:bg-neutral-400;

    &.secondary {
        @apply hover:bg-neutral-500 active:bg-neutral-500;
    }

    &:disabled {
        @apply bg-neutral-500/75 text-neutral-200/75;
    }
}

.danger {
    @apply bg-red-600 text-gray-50;
    @apply hover:bg-red-500 active:bg-red-500;

    &.secondary {
        @apply hover:bg-red-600 active:bg-red-600;
    }

    &:disabled {
        @apply bg-red-600/75 text-red-50/75;
    }
}
</style>
