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
            stats: [], sections: []
        })
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
    stats: props.report.stats || [],
    sections: props.report.sections || []
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
        json_payload: {}
    });
};
const removeChart = (sectionIndex, chartIndex) => form.sections[sectionIndex].charts.splice(chartIndex, 1);

const submit = () => {
    if (props.isEdit) {
        form.put(route('reports.update', props.report.id));
    } else {
        form.post(route('reports.store'));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-12">
        <!-- BASE METADATA -->
        <section class="bg-white dark:bg-slate-900 shadow sm:rounded-lg p-6 space-y-6">
            <h3 class="text-lg font-bold border-b border-slate-100 dark:border-slate-800 pb-3 text-slate-800 dark:text-white uppercase tracking-wider">
                1. Report Metadata
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Title</label>
                    <input v-model="form.title" type="text" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-sm font-semibold" placeholder="e.g. Di Tepi Badai" required>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Classification</label>
                    <select v-model="form.classification" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-sm font-semibold">
                        <option>TOP SECRET</option>
                        <option>INTERNAL</option>
                        <option>PUBLIC</option>
                    </select>
                </div>
                <div class="md:col-span-2 space-y-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Subtitle</label>
                    <textarea v-model="form.subtitle" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-sm" placeholder="Brief description..."></textarea>
                </div>
                <div class="md:col-span-2 space-y-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase text-rose-500">Alert Banner Text (HTML allowed)</label>
                    <textarea v-model="form.alert_text" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-sm" placeholder="Siaga fiskal..."></textarea>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Date</label>
                    <input v-model="form.report_date" type="date" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-xs font-mono">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Brent Price ($)</label>
                    <input v-model="form.brent_oil_price" type="number" step="0.01" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-sm font-mono">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Kurs (IDR)</label>
                    <input v-model="form.usd_idr_rate" type="number" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-sm font-mono">
                </div>
                <!-- Asumsi -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-400 uppercase">Asumsi ICP ($)</label>
                    <input v-model="form.asumsi_icp" type="number" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-sm font-mono italic">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-400 uppercase">Asumsi Kurs</label>
                    <input v-model="form.asumsi_kurs" type="number" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-sm font-mono italic">
                </div>
            </div>
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-500 uppercase">Publish Status</label>
                <div class="flex gap-4">
                    <label v-for="status in ['Draft', 'Published', 'Archived']" :key="status" class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" v-model="form.status" :value="status" class="text-indigo-600 focus:ring-indigo-500 bg-slate-100 dark:bg-slate-800 border-slate-300">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase group-hover:text-indigo-500 transition-colors">{{ status }}</span>
                    </label>
                </div>
            </div>
        </section>

        <!-- INDICATOR STATS -->
        <section class="bg-white dark:bg-slate-900 shadow sm:rounded-lg p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-wider">2. Indicator Stats</h3>
                <button type="button" @click="addStat" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase">+ Add Stat</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(stat, idx) in form.stats" :key="idx" class="p-4 border border-slate-200 dark:border-slate-800 rounded-xl space-y-4 relative group">
                    <button type="button" @click="removeStat(idx)" class="absolute top-2 right-2 text-rose-500 opacity-0 group-hover:opacity-100 transition-opacity">✕</button>
                    <div class="space-y-3">
                        <input v-model="stat.label" type="text" placeholder="Label (e.g. Brent)" class="w-full bg-transparent border-t-0 border-x-0 border-b border-slate-200 dark:border-slate-700 focus:ring-0 text-xs font-bold uppercase py-1">
                        <input v-model="stat.value" type="text" placeholder="Value (e.g. ~$99)" class="w-full bg-transparent border-0 focus:ring-0 text-xl font-bold py-0">
                        <input v-model="stat.delta_text" type="text" placeholder="Delta HTML (e.g. Gap: <strong>+41%</strong>)" class="w-full bg-transparent border-0 focus:ring-0 text-[10px] text-slate-500 py-0">
                        <select v-model="stat.status_level" class="w-full bg-slate-50 dark:bg-slate-800 border-0 rounded text-[10px] uppercase font-bold py-1">
                            <option value="ok">Level: OK (Green)</option>
                            <option value="warning">Level: Warning (Orange)</option>
                            <option value="danger">Level: Danger (Red)</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTIONS AND CONTENT -->
        <section class="space-y-8">
            <div class="flex justify-between items-center px-2">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-wide">3. Content Sections (Bab Laporan)</h3>
                <button type="button" @click="addSection" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs font-bold uppercase transition-transform hover:scale-105">+ Add New Bab</button>
            </div>

            <div v-for="(section, sIdx) in form.sections" :key="sIdx" class="bg-white dark:bg-slate-900 shadow sm:rounded-lg p-6 border-l-4 border-indigo-500 space-y-6">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded flex items-center justify-center font-bold text-slate-400">#{{ sIdx + 1 }}</div>
                        <div class="space-y-1">
                            <input v-model="section.title" type="text" placeholder="Section Title (Bab Title)" class="w-full bg-transparent border-0 focus:ring-0 text-lg font-bold py-0">
                            <input v-model="section.badge_text" type="text" placeholder="Badge Text" class="w-full bg-transparent border-0 focus:ring-0 text-[10px] uppercase font-bold text-indigo-500 py-0">
                        </div>
                    </div>
                    <button type="button" @click="removeSection(sIdx)" class="text-rose-500 text-sm font-bold uppercase">Delete Bab</button>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Narrative Content (HTML Allowed)</label>
                    <textarea v-model="section.content_html" rows="10" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-indigo-500 text-sm font-serif" placeholder="Write the report narrative here..."></textarea>
                </div>

                <!-- Nested Items (Timeline/Intel) -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-t border-slate-100 dark:border-slate-800 pt-4">
                        <h4 class="text-xs font-bold text-slate-400 uppercase">Nested Analysis Items (Timeline/Intel/Facts)</h4>
                        <button type="button" @click="addAnalysisItem(sIdx)" class="text-[10px] font-bold text-indigo-500 uppercase">+ Add Item</button>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div v-for="(item, iIdx) in section.analysis_items" :key="iIdx" class="grid grid-cols-12 gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg items-start border border-slate-100 dark:border-slate-800">
                            <div class="col-span-12 md:col-span-2">
                                <select v-model="item.item_type" class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-[10px] font-bold py-1 px-2 rounded">
                                    <option value="intel_card">Intel Card</option>
                                    <option value="timeline">Timeline</option>
                                    <option value="fact_list">Fact List</option>
                                </select>
                            </div>
                            <div class="col-span-12 md:col-span-9 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input v-model="item.heading" type="text" placeholder="Heading/Date" class="w-full bg-transparent border-0 border-b border-slate-300 dark:border-slate-600 focus:ring-0 text-xs font-bold">
                                <input v-model="item.tag" type="text" placeholder="Tag (Political, etc)" class="w-full bg-transparent border-0 border-b border-slate-300 dark:border-slate-600 focus:ring-0 text-[10px] font-bold uppercase text-indigo-500">
                                <textarea v-model="item.body" placeholder="Content text..." rows="2" class="col-span-2 w-full bg-transparent border-0 focus:ring-0 text-xs"></textarea>
                            </div>
                            <div class="col-span-1 text-right pt-1">
                                <button type="button" @click="removeAnalysisItem(sIdx, iIdx)" class="text-rose-500">✕</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-t border-slate-100 dark:border-slate-800 pt-4">
                        <h4 class="text-xs font-bold text-slate-400 uppercase">Visual Modules (Charts)</h4>
                        <button type="button" @click="addChart(sIdx)" class="text-[10px] font-bold text-indigo-500 uppercase">+ Add Chart</button>
                    </div>
                    <div v-for="(chart, cIdx) in section.charts" :key="cIdx" class="p-3 bg-indigo-50/30 dark:bg-indigo-900/10 rounded-lg border border-indigo-100 dark:border-indigo-900/30 flex gap-4">
                        <div class="flex-grow space-y-2">
                            <select v-model="chart.chart_type" class="bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-xs font-bold rounded py-1">
                                <option value="line_oil">Line Chart: Oil vs Asumsi</option>
                                <option value="fiscal_stress">Bar Chart: Fiscal Stress</option>
                            </select>
                            <div class="text-[10px] text-slate-400 italic">JSON Data payload needs manual entry or structure expansion for now.</div>
                        </div>
                        <button type="button" @click="removeChart(sIdx, cIdx)" class="text-rose-500">✕</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- SUBMIT -->
        <div class="sticky bottom-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-4 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 flex justify-end gap-4 z-50">
            <button type="button" @click="$inertia.visit(route('dashboard'))" class="px-6 py-2 text-slate-600 dark:text-slate-400 font-bold uppercase tracking-widest text-xs hover:text-slate-800 dark:hover:text-white transition-colors">Cancel</button>
            <button :disabled="form.processing" type="submit" class="px-8 py-2 bg-indigo-600 text-white font-bold uppercase tracking-widest text-xs rounded-xl shadow-lg hover:shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                {{ form.processing ? 'Saving...' : submitLabel }}
            </button>
        </div>
    </form>
</template>
