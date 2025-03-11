<template>
    <ForbiddenView v-if="status === 403" />
    <NotFoundView v-else-if="status === 404" />
    <ContainerComponent v-else :loading="loading || !collection">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold mb-4 truncate">{{ collection!.name }}</h1>
            <div class="flex gap-4" v-if="collection!.userAccess === 'owner'">
                <StyledButton variant="text" @click="router.back()">
                    <FontAwesomeIcon :icon="faArrowLeft" class="mr-2" /> {{ $t('common.back') }}
                </StyledButton>
                <StyledButton
                    v-if="collection!.access === 'shared'"
                    @click="router.push(`/collections/${collection!.id}/members`)"
                >
                    <FontAwesomeIcon :icon="faUser" class="mr-2" /> {{ $t('common.members') }}
                </StyledButton>
                <StyledButton @click="router.push(`/collections/${collection!.id}/edit`)">
                    <FontAwesomeIcon :icon="faPencil" class="mr-2" /> {{ $t('common.edit') }}
                </StyledButton>
                <StyledButton variant="danger" @click="deleteCollection">
                    <FontAwesomeIcon :icon="faTrash" class="mr-2" /> {{ $t('common.delete') }}
                </StyledButton>
            </div>
        </div>
        <div class="mb-4">
            <input
                type="text"
                class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600"
                :placeholder="$t('search.placeholder_by_name')"
                v-model="search"
                @input="onSearch"
            />
        </div>
        <div class="flex flex-wrap gap-4 justify-center mb-4">
            <AddStampCard v-if="collection!.userAccess === 'owner'" :collection="collection!" />
            <StampCard v-for="stamp in collection!.stamps" :key="stamp.id" :collection="collection!" :stamp="stamp" />
        </div>
        <div class="justify-center" v-if="collection!.stamps!.length === 0">
            <p class="text-center text-neutral-400">{{ $t('stamps.no_stamps') }}</p>
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
import { faArrowLeft, faPencil, faTrash, faUser } from '@fortawesome/free-solid-svg-icons';
import ContainerComponent from '@/components/ContainerComponent.vue';
import StyledButton from '@/components/StyledButton.vue';
import type { Collection } from '@/stores/types';
import AddStampCard from '@/components/AddStampCard.vue';
import StampCard from '@/components/StampCard.vue';
import { useDebounceFn } from '@vueuse/core';

const collectionStore = useCollectionStore();
const route = useRoute();
const router = useRouter();

const loading = ref(true);
const collection = ref<Collection>();
const status = ref(0);
const search = ref('');

const onSearch = useDebounceFn(() => {
    collectionStore.search(collection.value!.id, search.value).then((res) => {
        collection.value!.stamps = res.data.results ?? [];
    });
}, 500);

onBeforeMount(() => {
    const collectionId = Number(route.params.id);
    collectionStore
        .getCollection(collectionId, ['author', 'access'])
        .then((res) => {
            collection.value = res.data;
            if (!collection.value.stamps) {
                collection.value.stamps = [];
            }
            onSearch();
            loading.value = false;
        })
        .catch((error) => {
            status.value = error.status;
        });
});

const deleteCollection = () => {
    collectionStore.delete(collection.value!.id).then(() => {
        router.push('/');
    });
};
</script>
