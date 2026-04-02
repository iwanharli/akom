<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    report: Object
});

const getStatusClass = (level) => {
    const map = {
        'danger': 'danger',
        'warning': 'warning',
        'ok': 'ok'
    };
    return map[level] || 'ok';
};

const getBadgeClass = (badge) => {
    if (!badge) return 'badge-intel';
    const text = badge.toLowerCase();
    if (text.includes('analisa')) return 'badge-analysis';
    if (text.includes('situasi')) return 'badge-danger';
    if (text.includes('intel')) return 'badge-intel';
    return 'badge-intel';
};

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
});
</script>

<template>
    <Head :title="'INTEL BRIEF: ' + report.title" />

    <div class="report-wrapper">
        <!-- ══════════════════ MASTHEAD ══════════════════ -->
        <header class="masthead">
            <div class="masthead-inner">
                <div class="classification-bar">
                    <div class="pulse"></div>
                    ANALISA INTELIJEN EKONOMI
                    <span>•</span>
                    {{ report.classification }}
                    <span>•</span>
                    {{ new Date(report.report_date).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }).toUpperCase() }}
                </div>
                <h1 class="main-title" v-html="report.title.replace(/<em>/g, '<em class=\'accent-text\'>')">
                </h1>
                <p class="subtitle">{{ report.subtitle }}</p>
                <div class="meta-row">
                    <div>Tanggal Analisis: <span>{{ new Date(report.report_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span></div>
                    <div v-if="report.brent_oil_price">Brent Crude: <span>${{ report.brent_oil_price }}/bbl</span></div>
                    <div v-if="report.usd_idr_rate">Kurs USD/IDR: <span>Rp{{ Number(report.usd_idr_rate).toLocaleString('id-ID') }}</span></div>
                    <div v-if="report.asumsi_icp">APBN Asumsi ICP: <span>${{ report.asumsi_icp }}/bbl</span></div>
                    <div v-if="report.asumsi_kurs">APBN Asumsi Kurs: <span>Rp{{ Number(report.asumsi_kurs).toLocaleString('id-ID') }}</span></div>
                </div>
            </div>
        </header>

        <!-- ══════════════════ MAIN ══════════════════ -->
        <main class="container">
            <!-- ALERT -->
            <div v-if="report.alert_text" class="alert-banner fade-in">
                <span class="alert-icon">⚠</span>
                <div v-html="report.alert_text"></div>
            </div>

            <!-- DASHBOARD STATS -->
            <div v-if="report.stats?.length" class="stats-grid fade-in">
                <div v-for="stat in report.stats" :key="stat.id" 
                    :class="['stat-card', getStatusClass(stat.status_level)]">
                    <div class="stat-label">{{ stat.label }}</div>
                    <div class="stat-value">{{ stat.value }}</div>
                    <div class="stat-delta" v-html="stat.delta_text"></div>
                </div>
            </div>

            <!-- SECTIONS -->
            <template v-for="section in report.sections" :key="section.id">
                <section class="section fade-in">
                    <div class="section-header">
                        <div class="section-num">{{ String(section.section_num).padStart(2, '0') }}</div>
                        <div>
                            <div class="section-title" v-html="section.title.replace(/\n/g, '<br>')"></div>
                        </div>
                        <div v-if="section.badge_text" :class="['section-badge', getBadgeClass(section.badge_text)]">
                            {{ section.badge_text }}
                        </div>
                    </div>

                    <div class="prose" v-html="section.content_html"></div>

                    <!-- TWO COLUMN LAYOUT FOR ITEMS -->
                    <div v-if="section.analysis_items?.length" class="two-col mt-8">
                        <!-- Timeline -->
                        <div class="geo-card" v-if="section.analysis_items.some(i => i.item_type === 'timeline')">
                            <div class="geo-card-title">↯ TIMELINE ESKALASI</div>
                            <div class="timeline">
                                <div v-for="item in section.analysis_items.filter(i => i.item_type === 'timeline')" :key="item.id" class="tl-item">
                                    <div class="tl-date">{{ item.event_date }}</div>
                                    <div class="tl-text" v-html="item.heading + ': ' + (item.body || '')"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Fact List -->
                        <div class="geo-card" v-if="section.analysis_items.some(i => i.item_type === 'fact_list')">
                            <div class="geo-card-title">🇮🇩 DATA KERENTANAN</div>
                            <ul class="fact-list">
                                <li v-for="item in section.analysis_items.filter(i => i.item_type === 'fact_list')" :key="item.id" v-html="item.body"></li>
                            </ul>
                        </div>
                    </div>

                    <!-- INTEL GRID (full width if not in two-col) -->
                    <div class="intel-grid mt-8" v-if="section.analysis_items.some(i => i.item_type === 'intel_card')">
                        <div v-for="item in section.analysis_items.filter(i => i.item_type === 'intel_card')" :key="item.id" class="intel-card">
                            <div class="intel-card-tag">{{ item.tag }}</div>
                            <h4>{{ item.heading }}</h4>
                            <p v-html="item.body"></p>
                        </div>
                    </div>

                    <!-- CHARTS -->
                    <template v-for="chart in section.charts" :key="chart.id">
                        <!-- OIL CHART -->
                        <div v-if="chart.chart_type === 'line_oil'" class="chart-container fade-in">
                            <div class="chart-title">PERBANDINGAN ASUMSI VS REALITA HARGA BRENT (USD/bbl)</div>
                            <svg viewBox="0 0 700 180" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto;overflow:visible">
                                <line x1="60" y1="20" x2="680" y2="20" stroke="#1e2530" stroke-width="1"></line>
                                <line x1="60" y1="55" x2="680" y2="55" stroke="#1e2530" stroke-width="1"></line>
                                <line x1="60" y1="90" x2="680" y2="90" stroke="#1e2530" stroke-width="1"></line>
                                <line x1="60" y1="125" x2="680" y2="125" stroke="#1e2530" stroke-width="1"></line>
                                <line x1="60" y1="160" x2="680" y2="160" stroke="#1e2530" stroke-width="1"></line>
                                <text x="50" y="24" fill="#7a8090" font-size="9" text-anchor="end">$120</text>
                                <text x="50" y="59" fill="#7a8090" font-size="9" text-anchor="end">$100</text>
                                <text x="50" y="94" fill="#7a8090" font-size="9" text-anchor="end">$80</text>
                                <text x="50" y="129" fill="#7a8090" font-size="9" text-anchor="end">$60</text>
                                <text x="50" y="164" fill="#7a8090" font-size="9" text-anchor="end">$40</text>
                                <line x1="60" y1="107" x2="680" y2="107" stroke="#e8a020" stroke-width="1.5" stroke-dasharray="6,4" opacity="0.6"></line>
                                <text x="685" y="111" fill="#e8a020" font-size="8">APBN $70</text>
                                <defs>
                                    <linearGradient id="priceGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#e74c3c" stop-opacity="0.3"></stop>
                                        <stop offset="100%" stop-color="#e74c3c" stop-opacity="0"></stop>
                                    </linearGradient>
                                </defs>
                                <path d="M60,107 L100,104 L200,104 L320,57 L450,37 L520,25 L570,37 L620,44 L670,48 L670,160 L60,160 Z" fill="url(#priceGrad)"></path>
                                <path d="M60,107 L100,104 L200,104 L320,57 L450,37 L520,25 L570,37 L620,44 L670,48" fill="none" stroke="#e74c3c" stroke-width="2.5"></path>
                                <circle cx="200" cy="104" r="4" fill="#e74c3c"></circle>
                                <circle cx="670" cy="48" r="4" fill="#e67e22"></circle>
                            </svg>
                        </div>

                        <!-- FISCAL STRESS BAR -->
                        <div v-if="chart.chart_type === 'fiscal_stress'" class="fiscal-bar-wrap fade-in">
                            <div class="chart-title">SKENARIO TEKANAN DEFISIT APBN 2026</div>
                            <div v-for="(scenario, idx) in chart.json_payload" :key="idx" style="margin-bottom:14px">
                                <div class="fiscal-bar-label">
                                    <span>{{ scenario.name }}</span>
                                    <span :style="{ color: scenario.color }">{{ scenario.label }}</span>
                                </div>
                                <div class="bar-track">
                                    <div class="bar-fill" :style="{ width: scenario.width, background: scenario.gradient }"></div>
                                    <div class="bar-marker" style="left:72.7%">
                                        <div class="bar-marker-label">Batas 3%</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MBG EVOLUTION BAR CHART -->
                        <div v-if="chart.chart_type === 'mbg_evolution'" class="chart-container fade-in">
                            <div class="chart-title">ESKALASI ANGGARAN MBG (TRILIUN RUPIAH)</div>
                            <svg viewBox="0 0 700 160" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto;overflow:visible">
                                <rect x="80" y="108" width="70" height="32" fill="#e8a020" opacity="0.7" rx="2"></rect>
                                <text x="115" y="104" fill="#e8a020" font-size="10" text-anchor="middle" font-weight="600">Rp71T</text>
                                <text x="115" y="148" fill="#7a8090" font-size="9" text-anchor="middle">APBN 2025</text>
                                <text x="115" y="158" fill="#7a8090" font-size="8" text-anchor="middle">Awal</text>

                                <rect x="200" y="72" width="70" height="68" fill="#e8a020" opacity="0.8" rx="2"></rect>
                                <text x="235" y="68" fill="#e8a020" font-size="10" text-anchor="middle" font-weight="600">Rp171T</text>
                                <text x="235" y="148" fill="#7a8090" font-size="9" text-anchor="middle">APBN 2025</text>
                                <text x="235" y="158" fill="#7a8090" font-size="8" text-anchor="middle">+Tambahan</text>

                                <rect x="320" y="54" width="70" height="86" fill="#e67e22" opacity="0.8" rx="2"></rect>
                                <text x="355" y="50" fill="#e67e22" font-size="10" text-anchor="middle" font-weight="600">Rp217T</text>
                                <text x="355" y="148" fill="#7a8090" font-size="9" text-anchor="middle">KEM-PPKF</text>
                                <text x="355" y="158" fill="#7a8090" font-size="8" text-anchor="middle">2026 Usulan</text>

                                <rect x="440" y="6" width="70" height="134" fill="#e74c3c" rx="2"></rect>
                                <text x="475" y="18" fill="#fff" font-size="11" text-anchor="middle" font-weight="600">Rp335T</text>
                                <text x="475" y="148" fill="#e74c3c" font-size="9" text-anchor="middle" font-weight="600">APBN 2026</text>
                                <text x="475" y="158" fill="#e74c3c" font-size="8" text-anchor="middle">FINAL (+53,8%)</text>

                                <line x1="60" y1="140" x2="680" y2="140" stroke="#1e2530" stroke-width="1"></line>
                                <text x="570" y="95" fill="#e74c3c" font-size="16" text-anchor="middle" font-weight="bold">+371%</text>
                                <text x="570" y="108" fill="#7a8090" font-size="9" text-anchor="middle">dalam 1 tahun</text>
                            </svg>
                        </div>
                    </template>
                </section>
                <div class="sep"></div>
            </template>

            <!-- CONCLUSION BOX -->
            <div v-if="report.conclusion_html" class="conclusion fade-in">
                <div class="conclusion-title">✦ KESIMPULAN ANALISA INTELIJEN ✦</div>
                <div class="prose" v-html="report.conclusion_html"></div>
            </div>
        </main>

        <footer class="container">
            <div class="footer">
                <div>ANALISA EKONOMI INDONESIA | {{ new Date(report.report_date).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }).toUpperCase() }}</div>
                <div>DATA PER {{ new Date(report.report_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* ─── BASE DESIGN SYSTEM ─── */
.report-wrapper {
    --bg: #0a0c0f;
    --surface: #111318;
    --surface2: #171b22;
    --border: #1e2530;
    --accent: #e8a020;
    --accent2: #c0392b;
    --accent3: #27ae60;
    --text: #d4cfc8;
    --text-dim: #7a8090;
    --text-bright: #f0ece4;
    --red: #e74c3c;
    --orange: #e67e22;
    --green: #2ecc71;
    --yellow: #f1c40f;

    background: var(--bg);
    color: var(--text);
    font-family: 'Source Serif 4', Georgia, serif;
    line-height: 1.7;
    font-weight: 300;
    min-height: 100vh;
}

/* ─── HEADER ─── */
.masthead {
    background: var(--surface);
    border-bottom: 2px solid var(--accent);
    padding: 0;
    position: relative;
    overflow: hidden;
}
.masthead-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 40px 32px 32px;
}
.classification-bar {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10px;
    letter-spacing: 3px;
    color: var(--accent);
    text-transform: uppercase;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.classification-bar span { color: var(--text-dim); }
.pulse {
    width: 8px; height: 8px;
    background: var(--accent);
    border-radius: 50%;
    display: inline-block;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50% { opacity:0.3; transform:scale(0.7); }
}
.main-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(40px, 6vw, 76px);
    color: var(--text-bright);
    line-height: 0.9;
    letter-spacing: 2px;
    margin-bottom: 12px;
}
:deep(.accent-text) {
    color: var(--accent);
    font-style: normal;
}
.subtitle {
    font-family: 'Source Serif 4', serif;
    font-style: italic;
    font-size: 16px;
    color: var(--text-dim);
    margin-bottom: 24px;
    max-width: 700px;
}
.meta-row {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    color: var(--text-dim);
    padding-top: 20px;
    border-top: 1px solid var(--border);
}
.meta-row span { color: var(--accent); }

/* ─── MAIN LAYOUT ─── */
.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 32px 80px;
}

/* ─── ALERT BANNER ─── */
.alert-banner {
    background: linear-gradient(135deg, rgba(231,76,60,0.15), rgba(192,57,43,0.08));
    border: 1px solid rgba(231,76,60,0.4);
    border-left: 4px solid var(--red);
    border-radius: 4px;
    padding: 16px 20px;
    margin: 32px 0;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    color: #f08080;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
:deep(.alert-banner strong) { color: white; }

/* ─── DASHBOARD STATS ─── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1px;
    background: var(--border);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    margin: 32px 0;
}
.stat-card {
    background: var(--surface);
    padding: 20px 18px;
    position: relative;
}
.stat-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
}
.stat-card.danger::after { background: var(--red); }
.stat-card.warning::after { background: var(--orange); }
.stat-card.ok::after { background: var(--green); }
.stat-label {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 9px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--text-dim);
    margin-bottom: 6px;
}
.stat-value {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 32px;
    line-height: 1;
    margin-bottom: 4px;
}
.stat-card.danger .stat-value { color: var(--red); }
.stat-card.warning .stat-value { color: var(--orange); }
.stat-card.ok .stat-value { color: var(--green); }
.stat-delta {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10px;
    color: var(--text-dim);
}
:deep(.stat-delta strong) { color: var(--text-bright); }

/* ─── SECTION ─── */
.section {
    margin: 56px 0;
}
.section-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 28px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}
.section-num {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 48px;
    color: var(--border);
    line-height: 1;
    flex-shrink: 0;
}
.section-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 28px;
    color: var(--text-bright);
    letter-spacing: 1px;
    line-height: 1.1;
}
.section-badge {
    margin-left: auto;
    flex-shrink: 0;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 9px;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 2px;
}
.badge-danger { background: rgba(231,76,60,0.2); color: #ff8080; border: 1px solid rgba(231,76,60,0.4); }
.badge-intel { background: rgba(232,160,32,0.15); color: var(--accent); border: 1px solid rgba(232,160,32,0.3); }
.badge-analysis { background: rgba(39,174,96,0.15); color: #6ee09a; border: 1px solid rgba(39,174,96,0.3); }

/* ─── BODY COPY ─── */
:deep(.prose p) {
    margin-bottom: 16px;
    font-size: 15px;
    line-height: 1.8;
    color: var(--text);
}
:deep(.prose .lead) {
    font-size: 17px;
    font-weight: 300;
    font-style: italic;
    color: var(--text-dim);
    border-left: 3px solid var(--accent);
    padding-left: 16px;
    margin-bottom: 20px;
}

/* ─── TABLES ─── */
:deep(.scenario-table) {
    width: 100%;
    border-collapse: collapse;
    margin: 24px 0;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
}
:deep(.scenario-table th) {
    background: var(--border);
    color: var(--text-dim);
    text-transform: uppercase;
    padding: 12px;
    text-align: left;
}
:deep(.scenario-table td) {
    padding: 12px;
    border-bottom: 1px solid var(--border);
}
:deep(.status-danger) { color: var(--red); font-weight: bold; }
:deep(.status-warn) { color: var(--orange); font-weight: bold; }
:deep(.status-ok) { color: var(--green); font-weight: bold; }

/* ─── QUOTES ─── */
:deep(.quote-block) {
    margin: 32px 0;
    padding: 24px;
    background: var(--surface);
    border-left: 2px solid var(--accent);
    border-radius: 0 8px 8px 0;
}
:deep(.quote-text) {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    font-style: italic;
    color: var(--text-bright);
    margin-bottom: 12px;
}
:deep(.quote-attr) {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    color: var(--text-dim);
    text-transform: uppercase;
}

/* ─── CONCLUSION ─── */
.conclusion {
    background: linear-gradient(135deg, var(--surface2), var(--bg));
    border: 1px solid var(--accent);
    border-radius: 8px;
    padding: 40px;
    margin: 60px 0;
    position: relative;
}
.conclusion-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 24px;
    color: var(--accent);
    text-align: center;
    margin-bottom: 24px;
    letter-spacing: 4px;
}

/* ─── DUAL COLUMN ─── */
.two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin: 24px 0;
}

/* ─── FISCAL STRESS BAR ─── */
.fiscal-bar-wrap {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 24px;
    margin: 24px 0;
}
.fiscal-bar-label {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    color: var(--text-dim);
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 6px;
    display: flex;
    justify-content: space-between;
}
.bar-track {
    height: 20px;
    background: var(--border);
    border-radius: 3px;
    margin-bottom: 14px;
    position: relative;
}
.bar-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 1s ease;
}
.bar-marker {
    position: absolute;
    top: -4px;
    bottom: -4px;
    width: 2px;
    background: var(--red);
}
.bar-marker-label {
    position: absolute;
    top: -22px;
    transform: translateX(-50%);
    font-family: 'IBM Plex Mono', monospace;
    font-size: 9px;
    color: var(--red);
    white-space: nowrap;
}

