<template>
    <ContainerComponent :loading="loading || !user">
        <div class="flex flex-col md:flex-row items-center justify-center gap-4">
            <div class="w-full max-w-md p-8 bg-neutral-700 my-auto rounded-2xl shadow-lg">
                <h2 class="text-2xl font-semibold text-center text-neutral-100 mb-6">{{ $t('account.edit') }}</h2>

                <MessageComponent :message="accountMessage" />
                <VeeForm
                    v-slot="{ handleSubmit }"
                    :validation-schema="accountValidationSchema"
                    :initial-values="initialValues"
                    as="div"
                >
                    <form @submit="handleSubmit($event, onSubmitAccount)">
                        <div class="mb-4">
                            <FormInput name="first_name" :label="$t('common.first_name')" />
                        </div>

                        <div class="mb-4">
                            <FormInput name="last_name" :label="$t('common.last_name')" />
                        </div>

                        <div class="mb-4">
                            <FormInput name="email" :label="$t('common.email')" type="email" />
                        </div>

                        <StyledSubmitButton>{{ $t('account.edit') }}</StyledSubmitButton>
                    </form>
                </VeeForm>
            </div>
            <div class="w-full max-w-md p-8 bg-neutral-700 my-auto rounded-2xl shadow-lg">
                <h2 class="text-2xl font-semibold text-center text-neutral-100 mb-6">{{ $t('account.password') }}</h2>

                <MessageComponent :message="passwordMessage" />
                <VeeForm v-slot="{ handleSubmit }" :validation-schema="passwordValidationSchema" as="div">
                    <form @submit="handleSubmit($event, onSubmitPassword)">
                        <div class="mb-4">
                            <FormInput name="current_password" :label="$t('common.current_password')" type="password" />
                        </div>

                        <div class="mb-4">
                            <FormInput name="password" :label="$t('common.password')" type="password" />
                        </div>

                        <div class="mb-4">
                            <FormInput
                                name="password_confirmation"
                                :label="$t('common.confirm_password')"
                                type="password"
                            />
                        </div>

                        <StyledSubmitButton>{{ $t('account.password') }}</StyledSubmitButton>
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
import StyledSubmitButton from '@/components/StyledSubmitButton.vue';

const userStore = useUserStore();

const loading = ref(true);
const user = ref<User>();
const initialValues = ref();
const accountMessage = ref('');
const passwordMessage = ref('');

const accountValidationSchema = yup.object({
    first_name: yup.string().required('First name is required'),
    last_name: yup.string().required('Last name is required'),
    email: yup.string().email('Invalid email').required('Email is required'),
});

const passwordValidationSchema = yup.object({
    current_password: yup.string().required('Current password is required'),
    password: yup.string().min(6, 'Password must be at least 6 characters').required('Password is required'),
    password_confirmation: yup
        .string()
        .oneOf([yup.ref('password'), undefined], 'Passwords must match')
        .required('Password confirmation is required'),
});

onBeforeMount(() => {
    userStore.me().then((response) => {
        user.value = response.data.user;
        loading.value = false;
        initialValues.value = {
            first_name: user.value.first_name,
            last_name: user.value.last_name,
            email: user.value.email,
        };
    });
});

const onSubmitAccount = (values: GenericObject, actions: SubmissionContext) => {
    userStore
        .update(values.first_name, values.last_name, values.email)
        .then((res) => {
            accountMessage.value = res.data.message;
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors({
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

const onSubmitPassword = (values: GenericObject, actions: SubmissionContext) => {
    userStore
        .updatePassword(values.current_password, values.password)
        .then((res) => {
            passwordMessage.value = res.data.message;
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors({
                    current_password: Object.values(error.response.data.errors.current_password || []),
                    password: Object.values(error.response.data.errors.password || []),
                });
            } else {
                actions.setErrors({
                    password: error.response.data.error,
                });
            }
        });
};
</script>
