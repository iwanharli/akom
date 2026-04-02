<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { confirm, toast } from '@/Utils/Alert';

const props = defineProps({
    reports: Array,
    canManageAll: Boolean
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const form = useForm({});

// Handle Flash Messages with Toasts
watch(() => page.props.flash, (flash) => {
    if (flash?.success) toast(flash.success);
    if (flash?.error) toast(flash.error, 'error');
}, { deep: true, immediate: true });

const deleteReport = async (id) => {
    const isConfirmed = await confirm(
        'Delete Report?',
        'Are you sure you want to delete this report? This action cannot be undone.',
        'Delete Now'
    );

    if (isConfirmed) {
        form.delete(route('reports.destroy', id), {
            preserveScroll: true
        });
    }
};

const copyToClipboard = (uuid) => {
    const url = `${window.location.origin}/reports/${uuid}`;
    navigator.clipboard.writeText(url).then(() => {
        toast('Link copied to clipboard!');
    });
};

const canEdit = (report) => {
    return props.canManageAll || report.user_id === currentUser.value?.id;
};
</script>

<template>
    <Head title="Report Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="dashboard-header">
                <div>
                    <h2 class="page-title">Intelligence Reports</h2>
                    <p class="page-subtitle">Manage and monitor economic intelligence reports</p>
                </div>
                <Link
                    :href="route('reports.create')"
                    class="btn-primary"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                    Create New Report
                </Link>
            </div>
        </template>

        <div class="dashboard-container">
            <div class="card">
                <div v-if="reports.length === 0" class="empty-state">
                    <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3>No Reports Found</h3>
                    <p>Start your first intelligence brief by creating a new report.</p>
                    <Link :href="route('reports.create')" class="btn-primary mt-4 inline-flex">
                        Create Report
                    </Link>
                </div>

                <div v-else class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Report Title</th>
                                <th>Author</th>
                                <th>Date</th>
                                <th>Classification</th>
                                <th>Clients</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="report in reports" :key="report.id">
                                <td>
                                    <div class="report-title-cell">
                                        <div class="title-text" v-html="report.title"></div>
                                        <div class="title-uuid">{{ report.uuid.substring(0, 8) }}...</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="author-cell">
                                        <div class="author-avatar">{{ (report.user?.name || '?').charAt(0).toUpperCase() }}</div>
                                        <span>{{ report.user?.name || '—' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="date-cell">
                                        {{ new Date(report.report_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge classification">{{ report.classification }}</span>
                                </td>
                                <td>
                                    <div class="client-badge-group" v-if="report.clients && report.clients.length">
                                        <span class="badge client-badge tooltip-trigger" :title="client.name" v-for="client in report.clients.slice(0, 2)" :key="client.id">
                                            {{ client.institution || client.name }}
                                        </span>
                                        <span class="badge client-badge overflow" :title="report.clients.slice(2).map(c => c.institution || c.name).join(', ')" v-if="report.clients.length > 2">
                                            +{{ report.clients.length - 2 }}
                                        </span>
                                    </div>
                                    <span class="text-muted" style="font-size: 13px; color: var(--text-muted);" v-else>—</span>
                                </td>
                                <td>
                                    <span :class="['badge status', report.status.toLowerCase()]">
                                        <span class="status-dot"></span>
                                        {{ report.status }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <button @click="copyToClipboard(report.uuid)" class="action-btn link" title="Copy Link">
                                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"/></svg>
                                    </button>
                                    <Link v-if="canEdit(report)" :href="route('reports.edit', report.id)" class="action-btn edit" title="Edit">
                                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                    </Link>
                                    <button v-if="canEdit(report)" @click="deleteReport(report.id)" class="action-btn delete" title="Delete">
                                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* ─── LAYOUT & CONTAINERS ─── */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}
.page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.5px;
    margin-bottom: 4px;
}
.page-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
}

.dashboard-container {
    padding: 32px 24px;
    max-width: 1400px;
    margin: 0 auto;
}

.card {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.2);
    overflow: hidden;
}

/* ─── BUTTONS ─── */
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--accent), #8b6914);
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.2);
    border: 1px solid rgba(255,255,255,0.1);
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(201, 162, 39, 0.3);
    filter: brightness(1.1);
}

/* ─── TABLE ─── */
.table-responsive {
    overflow-x: auto;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}
.data-table th {
    padding: 16px 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border);
    background: rgba(0,0,0,0.2);
}
.data-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-subtle);
    vertical-align: middle;
}
.data-table tr:last-child td {
    border-bottom: none;
}
.data-table tr:hover {
    background: var(--bg-tertiary);
}

/* Cells */
.report-title-cell { display: flex; flex-direction: column; gap: 4px; }
.title-text { 
    font-size: 14px; 
    font-weight: 600; 
    color: var(--text-primary); 
}
.title-uuid { 
    font-size: 12px; 
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: var(--text-muted); 
}

.author-cell {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--text-secondary);
}
.author-avatar {
    width: 24px; height: 24px;
    border-radius: 6px;
    background: var(--bg-tertiary);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    color: var(--text-primary);
}

.date-cell { font-size: 13px; color: var(--text-secondary); }

/* Badges */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.badge.classification {
    background: rgba(201, 162, 39, 0.1);
    color: var(--accent);
    border: 1px solid rgba(201, 162, 39, 0.2);
}

.client-badge-group {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.client-badge {
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    border: 1px solid var(--border);
    max-width: 120px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
}
.client-badge.overflow {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border-color: rgba(59, 130, 246, 0.2);
}

.badge.status {
    gap: 6px;
    background: var(--bg-tertiary);
    border: 1px solid var(--border);
    color: var(--text-secondary);
}
.badge.status .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.badge.status.published { color: var(--success); background: rgba(63, 185, 80, 0.1); border-color: rgba(63, 185, 80, 0.2); }
.badge.status.draft { color: var(--accent); background: rgba(201, 162, 39, 0.1); border-color: rgba(201, 162, 39, 0.2); }

/* Action Buttons */
.actions-cell {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
.action-btn {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: 1px solid transparent;
    color: var(--text-muted);
    transition: all 0.2s;
    cursor: pointer;
}
.action-btn svg { width: 16px; height: 16px; }
.action-btn.link:hover { color: #3b82f6; background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); }
.action-btn.edit:hover { color: var(--accent); background: rgba(201, 162, 39, 0.1); border-color: rgba(201, 162, 39, 0.2); }
.action-btn.delete:hover { color: var(--danger); background: rgba(248, 81, 73, 0.1); border-color: rgba(248, 81, 73, 0.2); }

/* Empty State */
.empty-state {
    padding: 60px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.empty-icon {
    width: 48px; height: 48px;
    color: var(--text-muted);
    margin-bottom: 16px;
    opacity: 0.5;
}
.empty-state h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}
.empty-state p {
    font-size: 14px;
    color: var(--text-secondary);
    max-width: 400px;
}
</style>
