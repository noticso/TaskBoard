<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, show } from '@/routes/pages/projects';
import type { Project } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    projects: Project[];
}>();

function deleteProject(project: Project) {
    if (confirm(`Eliminare il progetto "${project.name}"?`)) {
        router.delete(destroy(project.id).url);
    }
}
</script>

<template>
    <Head title="Progetti" />

    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-semibold">I tuoi progetti</h1>
            <Link
                :href="create().url"
                class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white"
            >
                Nuovo progetto
            </Link>
        </div>

        <p v-if="projects.length === 0" class="text-sm text-gray-500">
            Non hai ancora nessun progetto.
        </p>

        <ul
            v-else
            class="divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white"
        >
            <li
                v-for="project in projects"
                :key="project.id"
                class="flex items-center justify-between px-4 py-3"
            >
                <Link
                    :href="show(project.id).url"
                    class="font-medium hover:underline"
                >
                    {{ project.name }}
                </Link>
                <button
                    type="button"
                    class="text-sm text-red-600 hover:underline"
                    @click="deleteProject(project)"
                >
                    Elimina
                </button>
            </li>
        </ul>
    </AppLayout>
</template>
