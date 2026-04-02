<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    report: {
        type: Object,
        default: () => ({
            title: '', subtitle: '', alert_text: '', report_date: new Date().toISOString().substr(0, 10),
            classification: 'INTERNAL', usd_idr_rate: 16500, brent_oil_price: 70,
            asumsi_icp: 70, asumsi_kurs: 16500, status: 'Draft',
            stats: [], sections: [], clients: []
        })
    },
    clients: {
        type: Array,
        default: () => []
    },
    submitLabel: { type: String, default: 'Create Report' },
    isEdit: { type: Boolean, default: false }
});

const form = useForm({
    title: props.report.title || '',
    subtitle: props.report.subtitle || '',
    alert_text: props.report.alert_text || '',
    report_date: props.report.report_date ? new Date(props.report.report_date).toISOString().substr(0, 10) : new Date().toISOString().substr(0, 10),
    classification: props.report.classification || 'INTERNAL',
    usd_idr_rate: props.report.usd_idr_rate || 16500,
    brent_oil_price: props.report.brent_oil_price || 70,
    asumsi_icp: props.report.asumsi_icp || 70,
    asumsi_kurs: props.report.asumsi_kurs || 16500,
    status: props.report.status || 'Draft',
    conclusion_html: props.report.conclusion_html || '',
    stats: props.report.stats || [],
    sections: props.report.sections || [],
    client_ids: props.report.clients ? props.report.clients.map(c => c.id) : []
});

// Initialize Chart JSON text for editing
onMounted(() => {
    if (props.isEdit) {
        form.sections.forEach(section => {
            (section.charts || []).forEach(chart => {
                if (chart.json_payload && !chart.json_payload_text) {
                    chart.json_payload_text = JSON.stringify(chart.json_payload, null, 4);
                }
            });
        });
    }
});

// Helpers for dynamic arrays
const addStat = () => {
    form.stats.push({ label: '', value: '', delta_text: '', status_level: 'ok' });
};
const removeStat = (index) => form.stats.splice(index, 1);

const addSection = () => {
    form.sections.push({
        section_num: form.sections.length + 1,
        title: '',
        badge_text: 'ANALISA SITUASI',
        content_html: '',
        analysis_items: [],
        charts: []
    });
};
const removeSection = (index) => form.sections.splice(index, 1);

const addAnalysisItem = (sectionIndex) => {
    form.sections[sectionIndex].analysis_items.push({
        item_type: 'intel_card',
        tag: 'STRATEGIS',
        heading: '',
        body: '',
        event_date: ''
    });
};
const removeAnalysisItem = (sectionIndex, itemIndex) => form.sections[sectionIndex].analysis_items.splice(itemIndex, 1);

const addChart = (sectionIndex) => {
    form.sections[sectionIndex].charts.push({
        chart_type: 'line_oil',
        json_payload: {},
        json_payload_text: '{}'
    });
};
const removeChart = (sectionIndex, chartIndex) => form.sections[sectionIndex].charts.splice(chartIndex, 1);

// Sync JSON text to actual json_payload before submit
const syncJsonPayloads = () => {
    form.sections.forEach(section => {
        (section.charts || []).forEach(chart => {
            try {
                if (chart.json_payload_text) {
                    chart.json_payload = JSON.parse(chart.json_payload_text);
                }
            } catch (e) {
                // Keep existing json_payload if parse fails
            }
        });
    });
};

