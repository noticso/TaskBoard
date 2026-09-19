<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { show as showProject } from '@/routes/pages/projects';
import {
    complete,
    create,
    destroy,
    edit,
    index,
} from '@/routes/pages/projects/tasks';
import type { Priority, Project, Status, Task } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    project: Project;
    tasks: Task[];
    filters: {
        status: Status | null;
        priority: Priority | null;
    };
}>();

const status = ref(props.filters.status ?? '');
const priority = ref(props.filters.priority ?? '');

function applyFilters() {
    router.get(
        index(props.project.id).url,
        {
            status: status.value || undefined,
            priority: priority.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

function completeTask(task: Task) {
    router.patch(complete({ project: props.project.id, task: task.id }).url);
}

function deleteTask(task: Task) {
    if (confirm(`Eliminare la task "${task.title}"?`)) {
        router.delete(
            destroy({ project: props.project.id, task: task.id }).url,
        );
    }
}
</script>

<template>
    <Head :title="`Task - ${project.name}`" />

    <AppLayout>
        <Link
            :href="showProject(project.id).url"
            class="text-sm text-gray-500 hover:underline"
        >
            &larr; Torna al progetto
        </Link>

        <div class="mt-4 mb-6 flex items-center justify-between">
            <h1 class="text-xl font-semibold">Task di {{ project.name }}</h1>
            <Link
                :href="create(project.id).url"
                class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white"
            >
                Nuova task
            </Link>
        </div>

        <div class="mb-4 flex gap-3">
            <select
                v-model="status"
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm"
                @change="applyFilters"
            >
                <option value="">Tutti gli status</option>
                <option value="todo">Todo</option>
                <option value="in_progress">In progress</option>
                <option value="completed">Completed</option>
            </select>

            <select
                v-model="priority"
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm"
                @change="applyFilters"
            >
                <option value="">Tutte le priorità</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>

        <p v-if="tasks.length === 0" class="text-sm text-gray-500">
            Nessuna task trovata.
        </p>

        <ul
            v-else
            class="divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white"
        >
            <li
                v-for="task in tasks"
                :key="task.id"
                class="flex items-center justify-between px-4 py-3"
            >
                <div>
                    <p
                        class="font-medium"
                        :class="{
                            'text-gray-400 line-through':
                                task.status === 'completed',
                        }"
                    >
                        {{ task.title }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ task.status }} · {{ task.priority }}
                    </p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <button
                        v-if="task.status !== 'completed'"
                        type="button"
                        class="text-green-700 hover:underline"
                        @click="completeTask(task)"
                    >
                        Completa
                    </button>
                    <Link
                        :href="edit({ project: project.id, task: task.id }).url"
                        class="text-gray-600 hover:underline"
                    >
                        Modifica
                    </Link>
                    <button
                        type="button"
                        class="text-red-600 hover:underline"
                        @click="deleteTask(task)"
                    >
                        Elimina
                    </button>
                </div>
            </li>
        </ul>
    </AppLayout>
</template>
