<template>
    <div v-if="collectionStore.currentCollection.loading">Loading...</div>
    <div v-else-if="notFound">
        <NotFoundView />
    </div>
    <div v-else>
        <h1>{{ collectionStore.currentCollection }}</h1>
    </div>
</template>

<script setup lang="ts">
import { useCollectionStore } from '@/stores/collection';
import { onBeforeMount, ref } from 'vue';
import { useRoute } from 'vue-router';
import NotFoundView from '@/views/NotFoundView.vue';

const collectionStore = useCollectionStore();
const route = useRoute();
const notFound = ref(false);

onBeforeMount(() => {
    const collectionId = Number(route.params.id);
    collectionStore.getCollection(collectionId, ['stamps', 'author']).catch((error) => {
        if (error.status === 404) {
            notFound.value = true;
        }
    });
});
</script>
