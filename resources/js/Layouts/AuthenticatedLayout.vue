<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const showProfileMenu = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const userInitial = computed(() => user.value?.name?.charAt(0)?.toUpperCase() || '?');
const roleBadge = computed(() => user.value?.role?.name || 'user');
</script>

<template>
    <div class="admin-shell">
        <!-- ═══ TOP NAV ═══ -->
        <nav class="admin-nav">
            <div class="nav-inner">
                <div class="nav-left">
                    <Link :href="route('dashboard')" class="nav-brand">
                        <div class="brand-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                                <path d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="brand-text">AKOM</span>
                        <span class="brand-sub">Intel System</span>
                    </Link>

                    <div class="nav-links">
                        <Link :href="route('dashboard')" :class="['nav-link', route().current('dashboard') && 'active']">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="nav-link-icon"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                            Dashboard
                        </Link>
                        <Link :href="route('reports.index')" :class="['nav-link', route().current('reports.*') && 'active']">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="nav-link-icon"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" /></svg>
                            Reports
                        </Link>
                        <Link :href="route('clients.index')" :class="['nav-link', route().current('clients.*') && 'active']">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="nav-link-icon"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" /></svg>
                            Clients
                        </Link>
                        <Link v-if="user?.role?.name === 'superadmin'" :href="route('users.index')" :class="['nav-link', route().current('users.*') && 'active']">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="nav-link-icon"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                            Users
                        </Link>
                    </div>
                </div>

                <div class="nav-right">
                    <div class="nav-status">
                        <div class="status-dot"></div>
                        <span>System Online</span>
                    </div>

                    <div class="profile-trigger" @click="showProfileMenu = !showProfileMenu">
                        <div class="avatar">{{ userInitial }}</div>
                        <div class="profile-info">
                            <div class="profile-name">{{ user?.name }}</div>
                            <div class="profile-role">{{ roleBadge }}</div>
                        </div>
                        <svg :class="['chevron', showProfileMenu && 'open']" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </div>

                    <Transition name="dropdown">
                        <div v-if="showProfileMenu" class="profile-dropdown" @mouseleave="showProfileMenu = false">
                            <div class="dropdown-header">
                                <div class="dropdown-avatar">{{ userInitial }}</div>
                                <div>
                                    <div class="dropdown-name">{{ user?.name }}</div>
                                    <div class="dropdown-email">{{ user?.email }}</div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <Link :href="route('profile.edit')" class="dropdown-item">
                                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                Profile Settings
                            </Link>
                            <Link :href="route('logout')" method="post" as="button" class="dropdown-item danger">
                                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
                                Sign Out
                            </Link>
                        </div>
                    </Transition>
                </div>

                <!-- Mobile Hamburger -->
                <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="mobile-menu-btn sm:hidden">
                    <svg class="w-5 h-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path v-if="!showingNavigationDropdown" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div v-if="showingNavigationDropdown" class="mobile-nav sm:hidden">
                <Link :href="route('dashboard')" class="mobile-link">Dashboard</Link>
                <Link :href="route('reports.index')" class="mobile-link">Reports</Link>
                <Link :href="route('clients.index')" class="mobile-link">Clients</Link>
                <Link v-if="user?.role?.name === 'superadmin'" :href="route('users.index')" class="mobile-link">Users</Link>
                <div class="mobile-divider"></div>
                <div class="mobile-user">{{ user?.name }} · {{ user?.email }}</div>
                <Link :href="route('profile.edit')" class="mobile-link">Profile</Link>
                <Link :href="route('logout')" method="post" as="button" class="mobile-link">Sign Out</Link>
            </div>
        </nav>

        <!-- ═══ HEADER ═══ -->
        <header v-if="$slots.header" class="admin-header">
            <div class="header-inner">
                <slot name="header" />
            </div>
        </header>

        <!-- ═══ MAIN CONTENT ═══ -->
        <main class="admin-main">
            <slot />
        </main>
    </div>
</template>

