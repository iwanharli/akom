<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    canManageUsers: Boolean
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="dashboard-header">
                <div>
                    <h2 class="page-title">Executive Dashboard</h2>
                    <p class="page-subtitle">Overview of system intelligence</p>
                </div>
            </div>
        </template>

        <div class="dashboard-container">
            <div class="stats-grid">
                <!-- Total Reports -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Total Reports</h3>
                        <p class="stat-value">{{ stats.total_reports }}</p>
                    </div>
                </div>

                <!-- Published Reports -->
                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Published</h3>
                        <p class="stat-value">{{ stats.published_reports }}</p>
                    </div>
                </div>

                <!-- Draft Reports -->
                <div class="stat-card">
                    <div class="stat-icon text-accent">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Drafts</h3>
                        <p class="stat-value">{{ stats.draft_reports }}</p>
                    </div>
                </div>

                <!-- Users (Superadmin only) -->
                <div v-if="canManageUsers" class="stat-card">
                    <div class="stat-icon text-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Total Admin</h3>
                        <p class="stat-value">{{ stats.total_users }}</p>
                    </div>
                </div>

                <!-- Total Clients -->
                <div class="stat-card">
                    <div class="stat-icon text-accent">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Total Clients</h3>
                        <p class="stat-value">{{ stats.total_clients }}</p>
                    </div>
                </div>
            </div>

            <!-- Future area for charts/logs -->
            <div class="dashboard-widgets mt-8">
                <div class="widget-card">
                    <div class="widget-header">
                        <h4>System Activity Overview</h4>
                    </div>
                    <div class="widget-body empty-state-blur">
                        <p>Detailed activity logs and aggregate metric charts will appear here.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
}

@media (max-width: 1400px) {
    .stats-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 1024px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .stats-grid { grid-template-columns: 1fr; }
}

.stat-card {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.2);
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    background: var(--bg-tertiary);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
}
.stat-icon svg { width: 28px; height: 28px; }
.stat-icon.text-success { color: var(--success); background: rgba(63, 185, 80, 0.1); border-color: rgba(63, 185, 80, 0.2); }
.stat-icon.text-accent { color: var(--accent); background: rgba(201, 162, 39, 0.1); border-color: rgba(201, 162, 39, 0.2); }
.stat-icon.text-blue { color: #3b82f6; background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); }

.stat-info h3 {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-secondary);
    margin-bottom: 4px;
    letter-spacing: 0.5px;
}
.stat-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
}

.mt-8 { margin-top: 32px; }

.widget-card {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.2);
    overflow: hidden;
}
.widget-header {
    padding: 16px 24px;
    border-bottom: 1px solid var(--border);
    background: rgba(0,0,0,0.2);
}
.widget-header h4 {
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-primary);
    margin: 0;
}
.widget-body {
    padding: 60px 24px;
    text-align: center;
}
.empty-state-blur p {
    font-size: 14px;
    color: var(--text-muted);
    font-style: italic;
}
</style>
