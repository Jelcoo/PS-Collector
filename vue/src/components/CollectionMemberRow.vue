<template>
    <tr class="hover:bg-neutral-400/25">
        <td class="px-6 py-4 whitespace-nowrap">
            {{ member.username }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ member.role }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <StyledButton v-if="member.role !== 'owner'" size="small" variant="danger" @click="removeMember">{{
                $t('common.remove')
            }}</StyledButton>
        </td>
    </tr>
</template>

<script setup lang="ts">
import type { CollectionMember } from '@/stores/types';
import StyledButton from './StyledButton.vue';
import { useCollectionStore } from '@/stores/collection';

const emit = defineEmits(['remove']);
const props = defineProps<{
    member: CollectionMember;
}>();

const collectionStore = useCollectionStore();

const removeMember = () => {
    collectionStore
        .removeMember(props.member.collection_id, props.member.user_id)
        .then(() => {
            emit('remove');
        })
        .catch((error) => {
            console.error(error);
        });
};
</script>