/* ─── GEOGRAPHIC CARDS & FACT LISTS ─── */
.geo-card {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 24px;
}
.geo-card-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 18px;
    color: var(--accent);
    letter-spacing: 1px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border);
    padding-bottom: 10px;
}
.fact-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
:deep(.fact-list li) {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 13px;
    color: var(--text);
    line-height: 1.5;
}
:deep(.fact-list li::before) {
    content: '—';
    color: var(--accent);
    flex-shrink: 0;
    font-family: 'IBM Plex Mono', monospace;
}

/* ─── TIMELINE ─── */
.timeline {
    position: relative;
    padding-left: 28px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 6px;
    bottom: 6px;
    width: 1px;
    background: var(--border);
}
.tl-item {
    position: relative;
    margin-bottom: 20px;
}
.tl-item::before {
    content: '';
    position: absolute;
    left: -23px;
    top: 6px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--accent);
}
.tl-date {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10px;
    color: var(--accent);
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 3px;
}

/* ─── HIGHLIGHT BOX ─── */
:deep(.highlight) {
    padding: 24px;
    background: rgba(46,204,113,0.05);
    border: 1px solid rgba(46,204,113,0.3);
    border-radius: 8px;
    font-style: italic;
    font-size: 15px;
    color: var(--text-bright);
    margin: 32px 0;
}

/* ─── INTEL ANALYSIS CARDS ─── */
.intel-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
}
.intel-card {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 20px;
}
.intel-card-tag {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 9px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 8px;
}
.intel-card h4 {
    font-family: 'Playfair Display', serif;
    font-size: 16px;
    color: var(--text-bright);
    margin-bottom: 10px;
    line-height: 1.3;
}

/* ─── CHART CONTAINER ─── */
.chart-container {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}
.chart-title {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--text-dim);
    margin-bottom: 16px;
}

/* SEPARATOR */
.sep {
    height: 1px;
    background: linear-gradient(to right, var(--accent), transparent);
    margin: 48px 0;
    opacity: 0.4;
}

/* FOOTER */
.footer {
    border-top: 1px solid var(--border);
    padding: 24px 0;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10px;
    color: var(--text-dim);
    letter-spacing: 1px;
    display: flex;
    justify-content: space-between;
}

/* scroll animation */
.fade-in {
    opacity: 0;
    transform: translateY(16px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.fade-in.visible { opacity: 1; transform: translateY(0); }

@media (max-width: 700px) { .two-col { grid-template-columns: 1fr; } }
</style>