const submit = () => {
    syncJsonPayloads();
    if (props.isEdit) {
        form.put(route('reports.update', props.report.id));
    } else {
        form.post(route('reports.store'));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="premium-form">
        <!-- BASE METADATA -->
        <section class="form-card">
            <div class="card-header">
                <h3>1. Report Metadata</h3>
            </div>
            <div class="card-body">
                <div class="form-grid cols-2">
                    <div class="form-group">
                        <label>Title</label>
                        <input v-model="form.title" type="text" class="p-input title-input" placeholder="e.g. Di Tepi Badai" required>
                    </div>
                    <div class="form-group">
                        <label>Classification</label>
                        <select v-model="form.classification" class="p-input select-input">
                            <option>TOP SECRET</option>
                            <option>INTERNAL</option>
                            <option>PUBLIC</option>
                        </select>
                    </div>
                    <div class="form-group col-span-2">
                        <label>Subtitle</label>
                        <textarea v-model="form.subtitle" rows="2" class="p-input" placeholder="Brief description..."></textarea>
                    </div>
                    <div class="form-group col-span-2">
                        <label class="alert-label">Alert Banner Text (HTML allowed)</label>
                        <textarea v-model="form.alert_text" rows="2" class="p-input" placeholder="Siaga fiskal..."></textarea>
                    </div>
                </div>

                <div class="form-grid cols-5 mt-4">
                    <div class="form-group">
                        <label>Date</label>
                        <input v-model="form.report_date" type="date" class="p-input mono-input">
                    </div>
                    <div class="form-group">
                        <label>Brent Price ($)</label>
                        <input v-model="form.brent_oil_price" type="number" step="0.01" class="p-input mono-input">
                    </div>
                    <div class="form-group">
                        <label>Kurs (IDR)</label>
                        <input v-model="form.usd_idr_rate" type="number" class="p-input mono-input">
                    </div>
                    <div class="form-group">
                        <label class="muted-label">Asumsi ICP ($)</label>
                        <input v-model="form.asumsi_icp" type="number" class="p-input mono-input italic">
                    </div>
                    <div class="form-group">
                        <label class="muted-label">Asumsi Kurs</label>
                        <input v-model="form.asumsi_kurs" type="number" class="p-input mono-input italic">
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label>Publish Status</label>
                    <div class="status-selector">
                        <label v-for="status in ['Draft', 'Published', 'Archived']" :key="status" 
                               :class="['status-option', form.status === status && 'is-active']">
                            <input type="radio" v-model="form.status" :value="status" name="status" class="hidden-radio">
                            <span class="status-text">{{ status }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </section>

        <!-- DISTRIBUTION & ACCESS -->
        <section class="form-card">
            <div class="card-header">
                <h3>2. Distribution & Access</h3>
            </div>
            <div class="card-body">
                <div class="client-selection-grid">
                    <label v-for="client in clients" :key="client.id" class="client-checkbox-card" :class="{ 'is-selected': form.client_ids.includes(client.id) }">
                        <input type="checkbox" :value="client.id" v-model="form.client_ids" class="hidden-checkbox">
                        <div class="checkbox-ui">
                            <div class="check-mark">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            </div>
                            <div class="client-info">
                                <span class="client-name">{{ client.name }}</span>
                                <span class="client-institution">{{ client.institution || 'Individual Client' }}</span>
                            </div>
                        </div>
                    </label>
                </div>
                <div v-if="clients.length === 0" class="text-sm text-gray-500 italic p-4 text-center border border-dashed border-gray-700 rounded-lg">
                    No active clients found in directory.
                </div>
            </div>
        </section>

        <!-- INDICATOR STATS -->
        <section class="form-card">
            <div class="card-header flex-between">
                <h3>2. Indicator Stats</h3>
                <button type="button" @click="addStat" class="p-btn ghost">+ Add Stat</button>
            </div>
            <div class="card-body">
                <div class="stats-grid">
                    <div v-for="(stat, idx) in form.stats" :key="idx" class="stat-box">
                        <button type="button" @click="removeStat(idx)" class="remove-btn">✕</button>
                        <input v-model="stat.label" type="text" placeholder="Label (e.g. Brent)" class="stat-input label">
                        <input v-model="stat.value" type="text" placeholder="Value (e.g. ~$99)" class="stat-input value">
                        <input v-model="stat.delta_text" type="text" placeholder="Delta HTML (e.g. Gap: <strong>+41%</strong>)" class="stat-input delta">
                        <select v-model="stat.status_level" class="stat-input status">
                            <option value="ok">Level: OK (Green)</option>
                            <option value="warning">Level: Warning (Orange)</option>
                            <option value="danger">Level: Danger (Red)</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTIONS AND CONTENT -->
        <section class="sections-wrapper">
            <div class="sections-header flex-between">
                <h3>3. Content Sections (Bab Laporan)</h3>
                <button type="button" @click="addSection" class="p-btn outline">+ Add New Bab</button>
            </div>

            <div v-for="(section, sIdx) in form.sections" :key="sIdx" class="form-card section-card">
                <div class="card-header flex-between">
                    <div class="section-title-wrap">
                        <div class="section-number">#{{ sIdx + 1 }}</div>
                        <div class="section-inputs">
                            <input v-model="section.title" type="text" placeholder="Section Title (Bab Title)" class="p-input blank title">
                            <input v-model="section.badge_text" type="text" placeholder="Badge Text" class="p-input blank badge">
                        </div>
                    </div>
                    <button type="button" @click="removeSection(sIdx)" class="p-btn text-danger">Delete Bab</button>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label>Narrative Content (HTML Allowed)</label>
                        <textarea v-model="section.content_html" rows="8" class="p-input narrative-text" placeholder="Write the report narrative here..."></textarea>
                    </div>

                    <!-- Nested Items (Timeline/Intel) -->
                    <div class="nested-group">
                        <div class="nested-header flex-between">
                            <h4>Nested Analysis Items (Timeline/Intel/Facts)</h4>
                            <button type="button" @click="addAnalysisItem(sIdx)" class="p-btn subtle">+ Add Item</button>
                        </div>
                        <div class="items-list">
                            <div v-for="(item, iIdx) in section.analysis_items" :key="iIdx" class="nested-item">
                                <div class="item-col type">
                                    <select v-model="item.item_type" class="p-input mini">
                                        <option value="intel_card">Intel Card</option>
                                        <option value="timeline">Timeline</option>
                                        <option value="fact_list">Fact List</option>
                                    </select>
                                </div>
                                <div class="item-col content">
                                    <div class="split-inputs">
                                        <input v-model="item.heading" type="text" placeholder="Heading/Date" class="p-input blank-sub">
                                        <input v-model="item.tag" type="text" placeholder="Tag" class="p-input blank-sub accent">
                                    </div>
                                    <textarea v-model="item.body" placeholder="Content text..." rows="2" class="p-input blank-sub block w-full"></textarea>
                                </div>
                                <div class="item-col action">
                                    <button type="button" @click="removeAnalysisItem(sIdx, iIdx)" class="remove-btn relative">✕</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="nested-group mt-4">
                        <div class="nested-header flex-between">
                            <h4>Visual Modules (Charts)</h4>
                            <button type="button" @click="addChart(sIdx)" class="p-btn subtle">+ Add Chart</button>
                        </div>
                        <div class="charts-list">
                            <div v-for="(chart, cIdx) in section.charts" :key="cIdx" class="chart-box">
                                <div class="chart-header flex-between">
                                    <select v-model="chart.chart_type" class="p-input mini flex-grow max-w-sm">
                                        <option value="line_oil">Line Chart: Oil vs Asumsi</option>
                                        <option value="fiscal_stress">Bar Chart: Fiscal Stress</option>
                                        <option value="mbg_evolution">Bar Chart: MBG Evolution</option>
                                    </select>
                                    <button type="button" @click="removeChart(sIdx, cIdx)" class="remove-btn relative">✕</button>
                                </div>
                                <div class="chart-helper">Enter JSON payload below. Schema must match chart type.</div>
                                <textarea v-model="chart.json_payload_text" rows="8" class="p-input mono-input w-full mt-2" placeholder="Enter JSON..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONCLUSION -->
        <section class="form-card">
            <div class="card-header">
                <h3>4. Final Conclusion (HTML Allowed)</h3>
            </div>
            <div class="card-body">
                <textarea v-model="form.conclusion_html" rows="6" class="p-input narrative-text" placeholder="Write final summary..."></textarea>
            </div>
        </section>

        <!-- SUBMIT -->
        <div class="p-footer-actions">
            <button type="button" @click="$inertia.visit(route('dashboard'))" class="p-btn ghost lg">Cancel</button>
            <button :disabled="form.processing" type="submit" class="p-btn primary xl">
                {{ form.processing ? 'Saving...' : submitLabel }}
            </button>
        </div>
    </form>
</template>

<style scoped>
/* ─── GLOBALS & COLORS ─── */
.premium-form {
    display: flex;
    flex-direction: column;
    gap: 32px;
    padding-bottom: 80px;
    color: var(--text-primary);
}

.flex-between { display: flex; justify-content: space-between; align-items: center; }
.w-full { width: 100%; }
.mt-4 { margin-top: 16px; }
.mt-2 { margin-top: 8px; }

/* ─── CARDS ─── */
.form-card {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.2);
    overflow: hidden;
}
.card-header {
    padding: 16px 24px;
    border-bottom: 1px solid var(--border);
    background: rgba(0,0,0,0.2);
}
.card-header h3 {
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-primary);
    margin: 0;
}
.card-body { padding: 24px; }

/* ─── FORMS ─── */
.form-grid {
    display: grid;
    gap: 20px;
}
.form-grid.cols-2 { grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }
.form-grid.cols-5 { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); }
.col-span-2 { grid-column: span 2; }
@media (max-width: 768px) { .col-span-2 { grid-column: span 1; } }

