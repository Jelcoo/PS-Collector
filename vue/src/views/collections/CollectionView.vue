<template>
    <div v-if="collectionStore.currentCollection.loading">Loading...</div>
    <ForbiddenView v-else-if="status === 403" />
    <NotFoundView v-else-if="status === 404" />
    <ContainerComponent v-else>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold mb-4 truncate">{{ collectionStore.currentCollection.data.name }}</h1>
            <div class="flex gap-4" v-if="collectionStore.currentCollection.data.userAccess === 'owner'">
                <StyledButton @click="router.push(`/collections/${collectionStore.currentCollection.data.id}/edit`)">
                    <FontAwesomeIcon :icon="faPencil" class="mr-2" /> Edit
                </StyledButton>
                <StyledButton variant="danger" @click="deleteCollection">
                    <FontAwesomeIcon :icon="faTrash" class="mr-2" /> Delete
                </StyledButton>
            </div>
        </div>
    </ContainerComponent>
</template>

<script setup lang="ts">
import { useCollectionStore } from '@/stores/collection';
import { onBeforeMount, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import NotFoundView from '@/views/status/NotFoundView.vue';
import ForbiddenView from '@/views/status/ForbiddenView.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPencil, faTrash } from '@fortawesome/free-solid-svg-icons';
import ContainerComponent from '@/components/ContainerComponent.vue';
import StyledButton from '@/components/StyledButton.vue';

const collectionStore = useCollectionStore();
const route = useRoute();
const router = useRouter();
const status = ref(0);

onBeforeMount(() => {
    const collectionId = Number(route.params.id);
    collectionStore.getCollection(collectionId, ['stamps', 'author', 'access']).catch((error) => {
        status.value = error.status;
    });
});

const deleteCollection = () => {
    collectionStore.delete(collectionStore.currentCollection.data.id).then(() => {
        router.push('/collections');
    });
};
</script>
