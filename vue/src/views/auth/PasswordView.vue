<template>
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md p-8 bg-neutral-700 my-auto rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center text-neutral-100 mb-6">Reset Password</h2>

            <MessageComponent :message="message" />
            <VeeForm
                v-slot="{ handleSubmit }"
                :validation-schema="resetToken ? passwordValidationScheme : emailValidationScheme"
                as="div"
            >
                <form @submit="handleSubmit($event, onSubmit)">
                    <div class="mb-4" v-if="!resetToken">
                        <FormInput name="email" label="Email" type="email" />
                    </div>

                    <div class="mb-4" v-if="!!resetToken">
                        <FormInput name="password" label="Password" type="password" />
                    </div>

                    <div class="mb-4" v-if="!!resetToken">
                        <FormInput name="password_confirmation" label="Confirm Password" type="password" />
                    </div>

                    <div class="mb-4">
                        <VueTurnstile ref="turnstile" :site-key="turnstileToken" v-model="turnstileRef" />
                    </div>

                    <button
                        :disabled="resetClicked"
                        type="submit"
                        class="cursor-pointer w-full p-3 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-lg transition"
                    >
                        {{ resetToken ? 'Reset Password' : 'Send Reset Link' }}
                    </button>
                </form>
            </VeeForm>

            <div class="text-center mt-4">
                <RouterLink to="/auth/login" class="text-sky-500 hover:text-sky-600 no-underline"> Login </RouterLink>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '@/stores/user';
import { Form as VeeForm, type GenericObject, type SubmissionContext } from 'vee-validate';
import { useRoute } from 'vue-router';
import * as yup from 'yup';
import FormInput from '@/components/forms/FormInput.vue';
import { ref, useTemplateRef } from 'vue';
import VueTurnstile from 'vue-turnstile';
import MessageComponent from '@/components/MessageComponent.vue';

const userStore = useUserStore();
const route = useRoute();

const resetClicked = ref(false);
const resetToken = ref(route.query.token?.toString() ?? undefined);
const message = ref('');

const emailValidationScheme = yup.object({
    email: yup.string().email('Invalid email').required('Email is required'),
});

const passwordValidationScheme = yup.object({
    password: yup.string().min(6, 'Password must be at least 6 characters').required('Password is required'),
    password_confirmation: yup
        .string()
        .oneOf([yup.ref('password'), undefined], 'Passwords must match')
        .required('Password confirmation is required'),
});

const turnstileToken = import.meta.env.VITE_TURNSTILE_KEY;
const turnstileRef = ref('');
const turnstile = useTemplateRef('turnstile');

const onSubmit = (values: GenericObject, actions: SubmissionContext) => {
    resetClicked.value = true;

    userStore
        .resetPassword(resetToken.value, values.email, values.password, turnstileRef.value)
        .then((res) => {
            message.value = res.data.message;
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors({
                    email: Object.values(error.response.data.errors.email || []),
                    password: Object.values(error.response.data.errors.password || []),
                });
            } else {
                actions.setErrors(
                    resetToken.value
                        ? { password_confirmation: error.response.data.error }
                        : { email: error.response.data.error },
                );
            }
            turnstile.value?.reset();
        });
};
</script>
