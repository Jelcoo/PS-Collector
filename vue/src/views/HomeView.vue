<template>
    <ContainerComponent>
        <div class="flex flex-col sm:flex-row items-center justify-between mb-4">
            <h1 class="text-3xl font-bold">{{ $t('common.collections') }}</h1>
            <div class="flex flex-col sm:flex-row gap-4">
                <RouterLink
                    v-if="userStore.isAuthenticated"
                    class="px-4 py-2 text-xl bg-emerald-600 rounded hover:bg-emerald-500"
                    to="/collections/create"
                >
                    {{ $t('common.create_new') }}
                </RouterLink>
            </div>
        </div>

        <PageIndicator :pagination="collectionStore.pages" @page-select="handlePageSelect">
            <div class="flex flex-wrap gap-4 justify-center">
                <CollectionCard
                    v-for="collection in collectionStore.data"
                    :key="collection.id"
                    :collection
                    :total-stamps="2"
                />
            </div>
            <div class="justify-center" v-if="collectionStore.data.length === 0">
                <p class="text-center text-neutral-400">{{ $t('collections.no_collections') }}</p>
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
