<template>
    <ContainerComponent :loading="!collection">
        <CollectionCreateView :collection="collection" />
    </ContainerComponent>
</template>

<script setup lang="ts">
import { useCollectionStore } from '@/stores/collection';
import { onBeforeMount, ref } from 'vue';
import { useRoute } from 'vue-router';
import CollectionCreateView from './CollectionCreateView.vue';
import type { Collection } from '@/stores/types';
import ContainerComponent from '@/components/ContainerComponent.vue';

const collectionStore = useCollectionStore();
const route = useRoute();
const collection = ref<Collection>();

onBeforeMount(() => {
    const collectionId = Number(route.params.id);
    collectionStore.getCollection(collectionId).then((res) => {
        collection.value = res.data;
    });
});
</script>
