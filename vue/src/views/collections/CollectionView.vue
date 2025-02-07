<template>
    <main>Welcome to collection</main>
    <div v-if="collectionStore.currentCollection.loading">Loading...</div>
    <div v-else>
        <h1>{{ collectionStore.currentCollection }}</h1>
    </div>
</template>

<script setup lang="ts">
import { useCollectionStore } from '@/stores/collection';
import { onBeforeMount } from 'vue';
import { useRoute } from 'vue-router';

const collectionStore = useCollectionStore();
const router = useRoute();

onBeforeMount(() => {
    const collectionId = router.params.id;
    collectionStore.getCollection(collectionId, ['stamps', 'author']);
});
</script>
