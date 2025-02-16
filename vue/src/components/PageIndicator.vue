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
            <StyledButton v-bind="buttonProps" :disabled="pagination.previousPages.length !== 2">
                <FontAwesomeIcon :icon="faChevronLeft" class="w-3 h-3" />
            </StyledButton>
            <StyledButton v-for="value in pagination.previousPages" :key="`previous-${value}`" v-bind="buttonProps">
                {{ value }}
            </StyledButton>
            <StyledButton size="small" shape="iconSquare">{{ pagination.currentPage }}</StyledButton>
            <StyledButton v-for="value in pagination.nextPages" :key="`next-${value}`" v-bind="buttonProps">
                {{ value }}
            </StyledButton>
            <StyledButton v-bind="buttonProps" :disabled="pagination.nextPages.length !== 2">
                <FontAwesomeIcon :icon="faChevronRight" class="w-3 h-3" />
            </StyledButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { PaginatedResponse } from '@/axios';
import { faChevronLeft, faChevronRight } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { computed } from 'vue';
import StyledButton, { type ButtonProps } from '@/components/StyledButton.vue';

const props = defineProps<{
    pagination: PaginatedResponse;
}>();

const buttonProps: ButtonProps = {
    variant: 'text',
    size: 'small',
    shape: 'iconSquare',
};

const start = computed(() => {
    return (props.pagination.currentPage - 1) * props.pagination.recordsPerPage;
});
const end = computed(() => {
    return (props.pagination.currentPage - 1) * props.pagination.recordsPerPage + props.pagination.totalPages;
});
</script>
