<template>
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md p-8 bg-neutral-700 my-auto rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center text-neutral-100 mb-6">
                {{ props.stamp ? 'Update Stamp' : 'Create Stamp' }}
            </h2>

            <VeeForm
                v-slot="{ handleSubmit }"
                :validation-schema="validationSchema"
                :initial-values="initialValues"
                as="div"
            >
                <form @submit="handleSubmit($event, onSubmit)">
                    <div class="mb-4">
                        <FormFile name="image" label="Image" type="file" @change="onFileChange" />
                    </div>

                    <div class="mb-4">
                        <FormInput name="name" label="Name" />
                    </div>

                    <div class="mb-4">
                        <FormCheckbox name="used" label="Used" type="checkbox" />
                    </div>

                    <div class="mb-4">
                        <FormCheckbox name="damaged" label="Damaged" type="checkbox" />
                    </div>

                    <button
                        type="submit"
                        class="cursor-pointer w-full p-3 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-lg transition"
                    >
                        {{ props.stamp ? 'Update Stamp' : 'Create Stamp' }}
                    </button>
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

const acceptedImageTypes = ['image/jpeg', 'image/png'];

const props = defineProps<{
    stamp?: Stamp;
}>();

const initialValues = {
    name: props.stamp?.name ?? '',
    used: props.stamp?.used ?? false,
    damaged: props.stamp?.damaged ?? false,
};

const validationSchema = yup.object({
    image: yup
        .mixed()
        .required('Image is required')
        .test('file', 'Invalid file type, accepted types: jpeg, png', (value) => {
            if (!value || !(value instanceof File)) {
                return false;
            }
            return acceptedImageTypes.includes(value.type);
        }),
    name: yup.string().required('Name is required').max(255, 'Name must be less than 255 characters'),
    used: yup.boolean(),
    damaged: yup.boolean(),
});

const stampStore = useStampStore();
const router = useRouter();
const route = useRoute();

const selectedFile = ref<string>('');

const onSubmit = (values: GenericObject, actions: SubmissionContext) => {
    const collectionId = Number(route.params.id);

    if (props.stamp) {
        // stampStore
        //     .update(props.collection.id, values.name)
        //     .then(() => {
        //         router.push(`/collections/${props.collection!.id}`);
        //     })
        //     .catch((error) => {
        //         if (error.response.status === 422) {
        //             actions.setErrors({
        //                 name: Object.values(error.response.data.errors.name || []),
        //                 access: Object.values(error.response.data.errors.access || []),
        //             });
        //         } else {
        //             actions.setErrors({
        //                 access: error.response.data.error,
        //             });
        //         }
        //     });
    } else {
        stampStore
            .create(collectionId, values.name, values.used, values.damaged, selectedFile.value)
            .then(() => {
                router.push(`/collections/${collectionId}`);
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
