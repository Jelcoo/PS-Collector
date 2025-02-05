<template>
    <div class="p-4">
        <div class="flex flex-wrap gap-4 justify-center">
            <CollectionCard
                v-for="collection in collectionStore.collections"
                :key="collection.id"
                :collection
                :total-stamps="2"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import CollectionCard from '@/components/CollectionCard.vue';
import { useCollectionStore } from '@/stores/collection';
import { onBeforeMount } from 'vue';

const collectionStore = useCollectionStore();

onBeforeMount(() => {
    collectionStore.getCollections(['author', 'stampCount']);
});
</script>
