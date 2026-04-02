<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="AKOM Intelligence - Secure Portal" />

    <div class="landing-shell">
        <!-- Ambient Background Effects -->
        <div class="ambient-glow glow-1"></div>
        <div class="ambient-glow glow-2"></div>

        <!-- Navigation -->
        <nav class="top-nav">
            <div class="nav-container">
                <div class="brand">
                    <div class="brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                            <path d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span>AKOM <span class="text-accent">INTEL</span></span>
                </div>

                <div class="nav-links" v-if="canLogin && $page.props.auth.user">
                    <Link
                        :href="route('dashboard')"
                        class="btn-primary"
                    >
                        Enter Dashboard
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Main Hero Section -->
        <main class="hero-section">
            <div class="hero-content">
                <div class="badge-pill">
                    <span class="pulse-dot"></span>
                    Classified Economic Briefings
                </div>
                
                <h1 class="hero-title">
                    Strategic Intelligence<br/>
                    <span class="text-gradient">Reports.</span>
                </h1>
                
                <p class="hero-subtitle">
                    Laporan intelijen ekonomi eksklusif yang menyajikan sintesis mendalam mengenai pergeseran makroekonomi kritis, tren pasar komoditas, dan pemetaan risiko geopolitik bagi para pengambil keputusan strategis.
                </p>

                <div class="cta-group flex-center gap-4">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="btn-primary lg">
                        Go to Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('client.register')" class="btn-primary lg outline">
                            Register Client
                        </Link>
                        <button class="btn-primary lg outline alt" onclick="alert('Feature coming soon')">
                            Request Demo
                        </button>
                    </template>
                </div>
            </div>

            <!-- CSS Abstract Mockup of the Report -->
            <div class="report-mockup">
                <div class="mockup-header">
                    <div class="mockup-dots"><span></span><span></span><span></span></div>
                    <div class="mockup-title">AKOM_INTEL_BRIEF_Q3_DRAFT.PDF</div>
                </div>
                <div class="mockup-body">
                    <div class="mockup-top">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-text full mt-2"></div>
                        <div class="skeleton-text wide"></div>
                    </div>
                    <div class="mockup-grid">
                        <div class="mockup-chart">
                            <div class="chart-bar" style="height: 40%"></div>
                            <div class="chart-bar" style="height: 65%"></div>
                            <div class="chart-bar text-accent" style="height: 35%; background: var(--accent); opacity: 0.8;"></div>
                            <div class="chart-bar" style="height: 85%"></div>
                            <div class="chart-bar" style="height: 55%"></div>
                        </div>
                        <div class="mockup-side">
                            <div class="skeleton-box"></div>
                            <div class="skeleton-box"></div>
                            <div class="skeleton-text full mt-4"></div>
                            <div class="skeleton-text mid"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Explanation Grid -->
            <div class="details-grid">
                <div class="detail-card">
                    <div class="detail-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                    </div>
                    <h3>Analisis Makroekonomi</h3>
                    <p>Mendedah pergeseran kebijakan moneter, laju inflasi, dan stabilitas nilai tukar Rupiah terhadap kondisi global yang fluktuatif.</p>
                </div>
                
                <div class="detail-card">
                    <div class="detail-icon accent">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                    </div>
                    <h3>Intelijen Komoditas</h3>
                    <p>Memetakan anomali suplai, prediksi pergerakan harga Brent/ICP, dan dampaknya terhadap rantai pasok industri prioritas nasional.</p>
                </div>

                <div class="detail-card">
                    <div class="detail-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                    </div>
                    <h3>Risiko Geopolitik</h3>
                    <p>Proyeksi simulasi atas eskalasi konflik internasional, embargo energi, maupun kebijakan proteksionisme berisiko tinggi.</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bottom-footer">
            <p>&copy; 2026 Intelligence Directorate. All rights reserved.</p>
        </footer>
    </div>
</template>

<style scoped>
/* ─── BASE TOKENS ─── */
.landing-shell {
    --bg-primary: #05070a;
    --bg-secondary: #0d1117;
    --bg-elevated: #161b22;
    --border: #21283b;
    --accent: #c9a227;
    --text-primary: #e6edf3;
    --text-secondary: #8b949e;
    --text-muted: #484f58;
    
    background-color: var(--bg-primary);
    color: var(--text-primary);
    height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    position: relative;
    overflow-y: scroll;
    /* Hide scrollbars */
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
    display: flex;
    flex-direction: column;
}

.landing-shell::-webkit-scrollbar {
    display: none; /* Chrome, Safari and Opera */
}

.text-accent { color: var(--accent); }

/* ─── AMBIENT BACKGROUND GLOWS ─── */
.ambient-glow {
    position: absolute;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    filter: blur(140px);
    opacity: 0.15;
    z-index: 0;
    pointer-events: none;
}
.glow-1 {
    background: var(--accent);
    top: -200px;
    right: -100px;
}
.glow-2 {
    background: #1d4ed8; /* Deep blue offset */
    bottom: -300px;
    left: -200px;
}

