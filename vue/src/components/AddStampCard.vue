<template>
    <RouterLink
        :to="`/collections/${collection.id}/stamps/add`"
        class="bg-neutral-600 shadow-md rounded-lg p-4 w-64 space-y-3 border-4 border-dashed border-neutral-500 hover:border-neutral-400 hover:bg-neutral-500"
    >
        <div class="flex items-center justify-center">
            <h2 class="text-2xl font-bold"><FontAwesomeIcon :icon="faPlus" class="mr-2" /> Add stamp</h2>
        </div>
    </RouterLink>
</template>

<script setup lang="ts">
import { type Collection } from '@/stores/types';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPlus } from '@fortawesome/free-solid-svg-icons';

defineProps<{
    collection: Collection;
}>();
</script>
