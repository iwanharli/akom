<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';

const props = defineProps({
    report: Object
});

const activeSection = ref(null);

const getStatusClass = (level) => {
    const map = { 'danger': 'danger', 'warning': 'warning', 'ok': 'ok' };
    return map[level] || 'ok';
};

const getBadgeClass = (badge) => {
    if (!badge) return 'badge-intel';
    const text = badge.toLowerCase();
    if (text.includes('analisa')) return 'badge-analysis';
    if (text.includes('situasi')) return 'badge-danger';
    return 'badge-intel';
};

// ─── DYNAMIC CHART HELPERS ───
const generateLinePath = (payload) => {
    if (!payload?.data_points?.length) return { path: '', points: [], areaPath: '' };
    const data = payload.data_points;
    const yRange = payload.y_range || { min: 40, max: 120 };
    const chartLeft = 60, chartRight = 680, chartTop = 20, chartBottom = 160;
    const chartWidth = chartRight - chartLeft, chartHeight = chartBottom - chartTop;
    const mapX = (i) => chartLeft + (i / (data.length - 1)) * chartWidth;
    const mapY = (val) => chartBottom - ((val - yRange.min) / (yRange.max - yRange.min)) * chartHeight;
    const points = data.map((d, i) => ({ x: mapX(i), y: mapY(d.value), label: d.label, value: d.value }));
    const pathD = points.map((p, i) => `${i === 0 ? 'M' : 'L'}${p.x.toFixed(1)},${p.y.toFixed(1)}`).join(' ');
    const areaD = pathD + ` L${points[points.length - 1].x.toFixed(1)},${chartBottom} L${points[0].x.toFixed(1)},${chartBottom} Z`;
    return { path: pathD, points, areaPath: areaD };
};

const generateYAxis = (payload) => {
    if (!payload?.y_range) return [];
    const { min, max } = payload.y_range;
    const step = payload.y_step || 20;
    const chartTop = 20, chartBottom = 160, chartHeight = chartBottom - chartTop;
    const lines = [];
    for (let val = min; val <= max; val += step) {
        const y = chartBottom - ((val - min) / (max - min)) * chartHeight;
        lines.push({ y, label: `$${val}` });
    }
    return lines;
};

const getAsumsiY = (payload) => {
    if (!payload?.asumsi_line || !payload?.y_range) return null;
    const { min, max } = payload.y_range;
    const chartTop = 20, chartBottom = 160, chartHeight = chartBottom - chartTop;
    return chartBottom - ((payload.asumsi_line.value - min) / (max - min)) * chartHeight;
};

const generateBars = (payload) => {
    if (!payload?.bars?.length) return [];
    const bars = payload.bars, chartBottom = 140, maxHeight = 134, barWidth = 70, startX = 80, gap = 40;
    return bars.map((bar, i) => {
        const height = (bar.height_pct / 100) * maxHeight;
        const x = startX + i * (barWidth + gap), y = chartBottom - height;
        return { ...bar, x, y, width: barWidth, height, textY: y - 4, labelY: chartBottom + 8, subLabelY: chartBottom + 18, textInside: bar.height_pct >= 90 };
    });
};

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                if (entry.target.id && entry.target.id.startsWith('sec-')) {
                    activeSection.value = entry.target.id;
                }
            }
        });
    }, { threshold: 0.3, rootMargin: '0px 0px -20% 0px' });

    document.querySelectorAll('.fade-in, section[id^="sec-"], #conclusion').forEach(el => observer.observe(el));
});

const scrollTo = (id) => {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
};
</script>

