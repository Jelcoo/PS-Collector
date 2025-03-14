<template>
    <ForbiddenView v-if="status === 403" />
    <NotFoundView v-else-if="status === 404" />
    <ContainerComponent v-else :loading="loading || !stamp">
        <ConfirmationModal :visible="deleteVisible" @confirm="deleteStamp" @close="deleteVisible = false" />
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold mb-4 truncate">{{ stamp?.name }}</h1>
            <div class="flex gap-4" v-if="stamp!.collection!.userAccess === 'owner'">
                <StyledButton variant="text" @click="router.back()">
                    <FontAwesomeIcon :icon="faArrowLeft" class="mr-2" /> {{ $t('common.back') }}
                </StyledButton>
                <StyledButton @click="router.push(`/collections/${stamp!.collection_id}/stamps/${stamp!.id}/edit`)">
                    <FontAwesomeIcon :icon="faPencil" class="mr-2" /> {{ $t('common.edit') }}
                </StyledButton>
                <StyledButton variant="danger" @click="clickDelete">
                    <FontAwesomeIcon :icon="faTrash" class="mr-2" /> {{ $t('common.delete') }}
                </StyledButton>
            </div>
        </div>
        <div class="flex">
            <img v-if="stamp!.headerUrl" :src="stamp!.headerUrl" class="w-1/3" />
            <div class="w-2/3 p-4">
                <div>
                    <FontAwesomeIcon
                        :icon="stamp!.used ? faBoxOpen : faBox"
                        class="w-6 h-6 mr-2"
                        :class="{ 'text-red-500': stamp!.used, 'text-green-500': !stamp!.used }"
                    />
                    <span class="font-semibold text-2xl">{{
                        stamp!.used ? $t('common.used') : $t('common.not_used')
                    }}</span>
                </div>
                <div>
                    <FontAwesomeIcon
                        :icon="stamp!.damaged ? faHeartBroken : faHeart"
                        class="w-6 h-6 mr-2"
                        :class="{ 'text-red-500': stamp!.damaged, 'text-green-500': !stamp!.damaged }"
                    />
                    <span class="font-semibold text-2xl">{{
                        stamp!.damaged ? $t('common.damaged') : $t('common.not_damaged')
                    }}</span>
                </div>
            </div>
        </div>
    </ContainerComponent>
</template>

<script setup lang="ts">
import { onBeforeMount, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import type { Stamp } from '@/stores/types';
import ContainerComponent from '@/components/ContainerComponent.vue';
import ForbiddenView from '@/views/status/ForbiddenView.vue';
import NotFoundView from '@/views/status/NotFoundView.vue';
import { useStampStore } from '@/stores/stamp';
import {
    faArrowLeft,
    faBox,
    faBoxOpen,
    faHeart,
    faHeartBroken,
    faPencil,
    faTrash,
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import StyledButton from '@/components/StyledButton.vue';
import ConfirmationModal from '@/components/ConfirmationModal.vue';

const stampStore = useStampStore();
const route = useRoute();
const router = useRouter();

const loading = ref(true);
const stamp = ref<Stamp>();
const status = ref(0);
const deleteVisible = ref(false);

onBeforeMount(() => {
    const stampId = Number(route.params.stampId);
    stampStore
        .getStamp(stampId, ['collection', 'header'])
        .then((res) => {
            stamp.value = res.data;
            loading.value = false;
        })
        .catch((error) => {
            status.value = error.status;
        });
});

const clickDelete = () => {
    deleteVisible.value = true;
};
const deleteStamp = () => {
    stampStore.delete(stamp.value!.id).then(() => {
        router.push(`/collections/${stamp.value!.collection_id}`);
    });
};
</script>