/* ─── NAVIGATION ─── */
.top-nav {
    position: relative;
    z-index: 10;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    backdrop-filter: blur(20px);
    background: rgba(5, 7, 10, 0.7);
}
.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.brand {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 2px;
}
.brand-icon {
    width: 32px; height: 32px;
    background: linear-gradient(135deg, var(--accent), #8b6914);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.3);
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--accent), #8b6914);
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.2);
    border: 1px solid rgba(255,255,255,0.15);
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(201, 162, 39, 0.3);
    filter: brightness(1.1);
}
.btn-primary.lg {
    padding: 16px 32px;
    font-size: 15px;
    border-radius: 12px;
}

.cta-group.flex-center.gap-4 {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}
.btn-primary.outline {
    background: transparent;
    color: var(--accent);
    box-shadow: none;
    border: 1px solid var(--accent);
}
.btn-primary.outline:hover {
    background: rgba(201, 162, 39, 0.1);
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.2);
}
.btn-primary.outline.alt {
    color: var(--text-primary);
    border: 1px solid rgba(255,255,255,0.2);
}
.btn-primary.outline.alt:hover {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.4);
    box-shadow: 0 4px 12px rgba(255,255,255,0.1);
}

/* ─── HERO SECTION ─── */
.hero-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 10;
    padding: 80px 24px;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}
.hero-content {
    text-align: center;
    max-width: 800px;
    margin-bottom: 80px;
    animation: fadeUp 1s ease-out;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-secondary);
    margin-bottom: 24px;
}
.pulse-dot {
    width: 6px; height: 6px;
    background: #3fb950;
    border-radius: 50%;
    box-shadow: 0 0 8px #3fb950;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.5); }
    100% { opacity: 1; transform: scale(1); }
}

.hero-title {
    font-size: clamp(40px, 6vw, 64px);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -2px;
    margin-bottom: 24px;
}
.text-gradient {
    background: linear-gradient(135deg, var(--text-primary) 0%, var(--accent) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-subtitle {
    font-size: clamp(16px, 2vw, 18px);
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 40px;
    max-width: 600px;
    margin-inline: auto;
}

/* ─── REPORT MOCKUP ─── */
.report-mockup {
    width: 100%;
    max-width: 800px;
    margin: 40px auto;
    background: rgba(13, 17, 23, 0.6);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.05);
    overflow: hidden;
    animation: fadeUp 1s ease-out 0.2s both;
}
.mockup-header {
    background: rgba(22, 27, 34, 0.8);
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.mockup-dots {
    display: flex;
    gap: 6px;
}
.mockup-dots span {
    width: 10px; height: 10px; border-radius: 50%;
    background: #484f58;
}
.mockup-dots span:nth-child(1) { background: #f85149; }
.mockup-dots span:nth-child(2) { background: #d29922; }
.mockup-dots span:nth-child(3) { background: #3fb950; }

.mockup-title {
    font-family: monospace;
    font-size: 11px;
    color: var(--text-muted);
    letter-spacing: 0.5px;
}

.mockup-body {
    padding: 24px;
}
.skeleton-title {
    height: 18px; width: 60%;
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
}
.skeleton-text {
    height: 10px;
    background: rgba(255,255,255,0.05);
    border-radius: 4px;
    margin-top: 8px;
}
.skeleton-text.full { width: 100%; }
.skeleton-text.wide { width: 85%; }
.skeleton-text.mid { width: 60%; }

.mockup-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.05);
}
.mockup-chart {
    height: 120px;
    display: flex;
    align-items: flex-end;
    gap: 12px;
    background: rgba(255,255,255,0.02);
    padding: 16px;
    border-radius: 8px;
    border: 1px dashed rgba(255,255,255,0.05);
}
.chart-bar {
    flex: 1;
    background: rgba(255,255,255,0.1);
    border-radius: 4px 4px 0 0;
    transition: all 1s ease;
}
.mockup-side {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.skeleton-box {
    height: 32px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 6px;
}

/* ─── DETAILS GRID ─── */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    width: 100%;
    max-width: 1000px;
    margin: 40px auto 0;
    animation: fadeUp 1s ease-out 0.4s both;
}
.detail-card {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    padding: 24px;
    transition: all 0.2s;
}
.detail-card:hover {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.1);
    transform: translateY(-2px);
}
.detail-icon {
    width: 40px; height: 40px;
    border-radius: 8px;
    background: rgba(255,255,255,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    margin-bottom: 16px;
}
.detail-icon svg { width: 20px; height: 20px; }
.detail-icon.accent {
    background: rgba(201, 162, 39, 0.1);
    color: var(--accent);
}
.detail-card h3 {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
}
.detail-card p {
    font-size: 13px;
    color: var(--text-secondary);
    line-height: 1.6;
}
/* ─── FOOTER ─── */
.bottom-footer {
    border-top: 1px solid rgba(255,255,255,0.05);
    padding: 32px 24px;
    text-align: center;
    color: var(--text-muted);
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 1px;
    text-transform: uppercase;
    position: relative;
    z-index: 10;
}
</style>
