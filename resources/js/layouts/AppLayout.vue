<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import { index as projectsIndex } from '@/routes/pages/projects';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();

function logout() {
    router.post(AuthenticatedSessionController.destroy().url);
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-900">
        <header class="border-b border-gray-200 bg-white">
            <div
                class="mx-auto flex max-w-3xl items-center justify-between px-6 py-4"
            >
                <Link :href="projectsIndex().url" class="text-lg font-semibold">
                    TaskBoard
                </Link>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span>{{ page.props.auth.user.name }}</span>
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 px-3 py-1.5 hover:bg-gray-100"
                        @click="logout"
                    >
                        Esci
                    </button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-6 py-8">
            <slot />
        </main>
    </div>
</template>