<style scoped>
/* ─── DESIGN TOKENS ─── */
.admin-shell {
    --bg-primary: #080a0e;
    --bg-secondary: #0d1117;
    --bg-tertiary: #151b23;
    --bg-elevated: #1c2333;
    --border: #21283b;
    --border-subtle: #171d2b;
    --accent: #c9a227;
    --accent-glow: rgba(201, 162, 39, 0.15);
    --accent-hover: #ddb832;
    --text-primary: #e6edf3;
    --text-secondary: #8b949e;
    --text-muted: #484f58;
    --danger: #f85149;
    --success: #3fb950;
    --radius: 8px;

    min-height: 100vh;
    background: var(--bg-primary);
    color: var(--text-primary);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* ─── NAV ─── */
.admin-nav {
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(12px);
    background: rgba(13, 17, 23, 0.85);
}
.nav-inner {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.nav-left { display: flex; align-items: center; gap: 32px; }
.nav-right { display: flex; align-items: center; gap: 20px; }

/* Brand */
.nav-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: var(--text-primary);
}
.brand-icon {
    width: 32px; height: 32px;
    background: linear-gradient(135deg, var(--accent), #8b6914);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 2px 8px rgba(201, 162, 39, 0.3);
}
.brand-text {
    font-weight: 800;
    font-size: 16px;
    letter-spacing: 2px;
    color: var(--accent);
}
.brand-sub {
    font-size: 10px;
    color: var(--text-muted);
    letter-spacing: 1px;
    text-transform: uppercase;
    padding-left: 8px;
    border-left: 1px solid var(--border);
    margin-left: 4px;
}

/* Nav Links */
.nav-links { display: flex; gap: 4px; }
.nav-link {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.2s;
}
.nav-link:hover { color: var(--text-primary); background: var(--bg-tertiary); }
.nav-link.active {
    color: var(--accent);
    background: var(--accent-glow);
}
.nav-link-icon { width: 16px; height: 16px; }

/* Status */
.nav-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    color: var(--text-muted);
    padding: 4px 12px;
    border-radius: 20px;
    border: 1px solid var(--border);
}
.status-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--success);
    box-shadow: 0 0 6px var(--success);
    animation: blink 3s infinite;
}
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

/* Profile */
.profile-trigger {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 8px 4px 4px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
    position: relative;
}
.profile-trigger:hover { background: var(--bg-tertiary); }
.avatar {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--accent), #8b6914);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
    color: #fff;
}
.profile-info { display: none; }
@media (min-width: 768px) { .profile-info { display: block; } }
.profile-name { font-size: 13px; font-weight: 600; color: var(--text-primary); line-height: 1.2; }
.profile-role { font-size: 10px; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; }
.chevron { width: 14px; height: 14px; color: var(--text-muted); transition: transform 0.2s; }
.chevron.open { transform: rotate(180deg); }

/* Profile Dropdown */
.profile-dropdown {
    position: absolute;
    top: 56px;
    right: 24px;
    width: 260px;
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 16px 48px rgba(0,0,0,0.5);
    z-index: 200;
    overflow: hidden;
}
.dropdown-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
}
.dropdown-avatar {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--accent), #8b6914);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    color: #fff;
    flex-shrink: 0;
}
.dropdown-name { font-size: 14px; font-weight: 600; color: var(--text-primary); }
.dropdown-email { font-size: 11px; color: var(--text-muted); }
.dropdown-divider { height: 1px; background: var(--border); }
.dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    font-size: 13px;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.15s;
    width: 100%;
    text-align: left;
    border: none;
    background: none;
    cursor: pointer;
}
.dropdown-item svg { width: 16px; height: 16px; flex-shrink: 0; }
.dropdown-item:hover { background: var(--bg-tertiary); color: var(--text-primary); }
.dropdown-item.danger:hover { color: var(--danger); }

/* Dropdown Animation */
.dropdown-enter-active,
.dropdown-leave-active { transition: all 0.2s ease; }
.dropdown-enter-from,
.dropdown-leave-to { opacity: 0; transform: translateY(-8px) scale(0.95); }

/* Mobile */
.mobile-menu-btn {
    display: none;
    padding: 8px;
    color: var(--text-secondary);
    background: none;
    border: none;
    cursor: pointer;
}
@media (max-width: 640px) { .mobile-menu-btn { display: flex; } .nav-links, .nav-right { display: none; } }
.mobile-nav {
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: var(--bg-secondary);
}
.mobile-link {
    display: block;
    padding: 10px 12px;
    font-size: 14px;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 6px;
    width: 100%; text-align: left; border: none; background: none; cursor: pointer;
}
.mobile-link:hover { background: var(--bg-tertiary); color: var(--text-primary); }
.mobile-divider { height: 1px; background: var(--border); margin: 8px 0; }
.mobile-user { padding: 8px 12px; font-size: 12px; color: var(--text-muted); }

/* ─── HEADER ─── */
.admin-header {
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border);
}
.header-inner {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 24px;
}

/* ─── MAIN ─── */
.admin-main {
    max-width: 1400px;
    margin: 0 auto;
}
</style>
