<template>
    <div v-if="collectionStore.currentCollection.loading">Loading...</div>
    <ForbiddenView v-else-if="status === 403" />
    <NotFoundView v-else-if="status === 404" />
    <div v-else>
        <h1>{{ collectionStore.currentCollection }}</h1>
    </div>
</template>

<script setup lang="ts">
import { useCollectionStore } from '@/stores/collection';
import { onBeforeMount, ref } from 'vue';
import { useRoute } from 'vue-router';
import NotFoundView from '@/views/status/NotFoundView.vue';
import ForbiddenView from '@/views/status/ForbiddenView.vue';

const collectionStore = useCollectionStore();
const route = useRoute();
const status = ref(0);

onBeforeMount(() => {
    const collectionId = Number(route.params.id);
    collectionStore.getCollection(collectionId, ['stamps', 'author']).catch((error) => {
        status.value = error.status;
    });
});
</script>
