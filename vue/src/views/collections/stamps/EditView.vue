<template>
    <ForbiddenView v-if="status === 403" />
    <NotFoundView v-else-if="status === 404" />
    <ContainerComponent v-else :loading="loading || !stamp">
        <AddView :stamp />
    </ContainerComponent>
</template>

<script setup lang="ts">
import { onBeforeMount, ref } from 'vue';
import { useRoute } from 'vue-router';
import type { Stamp } from '@/stores/types';
import ContainerComponent from '@/components/ContainerComponent.vue';
import ForbiddenView from '@/views/status/ForbiddenView.vue';
import NotFoundView from '@/views/status/NotFoundView.vue';
import AddView from './AddView.vue';
import { useStampStore } from '@/stores/stamp';

const stampStore = useStampStore();
const route = useRoute();

const loading = ref(true);
const stamp = ref<Stamp>();
const status = ref(0);

onBeforeMount(() => {
    const stampId = Number(route.params.stampId);
    stampStore
        .getStamp(stampId, ['header'])
        .then((res) => {
            stamp.value = res.data;
            loading.value = false;
        })
        .catch((error) => {
            status.value = error.status;
        });
});
</script>