<template>
    <Head :title="'INTEL BRIEF: ' + report.title" />

    <div class="report-wrapper">
        <!-- ══════════════════ HUD ELEMENTS ══════════════════ -->
        <div class="hud-scanline"></div>
        <div class="hud-vignette"></div>
        <div class="hud-corner top-left"></div>
        <div class="hud-corner top-right"></div>
        
        <div class="scroll-progress-bar"></div>

        <!-- ══════════════════ COVER PAGE ══════════════════ -->
        <header class="cover-hero">
            <div class="cover-bg-mesh"></div>
            <div class="cover-content">
                <div class="confidential-seal">
                    <div class="seal-text">TOP SECRET // AKOM INTELLIGENCE</div>
                </div>
                
                <div class="brief-meta hero-meta fade-in">
                    <span class="brief-id">REF: #{{ report.id }}-{{ new Date(report.report_date).getTime().toString(16).slice(-4).toUpperCase() }}</span>
                    <span class="sep">|</span>
                    <span class="brief-cat">{{ report.classification }} Briefing</span>
                </div>

                <h1 class="brief-title fade-in" v-html="report.title.replace(/<em>/g, '<span class=\'gold-text\'>').replace(/<\/em>/g, '</span>')"></h1>
                <p class="brief-lead fade-in">{{ report.subtitle }}</p>

                <div class="hero-stats fade-in">
                    <div class="h-stat">
                        <label>ISSUED ON</label>
                        <value>{{ new Date(report.report_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</value>
                    </div>
                    <div class="h-stat" v-if="report.brent_oil_price">
                        <label>BRENT PRICE</label>
                        <value>${{ report.brent_oil_price }}/bbl</value>
                    </div>
                    <div class="h-stat" v-if="report.usd_idr_rate">
                        <label>USD/IDR</label>
                        <value>Rp{{ Number(report.usd_idr_rate).toLocaleString('id-ID') }}</value>
                    </div>
                </div>
            </div>
            <div class="scroll-cue">EXPLORE ANALYSIS <div class="arrow">↓</div></div>
        </header>

        <div class="brief-body container">
            <!-- ══════════════════ ASYMMETRIC GRID ══════════════════ -->
            <div class="brief-grid">
                
                <!-- MAIN COLUMN -->
                <main class="brief-content">
                    
                    <!-- ALERT -->
                    <div v-if="report.alert_text" class="critical-alert fade-in">
                        <div class="alert-icon">!</div>
                        <div class="alert-msg" v-html="report.alert_text"></div>
                    </div>

                    <!-- EXECUTIVE SUMMARY STATS -->
                    <div v-if="report.stats?.length" class="executive-stats fade-in">
                        <div v-for="stat in report.stats" :key="stat.id" 
                            :class="['stat-hub-box', getStatusClass(stat.status_level)]">
                            <div class="sh-label">{{ stat.label }}</div>
                            <div class="sh-value">{{ stat.value }}</div>
                            <div class="sh-delta" v-html="stat.delta_text"></div>
                        </div>
                    </div>

                    <!-- REPORT SECTIONS -->
                    <template v-for="(section, sIdx) in report.sections" :key="section.id">
                        <section :id="'sec-' + sIdx" class="report-section fade-in">
                            <div class="section-marker">
                                <span class="num">{{ String(sIdx + 1).padStart(2, '0') }}</span>
                                <span class="line"></span>
                                <span class="label" v-if="section.badge_text">{{ section.badge_text }}</span>
                            </div>

                            <h2 class="section-heading" v-html="section.title"></h2>
                            
                            <div class="analysis-prose" v-html="section.content_html"></div>

                            <!-- NESTED MODULES -->
                            <div v-if="section.analysis_items?.length" class="analysis-modules">
                                <!-- Intel Cards -->
                                <div class="intel-brief-grid" v-if="section.analysis_items.some(i => i.item_type === 'intel_card')">
                                    <div v-for="item in section.analysis_items.filter(i => i.item_type === 'intel_card')" :key="item.id" class="intel-module">
                                        <div class="im-header">
                                            <span class="im-tag">{{ item.tag }}</span>
                                            <span class="im-accent"></span>
                                        </div>
                                        <h4>{{ item.heading }}</h4>
                                        <div class="im-body" v-html="item.body"></div>
                                    </div>
                                </div>

                                <!-- Timeline/Facts Dual Row -->
                                <div class="geo-dual-row mt-10">
                                    <!-- Timeline -->
                                    <div class="module-card timeline-card" v-if="section.analysis_items.some(i => i.item_type === 'timeline')">
                                        <h5 class="module-title">ESCALATION TIMELINE</h5>
                                        <div class="brief-timeline">
                                            <div v-for="item in section.analysis_items.filter(i => i.item_type === 'timeline')" :key="item.id" class="btl-item">
                                                <div class="btl-date">{{ item.event_date }}</div>
                                                <div class="btl-content"><strong>{{ item.heading }}:</strong> {{ item.body }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Fact List -->
                                    <div class="module-card fact-card" v-if="section.analysis_items.some(i => i.item_type === 'fact_list')">
                                        <h5 class="module-title">DATA ANALYTICS</h5>
                                        <ul class="brief-facts">
                                            <li v-for="item in section.analysis_items.filter(i => i.item_type === 'fact_list')" :key="item.id" v-html="item.body"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- VISUAL DATA MODULES -->
                            <div class="visualization-zone" v-if="section.charts?.length">
                                <template v-for="chart in section.charts" :key="chart.id">
                                    <!-- Line Chart -->
                                    <div v-if="chart.chart_type === 'line_oil'" class="premium-chart-box">
                                        <div class="chart-header">
                                            <span class="chart-type-tag">LINE ANALYTICS</span>
                                            <h6>{{ chart.json_payload?.title || 'OIL PRICE VS ASSUMPTION' }}</h6>
                                        </div>
                                        <div class="svg-canvas pt-4">
                                            <svg viewBox="0 0 700 180" xmlns="http://www.w3.org/2000/svg">
                                                <template v-for="(line, idx) in generateYAxis(chart.json_payload)" :key="'y-'+idx">
                                                    <line :x1="60" :y1="line.y" :x2="680" :y2="line.y" stroke="rgba(255,255,255,0.05)" stroke-width="1"></line>
                                                    <text :x="50" :y="line.y + 4" fill="rgba(255,255,255,0.3)" font-size="9" text-anchor="end">{{ line.label }}</text>
                                                </template>
                                                <path v-if="generateLinePath(chart.json_payload).areaPath" :d="generateLinePath(chart.json_payload).areaPath" fill="url(#chartGrad)"></path>
                                                <path v-if="generateLinePath(chart.json_payload).path" :d="generateLinePath(chart.json_payload).path" fill="none" stroke="var(--danger)" stroke-width="2.5" stroke-linecap="round"></path>
                                                <defs>
                                                    <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="0%" stop-color="var(--danger)" stop-opacity="0.25"></stop>
                                                        <stop offset="100%" stop-color="var(--danger)" stop-opacity="0"></stop>
                                                    </linearGradient>
                                                </defs>
                                                <template v-for="(pt, idx) in generateLinePath(chart.json_payload).points" :key="'p-'+idx">
                                                    <circle :cx="pt.x" :cy="pt.y" r="4" fill="var(--paper)" stroke="var(--danger)" stroke-width="2"></circle>
                                                    <text :x="pt.x" y="175" fill="rgba(255,255,255,0.4)" font-size="8" text-anchor="middle">{{ pt.label }}</text>
                                                </template>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Fiscal Stress -->
                                    <div v-if="chart.chart_type === 'fiscal_stress'" class="premium-chart-box">
                                        <div class="chart-header">
                                            <span class="chart-type-tag">FISCAL DYNAMICS</span>
                                            <h6>{{ chart.json_payload?.title || 'APBN STRESS SCENARIOS' }}</h6>
                                        </div>
                                        <div class="fiscal-metrics mt-6">
                                            <div v-for="(scenario, idx) in (chart.json_payload?.scenarios || chart.json_payload)" :key="idx" class="metric-row">
                                                <div class="m-label"><span>{{ scenario.name }}</span> <strong>{{ scenario.label }}</strong></div>
                                                <div class="m-track">
                                                    <div class="m-fill" :style="{ width: scenario.width, background: scenario.gradient }"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MBG Evolution -->
                                    <div v-if="chart.chart_type === 'mbg_evolution'" class="premium-chart-box">
                                        <div class="chart-header">
                                            <span class="chart-type-tag">EVOLUTION MODULE</span>
                                            <h6>{{ chart.json_payload?.title || 'MBG BUDGET ESCALATION' }}</h6>
                                        </div>
                                        <div class="svg-canvas pt-4">
                                            <svg viewBox="0 0 700 160" xmlns="http://www.w3.org/2000/svg">
                                                <template v-for="(bar, idx) in generateBars(chart.json_payload)" :key="'bar-'+idx">
                                                    <rect :x="bar.x" :y="bar.y" :width="bar.width" :height="bar.height" :fill="bar.color" :opacity="bar.opacity" rx="4"></rect>
                                                    <text v-if="bar.textInside" :x="bar.x + bar.width / 2" :y="bar.y + 14" fill="#000" font-size="11" text-anchor="middle" font-weight="800">{{ bar.value_label }}</text>
                                                    <text v-else :x="bar.x + bar.width / 2" :y="bar.textY" :fill="bar.color" font-size="10" text-anchor="middle" font-weight="700">{{ bar.value_label }}</text>
                                                    <text :x="bar.x + bar.width / 2" :y="bar.labelY + 8" :fill="bar.height_pct >= 90 ? bar.color : '#7a8090'" font-size="9" text-anchor="middle" font-weight="700">{{ bar.label }}</text>
                                                </template>
                                                <line x1="60" y1="140" x2="680" y2="140" stroke="rgba(255,255,255,0.1)" stroke-width="1"></line>
                                            </svg>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </template>

                    <!-- CONCLUSION -->
                    <section id="conclusion" class="final-conclusion fade-in" v-if="report.conclusion_html">
                        <div class="conc-header">
                            <span class="star">✦</span>
                            <h3>FINAL INTELLIGENCE SYNOPSIS</h3>
                            <span class="star">✦</span>
                        </div>
                        <div class="analysis-prose styled-conc" v-html="report.conclusion_html"></div>
                        <div class="seal-watermark">CONFIDENTIAL</div>
                    </section>
                </main>

                <!-- SIDEBAR COLUMN -->
                <aside class="brief-sidebar">
                    <div class="sticky-sidebar">
                        
                        <!-- Brief ID Block -->
                        <div class="sb-block info-block">
                            <div class="lbl">ANALYST IN CHARGE</div>
                            <div class="val">{{ report.user?.name || 'Senior Intel Analyst' }}</div>
                            <div class="lbl mt-6">BRIEFING REFERENCE</div>
                            <div class="val uuid">ID-AK-{{ report.uuid.split('-')[0].toUpperCase() }}</div>
                        </div>

                        <!-- NAVIGATION -->
                        <div class="sb-block toc-block">
                            <div class="lbl">STRUCTURE</div>
                            <nav class="toc-nav">
                                <button v-for="(section, idx) in report.sections" :key="section.id"
                                    @click="scrollTo('sec-' + idx)"
                                    :class="['toc-link', activeSection === 'sec-' + idx && 'is-active']">
                                    <span class="idx">{{ String(idx + 1).padStart(2, '0') }}</span>
                                    <span class="txt text-truncate">{{ section.badge_text || 'SECTION ' + (idx+1) }}</span>
                                </button>
                                <button v-if="report.conclusion_html" 
                                    @click="scrollTo('conclusion')"
                                    :class="['toc-link', activeSection === 'conclusion' && 'is-active']">
                                    <span class="idx">✦</span>
                                    <span class="txt">SYNOPSIS</span>
                                </button>
                            </nav>
                        </div>

                        <!-- META HUD -->
                        <div class="sb-block hud-block">
                            <div class="hud-mini-grid">
                                <div class="g-item"><span>TYPE:</span> <strong>STRAT-ECON</strong></div>
                                <div class="g-item"><span>LVL:</span> <strong>{{ report.classification }}</strong></div>
                                <div class="g-item"><span>SYS:</span> <strong>AKOM-v2</strong></div>
                                <div class="g-item"><span>SIG:</span> <strong>ENC-RSA</strong></div>
                            </div>
                            <div class="seal-logo">AKOM</div>
                        </div>

                        <div class="sidebar-footer">
                            FOR AUTHORIZED PERSONNEL ONLY
                        </div>
                    </div>
                </aside>

            </div>
        </div>

        <footer class="brief-footer">
            <div class="footer-inner">
                <div class="f-left">AKOM INTELLIGENCE REPORTING SYSTEM</div>
                <div class="f-center">{{ report.classification }} DOCUMENT</div>
                <div class="f-right">© {{ new Date().getFullYear() }} INDONESIA ECONOMIC BRIEF</div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* ─── PREMIUM DARK THEME (TACTICAL-ELEGANCE) ─── */
.report-wrapper {
    --bg: #050608;
    --paper: #0b0d11;
    --elevated: #111419;
    --border: #21262d;
    --accent: #c9a227;
    --accent-glow: rgba(201, 162, 39, 0.2);
    --text-pri: #e6edf3;
    --text-sec: #8b949e;
    --text-mut: #484f58;
    --danger: #f85149;
    --warn: #d29922;
    --ok: #238636;

    background: var(--bg);
    color: var(--text-pri);
    min-height: 100vh;
    font-family: 'Inter', system-ui, sans-serif;
    letter-spacing: -0.01em;
    position: relative;
    overflow-x: hidden;
}

/* ─── HUD OVERLAYS ─── */
.hud-scanline {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%);
    background-size: 100% 4px;
    pointer-events: none; z-index: 999; opacity: 0.1;
}
.hud-vignette {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at center, transparent 30%, rgba(0,0,0,0.4) 100%);
    pointer-events: none; z-index: 1000;
}
.hud-corner {
    position: fixed; width: 40px; height: 40px; border: 1px solid var(--border);
    pointer-events: none; z-index: 1001; opacity: 0.3;
}
.top-left { top: 30px; left: 30px; border-right: none; border-bottom: none; }
.top-right { top: 30px; right: 30px; border-left: none; border-bottom: none; }

/* ─── COVER HERO ─── */
.cover-hero {
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: #050608;
    border-bottom: 1px solid var(--border);
    position: relative;
    padding: 0 40px;
    text-align: center;
    overflow: hidden;
}
.cover-bg-mesh {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background-image: radial-gradient(var(--border) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(circle at center, black 0%, transparent 80%);
    opacity: 0.2;
}
.confidential-seal {
    position: absolute; top: 60px;
    padding: 10px 20px; border: 2px solid var(--danger);
    color: var(--danger); font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 800;
    letter-spacing: 4px; text-transform: uppercase; border-radius: 2px;
}
.brief-meta {
    font-family: 'IBM Plex Mono', monospace; font-size: 12px; color: var(--accent);
    letter-spacing: 3px; text-transform: uppercase; margin-bottom: 24px;
}
.hero-meta { opacity: 0; transform: translateY(20px); }
.brief-meta .sep { color: var(--text-mut); margin: 0 15px; }
.brief-title {
    font-family: 'Bebas Neue', sans-serif; font-size: clamp(56px, 12vw, 120px);
    line-height: 0.85; margin-bottom: 32px; letter-spacing: -1px;
    color: #fff;
}
.gold-text { color: var(--accent); }
.brief-lead {
    font-family: 'Source Serif 4', serif; font-size: 20px; color: var(--text-sec);
    max-width: 850px; line-height: 1.6; font-style: italic; margin-bottom: 80px;
}
.hero-stats {
    display: flex; gap: 60px; border: 1px solid var(--border); background: rgba(255,255,255,0.01);
    padding: 30px 60px; border-radius: 4px; backdrop-filter: blur(10px);
}
.h-stat { display: flex; flex-direction: column; gap: 10px; text-align: left; }
.h-stat label { font-size: 10px; color: var(--text-mut); font-weight: 800; letter-spacing: 2px; }
.h-stat value { font-family: 'IBM Plex Mono', monospace; font-size: 18px; color: var(--text-pri); font-weight: 600; }

.scroll-cue {
    position: absolute; bottom: 60px; font-family: 'IBM Plex Mono', monospace; font-size: 10px;
    color: var(--text-mut); letter-spacing: 3px;
}

/* ─── MAIN LAYOUT ─── */
.container { max-width: 1400px; margin: 0 auto; padding: 0 60px; }
.brief-body { margin-top: 100px; }
.brief-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 300px;
    gap: 80px;
    align-items: start;
}

/* ─── CONTENT COLUMN ─── */
.brief-content { padding-bottom: 150px; }

.critical-alert {
    background: rgba(248, 81, 73, 0.05); border: 1px solid rgba(248, 81, 73, 0.3);
    border-left: 6px solid var(--danger); padding: 24px 32px; border-radius: 4px;
    display: flex; gap: 24px; align-items: center; margin-bottom: 60px;
}
.alert-icon { font-weight: 900; color: var(--danger); font-size: 28px; }
.alert-msg { font-size: 14px; color: var(--text-pri); line-height: 1.6; font-family: 'IBM Plex Mono', monospace; }

.executive-stats {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px; margin-bottom: 80px;
}
.stat-hub-box {
    background: var(--elevated); border: 1px solid var(--border); border-radius: 4px;
    padding: 30px; position: relative; transition: all 0.4s cubic-bezier(0.19, 1, 0.22, 1);
}
.stat-hub-box:hover { transform: translateY(-4px); border-color: var(--accent); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
.sh-label { font-size: 10px; color: var(--text-sec); font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; }
.sh-value { font-family: 'Bebas Neue', sans-serif; font-size: 40px; line-height: 1; margin-bottom: 10px; }
.stat-hub-box.danger .sh-value { color: var(--danger); }
.stat-hub-box.warning .sh-value { color: var(--warn); }
.stat-hub-box.ok .sh-value { color: var(--ok); }
.sh-delta { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--text-mut); }

/* ─── SECTIONS ─── */
.report-section { margin-bottom: 120px; position: relative; }
.section-marker { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; }
.section-marker .num { font-family: 'Bebas Neue', sans-serif; font-size: 32px; color: var(--accent); }
.section-marker .line { flex-grow: 1; height: 1px; background: var(--border); }
.section-marker .label { font-family: 'IBM Plex Mono', monospace; font-size: 10px; color: var(--text-mut); text-transform: uppercase; letter-spacing: 4px; }

.section-heading { font-family: 'Bebas Neue', sans-serif; font-size: 56px; line-height: 1; margin-bottom: 40px; color: #fff; letter-spacing: 1px; }

.analysis-prose {
    font-family: 'Source Serif 4', serif; font-size: 18px; line-height: 1.9; color: #ced4da;
    max-width: 850px;
}
:deep(.analysis-prose p) { margin-bottom: 32px; text-align: justify; }
:deep(.analysis-prose strong) { color: #fff; font-weight: 600; }

.intel-brief-grid { display: grid; gap: 24px; margin-top: 50px; }
.intel-module { background: #0d1117; border: 1px solid var(--border); padding: 30px; border-radius: 4px; position: relative; }
.intel-module::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--accent); opacity: 0.5; }
.im-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.im-tag { font-family: 'IBM Plex Mono', monospace; font-size: 10px; color: var(--accent); letter-spacing: 3px; font-weight: 800; }
.intel-module h4 { font-size: 20px; font-weight: 700; margin-bottom: 15px; color: #fff; }
.im-body { font-size: 15px; color: var(--text-sec); line-height: 1.7; }

.module-card { background: rgba(255,255,255,0.01); border: 1px solid var(--border); border-radius: 4px; padding: 30px; }
.module-title { font-size: 11px; font-weight: 800; color: var(--text-mut); letter-spacing: 3px; margin-bottom: 24px; text-transform: uppercase; }

.btl-item { margin-bottom: 20px; padding-left: 20px; border-left: 1px solid var(--border); position: relative; }
.btl-item::before { content: ''; position: absolute; left: -3px; top: 6px; width: 5px; height: 5px; background: var(--accent); border-radius: 50%; }
.btl-date { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--accent); font-weight: 700; margin-bottom: 6px; }
.btl-content { font-size: 14px; color: var(--text-sec); line-height: 1.5; }

.brief-facts { list-style: none; display: flex; flex-direction: column; gap: 15px; }
.brief-facts li { font-size: 15px; color: var(--text-sec); padding-left: 20px; position: relative; line-height: 1.5; }
.brief-facts li::before { content: '+'; position: absolute; left: 0; color: var(--accent); font-weight: 900; }

.premium-chart-box { background: #050608; border: 1px solid var(--border); border-radius: 4px; padding: 40px; margin-top: 60px; }
.chart-header { display: flex; flex-direction: column; gap: 10px; margin-bottom: 30px; }
.chart-type-tag { font-family: 'IBM Plex Mono', monospace; font-size: 10px; color: var(--text-mut); font-weight: 800; letter-spacing: 4px; }
.chart-header h6 { font-size: 16px; font-weight: 700; text-transform: uppercase; color: #fff; }

.fiscal-metrics { display: flex; flex-direction: column; gap: 20px; }
.metric-row { display: flex; flex-direction: column; gap: 10px; }
.m-label { display: flex; justify-content: space-between; font-size: 12px; font-family: 'IBM Plex Mono', monospace; }
.m-label span { color: var(--text-sec); text-transform: uppercase; letter-spacing: 1px; }
.m-label strong { color: #fff; }
.m-track { height: 16px; background: rgba(255,255,255,0.03); border-radius: 2px; }
.m-fill { height: 100%; border-radius: 2px; }

/* ─── SIDEBAR ─── */
.brief-sidebar { position: relative; }
.sticky-sidebar { position: sticky; top: 60px; display: flex; flex-direction: column; gap: 40px; }

.sb-block { border-top: 1px solid var(--border); padding-top: 24px; }
.sb-block .lbl { font-size: 11px; font-weight: 800; color: var(--text-mut); letter-spacing: 3px; margin-bottom: 15px; }
.sb-block .val { font-family: 'IBM Plex Mono', monospace; font-size: 14px; color: var(--text-pri); }
.sb-block .val.uuid { font-size: 12px; color: var(--text-mut); }

.toc-nav { display: flex; flex-direction: column; gap: 8px; }
.toc-link {
    display: flex; align-items: center; gap: 15px; padding: 12px 15px; border-radius: 2px;
    background: transparent; border: none; text-align: left; cursor: pointer; transition: all 0.3s;
}
.toc-link:hover { background: rgba(255,255,255,0.02); }
.toc-link.is-active { background: rgba(201, 162, 39, 0.08); border-right: 2px solid var(--accent); }
.toc-link .idx { font-family: 'Bebas Neue', sans-serif; font-size: 20px; color: var(--text-mut); transition: all 0.3s; }
.toc-link.is-active .idx { color: var(--accent); }
.toc-link .txt { font-size: 11px; font-weight: 700; color: var(--text-sec); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0; }
.toc-link.is-active .txt { color: #fff; }

.hud-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; border-bottom: 1px solid var(--border); padding-bottom: 20px; margin-bottom: 20px; }
.g-item { font-family: 'IBM Plex Mono', monospace; font-size: 10px; line-height: 1.5; color: var(--text-mut); }
.g-item strong { color: var(--text-sec); }
.seal-logo {
    font-family: 'Bebas Neue', sans-serif; font-size: 40px; letter-spacing: 12px;
    color: var(--border); text-align: center; border: 1px solid var(--border); padding: 15px;
}
.sidebar-footer { font-family: 'IBM Plex Mono', monospace; font-size: 9px; color: var(--danger); text-align: center; margin-top: 20px; opacity: 0.6; }

/* ─── CONCLUSION ─── */
.final-conclusion {
    background: #0d1117; border: 1px solid var(--accent); border-radius: 4px; padding: 80px; margin-top: 150px;
    position: relative; overflow: hidden;
}
.conc-header { display: flex; align-items: center; justify-content: center; gap: 30px; margin-bottom: 50px; }
.conc-header h3 { font-family: 'Bebas Neue', sans-serif; font-size: 32px; letter-spacing: 6px; color: var(--accent); }
.conc-header .star { color: var(--accent); font-size: 24px; }
.styled-conc { max-width: 950px; margin: 0 auto; }
.seal-watermark {
    position: absolute; bottom: -20px; right: -20px; font-family: 'Bebas Neue', sans-serif;
    font-size: 120px; color: var(--border); opacity: 0.1; transform: rotate(-15deg);
    user-select: none; pointer-events: none;
}

/* ─── FOOTER ─── */
.brief-footer { border-top: 1px solid var(--border); padding: 60px 0; margin-top: 100px; }
.footer-inner {
    max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between;
    font-family: 'IBM Plex Mono', monospace; font-size: 10px; color: var(--text-mut); letter-spacing: 2px;
}

/* ─── ANIMATIONS ─── */
.fade-in { opacity: 0; transform: translateY(30px); transition: all 1s cubic-bezier(0.2, 0.8, 0.2, 1); }
.fade-in.visible { opacity: 1; transform: translateY(0); }

@media (max-width: 1200px) {
    .brief-grid { grid-template-columns: 1fr; }
    .brief-sidebar { display: none; }
    .container { padding: 0 30px; }
    .hero-stats { flex-direction: column; gap: 30px; padding: 40px; }
    .brief-title { font-size: 60px; }
}

.text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.mt-10 { margin-top: 40px; }
.geo-dual-row { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
</style>
