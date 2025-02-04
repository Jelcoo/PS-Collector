<template>
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md p-8 bg-neutral-700 my-auto rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center text-neutral-100 mb-6">Register</h2>

            <VeeForm v-slot="{ handleSubmit }" :validation-schema="validationSchema" as="div">
                <form @submit="handleSubmit($event, onSubmit)">
                    <div class="mb-4">
                        <label class="block text-neutral-300 mb-2" for="username">Username</label>
                        <Field
                            name="username"
                            type="text"
                            class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none"
                            placeholder="Enter your username"
                        />
                        <ErrorMessage name="username" class="text-red-400 text-sm mt-1" />
                    </div>

                    <div class="mb-4">
                        <label class="block text-neutral-300 mb-2" for="first_name">First name</label>
                        <Field
                            name="first_name"
                            type="text"
                            class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none"
                            placeholder="Enter your first name"
                        />
                        <ErrorMessage name="first_name" class="text-red-400 text-sm mt-1" />
                    </div>

                    <div class="mb-4">
                        <label class="block text-neutral-300 mb-2" for="last_name">Last name</label>
                        <Field
                            name="last_name"
                            type="text"
                            class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none"
                            placeholder="Enter your last name"
                        />
                        <ErrorMessage name="last_name" class="text-red-400 text-sm mt-1" />
                    </div>

                    <div class="mb-4">
                        <label class="block text-neutral-300 mb-2" for="email">Email</label>
                        <Field
                            name="email"
                            type="email"
                            class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none"
                            placeholder="Enter your email"
                        />
                        <ErrorMessage name="email" class="text-red-400 text-sm mt-1" />
                    </div>

                    <div class="mb-4">
                        <label class="block text-neutral-300 mb-2" for="password">Password</label>
                        <Field
                            name="password"
                            type="password"
                            class="w-full p-3 bg-neutral-800 text-neutral-100 rounded-lg border border-neutral-600 focus:ring-2 focus:ring-sky-500 outline-none"
                            placeholder="Enter your password"
                        />
                        <ErrorMessage name="password" class="text-red-400 text-sm mt-1" />
                    </div>

                    <button
                        type="submit"
                        class="w-full p-3 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-lg transition"
                    >
                        Register
                    </button>
                </form>
            </VeeForm>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '@/stores/user';
import { Form as VeeForm, Field, ErrorMessage, type GenericObject, type SubmissionContext } from 'vee-validate';
import { useRouter } from 'vue-router';
import * as yup from 'yup';

const validationSchema = yup.object({
    username: yup.string().required('Username is required'),
    first_name: yup.string().required('First name is required'),
    last_name: yup.string().required('Last name is required'),
    email: yup.string().email('Invalid email').required('Email is required'),
    password: yup.string().min(6, 'Password must be at least 6 characters').required('Password is required'),
});

const userStore = useUserStore();
const router = useRouter();

const onSubmit = (values: GenericObject, actions: SubmissionContext) => {
    userStore
        .register(values.username, values.first_name, values.last_name, values.email, values.password)
        .then(() => {
            router.push('/account');
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors({
                    username: Object.values(error.response.data.errors.username || []),
                    first_name: Object.values(error.response.data.errors.first_name || []),
                    last_name: Object.values(error.response.data.errors.last_name || []),
                    email: Object.values(error.response.data.errors.email || []),
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
