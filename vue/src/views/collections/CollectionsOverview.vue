<template>
    <ContainerComponent>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold mb-4">Collections</h1>
            <div class="flex gap-4">
                <button class="px-4 py-2 text-xl bg-sky-600 rounded hover:bg-sky-500">Filter</button>
                <RouterLink
                    v-if="userStore.isAuthenticated"
                    class="px-4 py-2 text-xl bg-emerald-600 rounded hover:bg-emerald-500"
                    to="/collections/create"
                >
                    Create new
                </RouterLink>
            </div>
        </div>

        <PageIndicator :pagination="collectionStore.collections.pages" @page-select="handlePageSelect">
            <div class="flex flex-wrap gap-4 justify-center">
                <CollectionCard
                    v-for="collection in collectionStore.collections.data"
                    :key="collection.id"
                    :collection
                    :total-stamps="2"
                />
            </div>
        </PageIndicator>
    </ContainerComponent>
</template>

<script setup lang="ts">
import CollectionCard from '@/components/CollectionCard.vue';
import PageIndicator from '@/components/PageIndicator.vue';
import { useCollectionStore } from '@/stores/collection';
import { useUserStore } from '@/stores/user';
import { onBeforeMount } from 'vue';
import { RouterLink } from 'vue-router';
import ContainerComponent from '@/components/ContainerComponent.vue';

const userStore = useUserStore();
const collectionStore = useCollectionStore();

onBeforeMount(() => {
    collectionStore.getCollections(['author', 'stampCount']);
});

function handlePageSelect(page: number) {
    collectionStore.getCollections(['author', 'stampCount'], page);
}
</script>
