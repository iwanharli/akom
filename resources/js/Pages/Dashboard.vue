<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    reports: Array
});

const form = useForm({});

const deleteReport = (id) => {
    if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        form.delete(route('reports.destroy', id));
    }
};

const copyToClipboard = (uuid) => {
    const url = `${window.location.origin}/reports/${uuid}`;
    navigator.clipboard.writeText(url).then(() => {
        alert('Report link copied to clipboard!');
    });
};
</script>

<template>
    <Head title="Report Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold leading-tight text-slate-800 dark:text-slate-100">
                    Intelligence Reports
                </h2>
                <Link
                    :href="route('reports.create')"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg transition-colors shadow-lg"
                >
                    + Create New Report
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-xl sm:rounded-xl border border-slate-200 dark:border-slate-800">
                    <div class="p-6 text-slate-900 dark:text-slate-100">
                        <div v-if="reports.length === 0" class="text-center py-12">
                            <div class="text-slate-500 mb-4">No reports found. Start by creating one.</div>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800">
                                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Report Title</th>
                                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Date</th>
                                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Classification</th>
                                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    <tr v-for="report in reports" :key="report.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-slate-900 dark:text-white" v-html="report.title"></div>
                                            <div class="text-xs text-slate-500 font-mono mt-1">{{ report.uuid.substring(0, 8) }}...</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-400">
                                            {{ new Date(report.report_date).toLocaleDateString('id-ID') }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                {{ report.classification }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span :class="[
                                                'px-2 py-0.5 rounded text-[10px] font-bold uppercase border',
                                                report.status === 'Published' ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 
                                                report.status === 'Draft' ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 
                                                'bg-slate-500/10 text-slate-500 border-slate-500/20'
                                            ]">
                                                {{ report.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right space-x-2">
                                            <button 
                                                @click="copyToClipboard(report.uuid)"
                                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-bold uppercase tracking-wider"
                                            >
                                                Link
                                            </button>
                                            <Link 
                                                :href="route('reports.edit', report.id)"
                                                class="text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 text-xs font-bold uppercase tracking-wider"
                                            >
                                                Edit
                                            </Link>
                                            <button 
                                                @click="deleteReport(report.id)"
                                                class="text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300 text-xs font-bold uppercase tracking-wider"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
