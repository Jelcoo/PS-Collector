<template>
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md p-8 bg-neutral-700 my-auto rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center text-neutral-100 mb-6">
                {{ props.collection ? $t('collections.edit_collection') : $t('collections.create_collection') }}
            </h2>

            <VeeForm
                v-slot="{ handleSubmit }"
                :validation-schema="validationSchema"
                :initial-values="initialValues"
                as="div"
            >
                <form @submit="handleSubmit($event, onSubmit)">
                    <div class="mb-4">
                        <FormInput name="name" :label="$t('common.name')" />
                    </div>

                    <div class="mb-4">
                        <FormSelect
                            name="access"
                            :label="$t('common.access')"
                            :options="[
                                { value: 'public', label: $t('collections.levels.public') },
                                { value: 'private', label: $t('collections.levels.private') },
                                { value: 'shared', label: $t('collections.levels.shared') },
                            ]"
                        />
                    </div>

                    <StyledSubmitButton>
                        {{ props.collection ? $t('collections.edit_collection') : $t('collections.create_collection') }}
                    </StyledSubmitButton>
                    <StyledButton variant="text" @click="router.back()" class="mt-2 w-full">
                        <FontAwesomeIcon :icon="faArrowLeft" class="mr-2" /> {{ $t('common.back') }}
                    </StyledButton>
                </form>
            </VeeForm>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Form as VeeForm, type GenericObject, type SubmissionContext } from 'vee-validate';
import { useRouter } from 'vue-router';
import * as yup from 'yup';
import FormInput from '@/components/forms/FormInput.vue';
import FormSelect from '@/components/forms/FormSelect.vue';
import { useCollectionStore } from '@/stores/collection';
import type { Collection } from '@/stores/types';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faArrowLeft } from '@fortawesome/free-solid-svg-icons';
import StyledButton from '@/components/StyledButton.vue';
import StyledSubmitButton from '@/components/StyledSubmitButton.vue';

const props = defineProps<{
    collection?: Collection;
}>();

const initialValues = {
    name: props.collection?.name ?? '',
    access: props.collection?.access ?? '',
};

const validationSchema = yup.object({
    name: yup.string().required('Name is required').max(255, 'Name must be less than 255 characters'),
    access: yup.string().required('Access is required'),
});

const collectionStore = useCollectionStore();
const router = useRouter();

const onSubmit = (values: GenericObject, actions: SubmissionContext) => {
    if (props.collection) {
        collectionStore
            .update(props.collection.id, values.name, values.access)
            .then(() => {
                router.replace(`/collections/${props.collection!.id}`);
            })
            .catch((error) => {
                if (error.response.status === 422) {
                    actions.setErrors({
                        name: Object.values(error.response.data.errors.name || []),
                        access: Object.values(error.response.data.errors.access || []),
                    });
                } else {
                    actions.setErrors({
                        access: error.response.data.error,
                    });
                }
            });
    } else {
        collectionStore
            .create(values.name, values.access)
            .then((res) => {
                router.replace(`/collections/${res.data.collection.id}`);
            })
            .catch((error) => {
                if (error.response.status === 422) {
                    actions.setErrors({
                        name: Object.values(error.response.data.errors.name || []),
                        access: Object.values(error.response.data.errors.access || []),
                    });
                } else {
                    actions.setErrors({
                        access: error.response.data.error,
                    });
                }
            });
    }
};
</script>
