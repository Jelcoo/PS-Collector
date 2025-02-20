<template>
    <ContainerComponent :loading="loading || !user">
        <div class="flex items-center justify-center">
            <div class="w-full max-w-md p-8 bg-neutral-700 my-auto rounded-2xl shadow-lg">
                <h2 class="text-2xl font-semibold text-center text-neutral-100 mb-6">Edit Account</h2>

                <MessageComponent :message="message" />
                <VeeForm
                    v-slot="{ handleSubmit }"
                    :validation-schema="validationSchema"
                    :initial-values="initialValues"
                    as="div"
                >
                    <form @submit="handleSubmit($event, onSubmit)">
                        <div class="mb-4">
                            <FormInput name="username" label="Username" disabled />
                        </div>

                        <div class="mb-4">
                            <FormInput name="first_name" label="First name" />
                        </div>

                        <div class="mb-4">
                            <FormInput name="last_name" label="Last name" />
                        </div>

                        <div class="mb-4">
                            <FormInput name="email" label="Email" type="email" />
                        </div>

                        <button
                            type="submit"
                            class="cursor-pointer w-full p-3 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-lg transition"
                        >
                            Update
                        </button>
                    </form>
                </VeeForm>
            </div>
        </div>
    </ContainerComponent>
</template>

<script setup lang="ts">
import { useUserStore } from '@/stores/user';
import { Form as VeeForm, type GenericObject, type SubmissionContext } from 'vee-validate';
import * as yup from 'yup';
import FormInput from '@/components/forms/FormInput.vue';
import { onBeforeMount, ref } from 'vue';
import ContainerComponent from '@/components/ContainerComponent.vue';
import type { User } from '@/stores/types';
import MessageComponent from '@/components/MessageComponent.vue';

const userStore = useUserStore();

const loading = ref(true);
const user = ref<User>();
const initialValues = ref();
const message = ref('');

const validationSchema = yup.object({
    username: yup.string().required('Username is required'),
    first_name: yup.string().required('First name is required'),
    last_name: yup.string().required('Last name is required'),
    email: yup.string().email('Invalid email').required('Email is required'),
});

onBeforeMount(() => {
    userStore.me().then((response) => {
        user.value = response.data.user;
        loading.value = false;
        initialValues.value = {
            username: user.value.username,
            first_name: user.value.first_name,
            last_name: user.value.last_name,
            email: user.value.email,
        };
    });
});

const onSubmit = (values: GenericObject, actions: SubmissionContext) => {
    userStore
        .update(values.first_name, values.last_name, values.email)
        .then((res) => {
            message.value = res.data.message;
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors({
                    username: Object.values(error.response.data.errors.username || []),
                    first_name: Object.values(error.response.data.errors.first_name || []),
                    last_name: Object.values(error.response.data.errors.last_name || []),
                    email: Object.values(error.response.data.errors.email || []),
                });
            } else {
                actions.setErrors({
                    email: error.response.data.error,
                });
            }
        });
};
</script>
