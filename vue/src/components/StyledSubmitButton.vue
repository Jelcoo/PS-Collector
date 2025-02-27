<template>
    <button
        type="submit"
        class="cursor-pointer w-full p-3 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded transition"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>

<script setup lang="ts">
defineProps<{
    disabled?: boolean;
}>();
</script>