.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-secondary);
    letter-spacing: 0.5px;
}
.alert-label { color: var(--danger) !important; }
.muted-label { color: var(--text-muted) !important; }

/* Basic Inputs */
.p-input {
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    color: var(--text-primary);
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    transition: all 0.2s;
    outline: none;
}
.p-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 1px var(--accent);
}
.p-input.title-input { font-weight: 600; }
.p-input.mono-input { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
.p-input.narrative-text { font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif; font-size: 15px; line-height: 1.6; }
.p-input.blank { background: transparent; border: none; padding: 0; }
.p-input.blank:focus { box-shadow: none; }
.p-input.mini { padding: 6px 10px; font-size: 12px; }

/* Remove default select styling to better match dark theme */
select.p-input option { background: var(--bg-elevated); }

/* Status Selector */
.status-selector {
    display: flex;
    gap: 8px;
    background: var(--bg-tertiary);
    padding: 4px;
    border-radius: 10px;
    border: 1px solid var(--border);
    width: fit-content;
}
.status-option {
    padding: 8px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    user-select: none;
}
.status-option:hover {
    color: var(--text-primary);
}
.status-option.is-active {
    background: linear-gradient(135deg, var(--accent), #8b6914);
    color: #fff;
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.3);
}
.hidden-radio {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

/* ─── BUTTONS ─── */
.p-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 6px;
    padding: 6px 12px;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
}
.p-btn.ghost { background: transparent; color: var(--accent); }
.p-btn.ghost:hover { background: rgba(201,162,39,0.1); }
.p-btn.ghost.lg { font-size: 13px; padding: 8px 16px; color: var(--text-secondary); }
.p-btn.ghost.lg:hover { color: var(--text-primary); background: var(--bg-tertiary); }
.p-btn.outline { background: transparent; border-color: var(--accent); color: var(--accent); }
.p-btn.outline:hover { background: var(--accent); color: #fff; }
.p-btn.text-danger { color: var(--danger); background: transparent; }
.p-btn.text-danger:hover { background: rgba(248,81,73,0.1); }
.p-btn.subtle { color: var(--text-secondary); }
.p-btn.subtle:hover { color: var(--text-primary); background: var(--bg-tertiary); }

.p-btn.primary {
    background: linear-gradient(135deg, var(--accent), #8b6914);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 4px 12px rgba(201,162,39,0.2);
}
.p-btn.primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(201,162,39,0.3);
    filter: brightness(1.1);
}
.p-btn.primary.xl { font-size: 14px; padding: 12px 24px; border-radius: 8px; }

.remove-btn {
    position: absolute;
    top: 8px; right: 8px;
    color: var(--text-muted);
    border: none; background: none; font-size: 14px;
    cursor: pointer; opacity: 0; transition: all 0.2s;
}
.remove-btn:hover { color: var(--danger); }
.remove-btn.relative { position: static; opacity: 1; }
*:hover > .remove-btn { opacity: 1; }

/* ─── SECTION SPECIFICS ─── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
}
.stat-box {
    position: relative;
    padding: 16px;
    border-radius: 10px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    display: flex; flex-direction: column; gap: 8px;
}
.stat-input {
    background: transparent; border: none; outline: none; color: var(--text-primary); width: 100%;
}
.stat-input.label { border-bottom: 1px solid var(--border); padding-bottom: 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); }
.stat-input.value { font-size: 24px; font-weight: 800; }
.stat-input.delta { font-size: 11px; color: var(--text-muted); }
.stat-input.status { font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 4px 0; background: var(--bg-tertiary); border-radius: 4px; padding-left: 4px; }

.sections-wrapper { display: flex; flex-direction: column; gap: 24px; }
.sections-header h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); }
.section-card { border-left: 4px solid var(--accent); }

.section-title-wrap { display: flex; align-items: center; gap: 16px; width: 100%; }
.section-number {
    width: 40px; height: 40px; border-radius: 8px;
    background: var(--bg-secondary); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 800; color: var(--accent);
}
.section-inputs { display: flex; flex-direction: column; flex-grow: 1; }
.section-inputs .title { font-size: 20px; font-weight: 700; color: var(--text-primary); }
.section-inputs .badge { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); }

/* Nested Items */
.nested-group { margin-top: 24px; }
.nested-header { padding: 12px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); margin-bottom: 16px; }
.nested-header h4 { font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin: 0; }
.items-list { display: flex; flex-direction: column; gap: 12px; }
.nested-item {
    display: flex; gap: 16px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 8px; padding: 12px;
}
.item-col.type { width: 120px; flex-shrink: 0; }
.item-col.content { flex-grow: 1; display: flex; flex-direction: column; gap: 8px; }
.item-col.action { display: flex; align-items: flex-start; justify-content: flex-end; }
.split-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-bottom: 1px solid var(--border); padding-bottom: 8px; }
.blank-sub { background: transparent; border: none; color: var(--text-primary); font-size: 13px; outline: none; }
.blank-sub.accent { color: var(--accent); font-size: 11px; font-weight: 700; text-transform: uppercase; }

