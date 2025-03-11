<template>
    <RouterLink
        :to="`/collections/${collection.id}/stamps/${stamp.id}`"
        class="bg-neutral-600 shadow-md rounded-lg p-4 w-64 space-y-3 border border-neutral-500 hover:bg-neutral-500"
    >
        <div>
            <img v-if="stamp.headerUrl" :src="stamp.headerUrl" class="h-32 rounded-lg object-contain" />
            <h2 class="text-xl font-bold truncate">{{ stamp.name }}</h2>
            <div>
                <FontAwesomeIcon
                    :icon="stamp.used ? faBoxOpen : faBox"
                    class="w-6 h-6 mr-2"
                    :class="{ 'text-red-500': stamp.used, 'text-green-500': !stamp.used }"
                />
                <span>{{ stamp.used ? $t('common.used') : $t('common.not_used') }}</span>
            </div>
            <div>
                <FontAwesomeIcon
                    :icon="stamp.damaged ? faHeartBroken : faHeart"
                    class="w-6 h-6 mr-2"
                    :class="{ 'text-red-500': stamp.damaged, 'text-green-500': !stamp.damaged }"
                />
                <span>{{ stamp.damaged ? $t('common.damaged') : $t('common.not_damaged') }}</span>
            </div>
        </div>
    </RouterLink>
</template>

<script setup lang="ts">
import { type Collection, type Stamp } from '@/stores/types';
import { faBox, faBoxOpen, faHeart, faHeartBroken } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

defineProps<{
    collection: Collection;
    stamp: Stamp;
}>();
</script>
