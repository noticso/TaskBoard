<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { show, update } from '@/routes/pages/projects';
import type { Project } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    project: Project;
}>();

const form = useForm({
    name: props.project.name,
    description: props.project.description ?? '',
});

function submit() {
    form.put(update(props.project.id).url);
}
</script>

<template>
    <Head title="Modifica progetto" />

    <AppLayout>
        <h1 class="mb-6 text-xl font-semibold">Modifica progetto</h1>

        <form class="max-w-md space-y-4" @submit.prevent="submit">
            <div>
                <label
                    for="name"
                    class="block text-sm font-medium text-gray-700"
                    >Nome</label
                >
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label
                    for="description"
                    class="block text-sm font-medium text-gray-700"
                    >Descrizione</label
                >
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                ></textarea>
                <p
                    v-if="form.errors.description"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.description }}
                </p>
            </div>

            <div class="flex gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
                >
                    Salva
                </button>
                <Link
                    :href="show(props.project.id).url"
                    class="px-4 py-2 text-sm text-gray-600 hover:underline"
                >
                    Annulla
                </Link>
            </div>
        </form>
    </AppLayout>
</template>
