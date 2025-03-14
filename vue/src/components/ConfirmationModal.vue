<template>
    <ModalComponent :visible :title="$t('common.confirm')" @close="emit('close')">
        <p>{{ $t('common.confirm_delete') }}</p>
        <template v-slot:footer>
            <StyledButton variant="text" @click="emit('close')">{{ $t('common.cancel') }}</StyledButton>
            <StyledButton variant="danger" @click="emit('confirm')">{{ $t('common.confirm') }}</StyledButton>
        </template>
    </ModalComponent>
</template>

<script setup lang="ts">
import ModalComponent from './ModalComponent.vue';
import StyledButton from './StyledButton.vue';

const emit = defineEmits(['confirm', 'close']);

defineProps<{
    visible: boolean;
}>();
</script>
