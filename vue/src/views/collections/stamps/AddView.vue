<template>
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md p-8 bg-neutral-700 my-auto rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center text-neutral-100 mb-6">
                {{ props.stamp ? $t('stamps.edit_stamp') : $t('stamps.create_stamp') }}
            </h2>

            <VeeForm
                v-slot="{ handleSubmit }"
                :validation-schema="validationSchema"
                :initial-values="initialValues"
                as="div"
            >
                <form @submit="handleSubmit($event, onSubmit)">
                    <div class="mb-4">
                        <img :src="selectedFile" v-if="selectedFile" class="w-32 h-32 object-cover" />
                        <FormFile
                            name="image"
                            :label="$t('common.image')"
                            type="file"
                            :accept="acceptedImageTypes.join(', ')"
                            @change="onFileChange"
                        />
                    </div>

                    <div class="mb-4">
                        <FormInput name="name" :label="$t('common.name')" />
                    </div>

                    <div class="mb-4">
                        <FormCheckbox name="used" :label="$t('common.used')" type="checkbox" />
                    </div>

                    <div class="mb-4">
                        <FormCheckbox name="damaged" :label="$t('common.damaged')" type="checkbox" />
                    </div>

                    <StyledSubmitButton>
                        {{ props.stamp ? $t('stamps.edit_stamp') : $t('stamps.create_stamp') }}
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
import { useRoute, useRouter } from 'vue-router';
import * as yup from 'yup';
import FormInput from '@/components/forms/FormInput.vue';
import type { Stamp } from '@/stores/types';
import { useStampStore } from '@/stores/stamp';
import FormCheckbox from '@/components/forms/FormCheckbox.vue';
import FormFile from '@/components/forms/FormFile.vue';
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faArrowLeft } from '@fortawesome/free-solid-svg-icons';
import StyledButton from '@/components/StyledButton.vue';
import StyledSubmitButton from '@/components/StyledSubmitButton.vue';

const acceptedImageTypes = ['image/jpeg', 'image/png'];

const props = defineProps<{
    stamp?: Stamp;
}>();

const initialValues = {
    name: props.stamp?.name ?? '',
    used: props.stamp?.used ?? false,
    damaged: props.stamp?.damaged ?? false,
    image: props.stamp?.headerUrl?.split('/').pop() ?? '',
};

const validationSchema = yup.object({
    image: yup
        .mixed()
        .test('required', 'Please select an image', (value) => {
            if (props.stamp?.headerUrl) {
                return true;
            }
            return !!value;
        })
        .test('file', 'Invalid file type, accepted types: jpeg, png', (value) => {
            if (!value || !(value instanceof File)) {
                if (props.stamp?.headerUrl) {
                    return true;
                }
                return false;
            }
            return acceptedImageTypes.includes(value.type);
        })
        .test('size', 'File size is too large', (value) => {
            if (!value || !(value instanceof File)) {
                if (props.stamp?.headerUrl) {
                    return true;
                }
                return false;
            }
            return value.size <= 1024 * 1024 * 5;
        }),
    name: yup.string().required('Name is required').max(255, 'Name must be less than 255 characters'),
    used: yup.boolean(),
    damaged: yup.boolean(),
});

const stampStore = useStampStore();
const router = useRouter();
const route = useRoute();

const selectedFile = ref<string>(props.stamp?.headerUrl ?? '');

const onSubmit = (values: GenericObject, actions: SubmissionContext) => {
    const collectionId = Number(route.params.id);

    if (props.stamp) {
        stampStore
            .update(
                props.stamp.id,
                values.name,
                values.used,
                values.damaged,
                selectedFile.value === props.stamp.headerUrl ? null : selectedFile.value,
            )
            .then(() => {
                router.replace(`/collections/${props.stamp!.collection_id}/stamps/${props.stamp!.id}`);
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
        stampStore
            .create(collectionId, values.name, values.used, values.damaged, selectedFile.value)
            .then((res) => {
                router.replace(`/collections/${collectionId}/stamps/${res.data.stamp.id}`);
            })
            .catch((error) => {
                if (error.response.status === 422) {
                    actions.setErrors({
                        name: Object.values(error.response.data.errors.name || []),
                        used: Object.values(error.response.data.errors.used || []),
                        damaged: Object.values(error.response.data.errors.damaged || []),
                    });
                } else {
                    actions.setErrors({
                        damaged: error.response.data.error,
                    });
                }
            });
    }
};

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files;
    if (files && files.length > 0) {
        const file = files[0];
        const reader = new FileReader();
        reader.onload = () => {
            selectedFile.value = reader.result as string;
        };
        reader.readAsDataURL(file);
    }
};
</script>
