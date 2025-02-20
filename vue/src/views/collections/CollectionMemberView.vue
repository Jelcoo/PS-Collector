<template>
    <ForbiddenView v-if="status === 403" />
    <NotFoundView v-else-if="status === 404" />
    <ContainerComponent v-else :loading="loading || !collection">
        <ModalComponent :visible="addVisible" title="Add member" @close="addVisible = false">
            <VeeForm v-slot="{ handleSubmit }" as="div">
                <form @submit="handleSubmit($event, onSubmit)" ref="formRef">
                    <div class="mb-4">
                        <FormInput name="username" label="Username" placeholder="Enter their username" />
                    </div>
                </form>
            </VeeForm>
            <template v-slot:footer>
                <StyledButton variant="text" @click="addVisible = false">Cancel</StyledButton>
                <StyledButton @click="submitForm">Add</StyledButton>
            </template>
        </ModalComponent>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold mb-4 truncate">{{ collection!.name }}</h1>
            <div class="flex gap-4" v-if="collection!.userAccess === 'owner'">
                <StyledButton @click="addVisible = true">
                    <FontAwesomeIcon :icon="faUserPlus" class="mr-2" /> Add member
                </StyledButton>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <h2 class="text-2xl font-bold mb-4">Members</h2>
            <table class="min-w-full divide-y divide-neutral-400">
                <thead class="bg-neutral-600">
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-50 uppercase tracking-wider"
                        >
                            Username
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-50 uppercase tracking-wider"
                        >
                            Role
                        </th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody class="bg-neutral-500 divide-y divide-neutral-200">
                    <CollectionMemberRow v-for="member in collection!.members" :key="member.id" :member="member" />
                </tbody>
            </table>
        </div>
    </ContainerComponent>
</template>

<script setup lang="ts">
import { useCollectionStore } from '@/stores/collection';
import { onBeforeMount, ref } from 'vue';
import { useRoute } from 'vue-router';
import type { Collection } from '@/stores/types';
import ContainerComponent from '@/components/ContainerComponent.vue';
import ForbiddenView from '@/views/status/ForbiddenView.vue';
import NotFoundView from '@/views/status/NotFoundView.vue';
import CollectionMemberRow from '@/components/CollectionMemberRow.vue';
import StyledButton from '@/components/StyledButton.vue';
import { faUserPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import ModalComponent from '@/components/ModalComponent.vue';
import { Form as VeeForm, type GenericObject, type SubmissionContext } from 'vee-validate';
import FormInput from '@/components/forms/FormInput.vue';

const collectionStore = useCollectionStore();
const route = useRoute();

const loading = ref(true);
const collection = ref<Collection>();
const status = ref(0);
const addVisible = ref(false);
const formRef = ref<HTMLFormElement | null>(null);

onBeforeMount(() => {
    const collectionId = Number(route.params.id);
    collectionStore
        .getCollection(collectionId, ['access', 'members'])
        .then((res) => {
            collection.value = res.data;
            loading.value = false;
        })
        .catch((error) => {
            status.value = error.status;
        });
});

const onSubmit = (values: GenericObject, actions: SubmissionContext) => {
    collectionStore
        .addMember(collection.value!.id, values.username)
        .then(() => {
            addVisible.value = false;
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors({
                    username: error.response.data.errors.username,
                });
            } else {
                actions.setErrors({
                    username: error.response.data.error,
                });
            }
        });
};

const submitForm = () => {
    formRef.value?.requestSubmit();
};
</script>
