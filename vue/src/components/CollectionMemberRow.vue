<template>
    <tr class="hover:bg-neutral-400/25">
        <td class="px-6 py-4 whitespace-nowrap">
            {{ member.username }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ member.role }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <StyledButton v-if="member.role !== 'owner'" size="small" variant="danger">Remove</StyledButton>
        </td>
    </tr>
</template>

<script setup lang="ts">
import type { CollectionMember } from '@/stores/types';
import StyledButton from './StyledButton.vue';

defineProps<{
    member: CollectionMember;
}>();
</script>
