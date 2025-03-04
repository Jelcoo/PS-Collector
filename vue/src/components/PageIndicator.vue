<template>
    <slot />
    <div class="flex items-center justify-between my-2">
        <p className="text-sm text-neutral-500">
            Showing&nbsp;
            <span className="font-semibold text-neutral-400">
                {{ Math.max(start, Math.min(pagination.totalRecords, 1)) }}
            </span>
            &nbsp;to&nbsp;
            <span className="font-semibold text-neutral-400">{{ end }}</span> of&nbsp;
            <span className="font-semibold text-neutral-400">{{ props.pagination.totalRecords }}</span> results.
        </p>
        <div className="flex space-x-1" v-if="pagination.totalPages > 1">
            <StyledButton
                v-bind="buttonProps"
                @click="emit('pageSelect', 1)"
                :disabled="pagination.previousPages.length !== 2"
            >
                <FontAwesomeIcon :icon="faAnglesLeft" class="w-3 h-3" />
            </StyledButton>
            <StyledButton
                v-for="value in pagination.previousPages"
                :key="`previous-${value}`"
                @click="emit('pageSelect', value)"
                v-bind="buttonProps"
            >
                {{ value }}
            </StyledButton>
            <StyledButton size="small" shape="iconSquare">{{ pagination.currentPage }}</StyledButton>
            <StyledButton
                v-for="value in pagination.nextPages"
                :key="`next-${value}`"
                @click="emit('pageSelect', value)"
                v-bind="buttonProps"
            >
                {{ value }}
            </StyledButton>
            <StyledButton
                v-bind="buttonProps"
                @click="emit('pageSelect', pagination.totalPages)"
                :disabled="pagination.nextPages.length !== 2"
            >
                <FontAwesomeIcon :icon="faAnglesRight" class="w-3 h-3" />
            </StyledButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { PaginatedResponse } from '@/axios';
import { faAnglesLeft, faAnglesRight } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { computed } from 'vue';
import StyledButton, { type ButtonProps } from '@/components/StyledButton.vue';

const props = defineProps<{
    pagination: PaginatedResponse;
}>();
const emit = defineEmits(['pageSelect']);

const buttonProps: ButtonProps = {
    variant: 'text',
    size: 'small',
    shape: 'iconSquare',
};

const start = computed(() => {
    return (props.pagination.currentPage - 1) * props.pagination.recordsPerPage;
});
const end = computed(() => {
    return start.value + props.pagination.recordsThisPage;
});
</script>