/* Charts */
.charts-list { display: flex; flex-direction: column; gap: 16px; }
.chart-box {
    background: rgba(201, 162, 39, 0.05); /* very subtle gold accent */
    border: 1px dashed rgba(201, 162, 39, 0.3);
    border-radius: 8px; padding: 16px;
}
.chart-helper { font-size: 11px; color: var(--text-muted); margin-top: 4px; font-style: italic; }

/* Footer */
.p-footer-actions {
    position: sticky;
    bottom: 24px;
    background: rgba(13, 17, 23, 0.85); /* var(--bg-secondary) with opacity */
    backdrop-filter: blur(12px);
    border: 1px solid var(--border);
    padding: 16px 24px;
    border-radius: 12px;
    display: flex;
    justify-content: flex-end;
    gap: 16px;
    box-shadow: 0 16px 48px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.05);
    z-index: 50;
    width: 100%;
}

/* Client Selection Grid */
.client-selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 12px;
}
.client-checkbox-card {
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
}
.hidden-checkbox {
    position: absolute;
    opacity: 0;
    width: 0; height: 0;
}
.checkbox-ui {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 10px;
    transition: all 0.2s;
}
.client-checkbox-card:hover .checkbox-ui {
    border-color: rgba(201, 162, 39, 0.4);
    background: var(--bg-tertiary);
}
.client-checkbox-card.is-selected .checkbox-ui {
    border-color: var(--accent);
    background: rgba(201, 162, 39, 0.05);
    box-shadow: 0 0 0 1px var(--accent);
}

.check-mark {
    width: 20px; height: 20px;
    border-radius: 4px;
    border: 1.5px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    color: transparent;
    transition: all 0.2s;
    flex-shrink: 0;
}
.is-selected .check-mark {
    background: var(--accent);
    border-color: var(--accent);
    color: #000;
}

.client-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.client-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.client-institution {
    font-size: 11px;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
