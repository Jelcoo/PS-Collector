<template>
    <RouterLink
        :to="`/collections/${collection.id}`"
        class="bg-neutral-600 shadow-md rounded-lg p-4 w-64 space-y-3 border border-neutral-500 hover:bg-neutral-500"
    >
        <div>
            <h2 class="text-xl font-bold truncate">{{ collection.name }}</h2>
            <p class="text-sm truncate">{{ $t('collections.by', { user: collection.authorName }) }}</p>
        </div>
        <div class="flex items-center justify-between">
            <span :class="['px-2 py-1 text-xs text-white rounded-full', accessLevelColor]">
                {{ $t(`collections.levels.${collection.access}`) }}
            </span>
            <div class="font-medium">{{ $t('collections.stamps', { count: collection.stampCount }) }}</div>
        </div>
    </RouterLink>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { type Collection } from '@/stores/types';

const props = defineProps<{
    collection: Collection;
}>();

const accessLevelColor = computed(() => {
    switch (props.collection.access) {
        case 'public':
            return 'bg-green-500';
        case 'private':
            return 'bg-red-500';
        case 'shared':
            return 'bg-blue-500';
        default:
            return 'bg-gray-500';
    }
});
</script>
