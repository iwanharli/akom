<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReportForm from './Partials/ReportForm.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    report: Object
});
</script>

<template>
    <Head :title="'Editing: ' + report.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-slate-800 dark:text-slate-100">
                Edit Intelligence Report
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <ReportForm :report="report" submit-label="Update Report" :is-edit="true" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
