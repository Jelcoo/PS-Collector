<template>
    <ForbiddenView v-if="status === 403" />
    <NotFoundView v-else-if="status === 404" />
    <ContainerComponent v-else :loading="loading || !collection">
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
import ForbiddenView from '../status/ForbiddenView.vue';
import NotFoundView from '../status/NotFoundView.vue';

const collectionStore = useCollectionStore();
const route = useRoute();

const loading = ref(true);
const collection = ref<Collection>();
const status = ref(0);

onBeforeMount(() => {
    const collectionId = Number(route.params.id);
    collectionStore
        .getCollection(collectionId)
        .then((res) => {
            collection.value = res.data;
            loading.value = false;
        })
        .catch((error) => {
            status.value = error.status;
        });
});
</script>
