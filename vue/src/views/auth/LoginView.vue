<template>
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md p-8 bg-neutral-700 my-auto rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center text-neutral-100 mb-6">Login</h2>

            <VeeForm v-slot="{ handleSubmit }" :validation-schema="validationSchema" as="div">
                <form @submit="handleSubmit($event, onSubmit)">
                    <div class="mb-4">
                        <FormInput name="email" label="Email" type="email" />
                    </div>

                    <div class="mb-4">
                        <FormInput name="password" label="Password" type="password" />
                    </div>

                    <div class="mb-4">
                        <VueTurnstile ref="turnstile" :site-key="turnstileToken" v-model="turnstileRef" />
                    </div>

                    <StyledSubmitButton>Login</StyledSubmitButton>
                </form>
            </VeeForm>

            <div class="text-center mt-4">
                <RouterLink to="/auth/register" class="text-sky-500 hover:text-sky-600 no-underline">
                    Don't have an account? Register
                </RouterLink>
            </div>
            <div class="text-center mt-4">
                <RouterLink to="/auth/password" class="text-sky-500 hover:text-sky-600 no-underline">
                    Forgot password?
                </RouterLink>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '@/stores/user';
import { Form as VeeForm, type GenericObject, type SubmissionContext } from 'vee-validate';
import { useRouter } from 'vue-router';
import * as yup from 'yup';
import FormInput from '@/components/forms/FormInput.vue';
import { ref, useTemplateRef } from 'vue';
import VueTurnstile from 'vue-turnstile';
import StyledSubmitButton from '@/components/StyledSubmitButton.vue';

const validationSchema = yup.object({
    email: yup.string().email('Invalid email').required('Email is required'),
    password: yup.string().min(0, 'Password must be at least 6 characters').required('Password is required'),
});

const userStore = useUserStore();
const router = useRouter();

const turnstileToken = import.meta.env.VITE_TURNSTILE_KEY;
const turnstileRef = ref('');
const turnstile = useTemplateRef('turnstile');

const onSubmit = (values: GenericObject, actions: SubmissionContext) => {
    userStore
        .login(values.email, values.password, turnstileRef.value)
        .then(() => {
            router.back();
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors({
                    email: Object.values(error.response.data.errors.email || []),
                    password: Object.values(error.response.data.errors.password || []),
                });
            } else {
                actions.setErrors({
                    password: error.response.data.error,
                });
            }
            turnstile.value?.reset();
        });
};
</script>
