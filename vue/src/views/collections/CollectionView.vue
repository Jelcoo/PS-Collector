<template>
    <div v-if="collectionStore.currentCollection.loading">Loading...</div>
    <ForbiddenView v-else-if="status === 403" />
    <NotFoundView v-else-if="status === 404" />
    <ContainerComponent v-else>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold mb-4 truncate">{{ collectionStore.currentCollection.data.name }}</h1>
            <div class="flex gap-4" v-if="collectionStore.currentCollection.data.userAccess === 'owner'">
                <button
                    class="cursor-pointer px-4 py-2 text-xl bg-red-500 rounded hover:bg-red-400"
                    @click="collectionStore.delete(collectionStore.currentCollection.data.id)"
                >
                    <FontAwesomeIcon :icon="faTrash" /> Delete
                </button>
            </div>
        </div>
    </ContainerComponent>
</template>

<script setup lang="ts">
import { useCollectionStore } from '@/stores/collection';
import { onBeforeMount, ref } from 'vue';
import { useRoute } from 'vue-router';
import NotFoundView from '@/views/status/NotFoundView.vue';
import ForbiddenView from '@/views/status/ForbiddenView.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faTrash } from '@fortawesome/free-solid-svg-icons';
import ContainerComponent from '@/components/ContainerComponent.vue';

const collectionStore = useCollectionStore();
const route = useRoute();
const status = ref(0);

onBeforeMount(() => {
    const collectionId = Number(route.params.id);
    collectionStore.getCollection(collectionId, ['stamps', 'author', 'access']).catch((error) => {
        status.value = error.status;
    });
});
</script>
