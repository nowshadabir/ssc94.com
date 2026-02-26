<?php
/**
 * Admin Dashboard
 * SSC Batch '94
 */
require_once '../../config/config.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../auth/admin_login.html");
    exit();
}

$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$adminRole = $_SESSION['admin_role'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin Portal — SSC Batch '94</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
        }

        .stat-card {
            transition: transform .15s, box-shadow .15s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        }

        .div-bar {
            transition: width .6s cubic-bezier(.4, 0, .2, 1);
        }

        .badge-paid {
            background: #dcfce7;
            color: #166534;
        }

        .badge-pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-failed {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Modal */
        #detailModal {
            display: none;
        }

        #detailModal.open {
            display: flex;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>

<body class="flex min-h-screen">

    <!-- ══ SIDEBAR ══════════════════════════════════════════════════════════════ -->
    <aside class="sidebar bg-slate-900 text-slate-400 flex flex-col hidden lg:flex shrink-0">
        <div class="p-6 flex items-center gap-3 border-b border-slate-800">
            <div
                class="w-9 h-9 bg-yellow-400 text-slate-900 rounded-lg flex items-center justify-center font-black text-sm">
                94</div>
            <span class="text-white font-bold tracking-wider text-sm">ADMIN PORTAL</span>
        </div>
        <nav class="flex-1 px-3 py-5 space-y-0.5">
            <a href="#"
                class="flex items-center gap-3 px-4 py-2.5 text-white bg-slate-800 rounded-xl text-sm font-semibold">
                <i data-lucide="layout-dashboard" class="w-4 h-4 text-yellow-400"></i> Overview
            </a>
            <a href="user_registrations.php"
                class="flex items-center gap-3 px-4 py-2.5 hover:text-white hover:bg-slate-800 rounded-xl transition text-sm font-medium">
                <i data-lucide="user-plus" class="w-4 h-4"></i> User Registrations
            </a>
            <a href="payment_gateway_settings.php"
                class="flex items-center gap-3 px-4 py-2.5 hover:text-white hover:bg-slate-800 rounded-xl transition text-sm font-medium">
                <i data-lucide="credit-card" class="w-4 h-4"></i> Payment Gateway
            </a>
            <a href="reunions.php"
                class="flex items-center gap-3 px-4 py-2.5 hover:text-white hover:bg-slate-800 rounded-xl transition text-sm font-medium">
                <i data-lucide="party-popper" class="w-4 h-4"></i> Reunions
            </a>
            <a href="#"
                class="flex items-center gap-3 px-4 py-2.5 hover:text-white hover:bg-slate-800 rounded-xl transition text-sm font-medium">
                <i data-lucide="users" class="w-4 h-4"></i> Batchmates
            </a>
            <a href="#"
                class="flex items-center gap-3 px-4 py-2.5 hover:text-white hover:bg-slate-800 rounded-xl transition text-sm font-medium">
                <i data-lucide="calendar" class="w-4 h-4"></i> Events
            </a>
            <a href="#"
                class="flex items-center gap-3 px-4 py-2.5 hover:text-white hover:bg-slate-800 rounded-xl transition text-sm font-medium">
                <i data-lucide="heart-pulse" class="w-4 h-4"></i> Blood Donors
            </a>
        </nav>
        <div class="p-4 border-t border-slate-800">
            <a href="../../api/auth/logout.php"
                class="flex items-center gap-3 px-4 py-2.5 text-red-400 hover:bg-red-400/10 rounded-xl transition text-sm font-medium">
                <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
            </a>
        </div>
    </aside>

    <!-- ══ MAIN ══════════════════════════════════════════════════════════════════ -->
    <main class="flex-1 flex flex-col min-w-0">

        <!-- Top Header -->
        <header
            class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 lg:px-8 shrink-0 sticky top-0 z-20">
            <div>
                <h1 class="text-sm font-bold text-slate-900">System Overview</h1>
                <p class="text-[11px] text-slate-400">SSC Batch '94 — Admin Dashboard</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-slate-900"><?php echo htmlspecialchars($adminName); ?></p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        <?php echo htmlspecialchars($adminRole); ?>
                    </p>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($adminName); ?>&background=0f172a&color=fff&size=80"
                    class="w-9 h-9 rounded-full border-2 border-slate-200" alt="Admin">
            </div>
        </header>

        <div class="p-6 lg:p-8 space-y-8 overflow-y-auto">

            <!-- ══ TOP STAT CARDS ══════════════════════════════════════════════ -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Total Members -->
                <div
                    class="stat-card bg-white rounded-2xl border border-slate-100 shadow-sm p-5 col-span-2 lg:col-span-1">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total</span>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900" id="statTotal">—</p>
                    <p class="text-xs text-slate-500 mt-1">Total Members</p>
                    <p class="text-[11px] text-green-600 font-bold mt-0.5" id="statActive"></p>
                </div>

                <!-- Reunion Registrations -->
                <div class="stat-card bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="ticket" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900" id="statRegTotal">—</p>
                    <p class="text-xs text-slate-500 mt-1">Reunion Registrations</p>
                    <p class="text-[11px] text-yellow-600 font-bold mt-0.5" id="statRegPaid"></p>
                </div>

                <!-- Revenue -->
                <div class="stat-card bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="banknote" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900" id="statRevenue">—</p>
                    <p class="text-xs text-slate-500 mt-1">Reunion Revenue (৳)</p>
                </div>

                <!-- Pending -->
                <div class="stat-card bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900" id="statPending">—</p>
                    <p class="text-xs text-slate-500 mt-1">Awaiting Payment</p>
                </div>
            </div>

            <!-- ══ MEMBERS BY DIVISION ══════════════════════════════════════════ -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-5 h-5 text-indigo-500"></i>
                    <h2 class="font-bold text-slate-800 text-sm">Members by Division</h2>
                    <span class="ml-auto text-[11px] text-slate-400">All 8 Bangladesh Divisions</span>
                </div>
                <div class="p-6" id="divisionBars">
                    <!-- JS populated -->
                    <div class="flex items-center justify-center h-24 text-slate-300">
                        <i data-lucide="loader-2" class="animate-spin w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <!-- ══ REUNION MANAGEMENT ══════════════════════════════════════════ -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="party-popper" class="w-5 h-5 text-yellow-500"></i>
                    <h2 class="font-bold text-slate-800 text-sm">Manage Upcoming Reunion</h2>
                    <span id="reunionStatusBadge"
                        class="ml-auto hidden text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
                <div class="p-6">
                    <form id="reunionForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                        <div class="sm:col-span-2 lg:col-span-3">
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Reunion
                                Title</label>
                            <input type="text" name="title" required placeholder="e.g. Grand Reunion 2025"
                                class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Date</label>
                            <input type="date" name="reunion_date" required
                                class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Time</label>
                            <input type="text" name="reunion_time" placeholder="e.g. 09:00 AM"
                                class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Registration
                                Deadline</label>
                            <input type="date" name="registration_deadline"
                                class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Venue
                                Name</label>
                            <input type="text" name="venue" required placeholder="Venue Name"
                                class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                        </div>
                        <div class="sm:col-span-2">
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Venue
                                Address / Details</label>
                            <input type="text" name="venue_details" placeholder="Full address or area"
                                class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Cost
                                — Alumnus (৳)</label>
                            <input type="number" name="cost_alumnus" required placeholder="2500"
                                class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Cost
                                — Spouse / Guest (৳)</label>
                            <input type="number" name="cost_guest" required placeholder="1500"
                                class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Status</label>
                            <select name="status"
                                class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm font-semibold focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                                <option value="active">🟢 Active / Upcoming</option>
                                <option value="completed">🔵 Completed</option>
                                <option value="inactive">⚪ Inactive</option>
                            </select>
                        </div>

                        <div class="sm:col-span-1 lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Food
                                    Menu / Feast</label>
                                <textarea name="food_menu" rows="3"
                                    placeholder="e.g. Breakfast, Grand Buffet Lunch & Evening Snacks"
                                    class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition resize-none"></textarea>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Activities
                                    / Entertainment</label>
                                <textarea name="activities" rows="3"
                                    placeholder="e.g. Live Band, Raffle Draw & Cultural Program"
                                    class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition resize-none"></textarea>
                            </div>
                        </div>

                        <div class="sm:col-span-2 lg:col-span-3 flex items-center gap-3">
                            <button type="submit" id="saveBtn"
                                class="bg-slate-900 text-white px-8 py-2.5 rounded-xl font-bold text-sm hover:bg-black transition flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i> Save Reunion Details
                            </button>
                            <span id="saveStatus" class="text-sm font-medium hidden"></span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ══ RECENT REUNION REGISTRATIONS ══════════════════════════════ -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="list-checks" class="w-5 h-5 text-indigo-500"></i>
                    <h2 class="font-bold text-slate-800 text-sm">Recent Reunion Registrations</h2>
                    <div class="ml-auto flex items-center gap-2">
                        <span id="regCountBadge"
                            class="text-[11px] bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded-full hidden"></span>
                    </div>
                </div>

                <!-- Loading -->
                <div id="regLoading" class="hidden p-10 flex justify-center">
                    <i data-lucide="loader-2" class="w-7 h-7 animate-spin text-slate-300"></i>
                </div>

                <!-- Table -->
                <div id="regTableWrap" class="overflow-x-auto">
                    <table class="w-full text-left min-w-[700px]">
                        <thead>
                            <tr
                                class="bg-slate-50 text-[10px] text-slate-400 font-bold uppercase tracking-widest border-b border-slate-100">
                                <th class="px-5 py-3">Alumnus</th>
                                <th class="px-5 py-3">Ticket</th>
                                <th class="px-5 py-3">T-Shirt</th>
                                <th class="px-5 py-3">Guests</th>
                                <th class="px-5 py-3">Amount</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Date</th>
                                <th class="px-5 py-3 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody id="regTableBody" class="divide-y divide-slate-50 text-sm">
                            <!-- JS populated -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/60 flex items-center justify-between">
                    <p class="text-xs text-slate-500">
                        Showing <strong id="regRange" class="text-slate-800">—</strong> of <strong id="regTotal"
                            class="text-slate-800">—</strong>
                    </p>
                    <div class="flex items-center gap-2">
                        <button id="prevBtn" onclick="changePage(-1)" disabled
                            class="p-1.5 rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <span class="text-xs font-bold text-slate-800 w-6 text-center" id="regPage">1</span>
                        <button id="nextBtn" onclick="changePage(1)" disabled
                            class="p-1.5 rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div><!-- /content -->
    </main>

    <!-- ══ DETAIL MODAL ══════════════════════════════════════════════════════════ -->
    <div id="detailModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="bg-slate-900 px-6 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <i data-lucide="ticket" class="w-5 h-5 text-yellow-400"></i>
                    <span class="text-white font-bold text-sm uppercase tracking-wider">Registration Detail</span>
                </div>
                <button onclick="closeModal()" class="text-slate-400 hover:text-white transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <!-- Modal Body -->
            <div id="modalBody" class="overflow-y-auto p-6 space-y-5 text-sm">
                <!-- JS populated -->
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // ══ Division colours ══════════════════════════════════════════════════════
        const DIV_COLORS = ['#6366f1', '#0ea5e9', '#f59e0b', '#10b981', '#f43f5e', '#a855f7', '#14b8a6', '#f97316'];

        // ══ LOAD DASHBOARD STATS ══════════════════════════════════════════════════
        async function loadStats() {
            try {
                const res = await fetch('../../api/admin/get_dashboard_stats.php');
                const json = await res.json();
                if (!json.success) return;
                const d = json.data;

                // Top cards
                document.getElementById('statTotal').textContent = d.total_members.toLocaleString();
                document.getElementById('statActive').textContent = d.active_members.toLocaleString() + ' active';
                document.getElementById('statRegTotal').textContent = d.reunion_stats.total.toLocaleString();
                document.getElementById('statRegPaid').textContent = (d.reunion_stats.completed ?? 0) + ' paid';
                document.getElementById('statRevenue').textContent = '৳ ' + (d.reunion_stats.revenue ?? 0).toLocaleString();
                document.getElementById('statPending').textContent = (d.reunion_stats.pending ?? 0).toLocaleString();

                // Division bars
                const container = document.getElementById('divisionBars');
                const divs = d.divisions;
                const max = Math.max(...Object.values(divs), 1);
                const total = d.total_members || 1;

                container.innerHTML = Object.entries(divs).map(([name, count], i) => {
                    const pct = Math.round((count / total) * 100);
                    const barW = Math.round((count / max) * 100);
                    return `
            <div class="flex items-center gap-3 mb-3 group">
                <span class="w-24 text-xs font-semibold text-slate-600 shrink-0 text-right">${name}</span>
                <div class="flex-1 bg-slate-100 rounded-full h-5 overflow-hidden">
                    <div class="div-bar h-full rounded-full flex items-center justify-end pr-2"
                         style="width:${barW}%; background:${DIV_COLORS[i % DIV_COLORS.length]};">
                    </div>
                </div>
                <span class="w-20 text-xs text-slate-500 font-bold shrink-0">
                    ${count} <em class="not-italic text-slate-400">(${pct}%)</em>
                </span>
            </div>`;
                }).join('');

                // Reunion form
                if (d.reunion) {
                    const r = d.reunion;
                    const form = document.getElementById('reunionForm');
                    form.title.value = r.title || '';
                    form.reunion_date.value = r.reunion_date || '';
                    form.reunion_time.value = r.reunion_time || '';
                    form.venue.value = r.venue || '';
                    form.venue_details.value = r.venue_details || '';
                    form.cost_alumnus.value = r.cost_alumnus || '';
                    form.cost_guest.value = r.cost_guest || '';
                    form.registration_deadline.value = r.registration_deadline || '';
                    form.food_menu.value = r.food_menu || '';
                    form.activities.value = r.activities || '';
                    if (form.status) form.status.value = r.status || 'active';

                    const badge = document.getElementById('reunionStatusBadge');
                    badge.classList.remove('hidden');
                    if (r.status === 'active') {
                        badge.className = 'ml-auto text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-green-100 text-green-700';
                        badge.textContent = '🟢 Active';
                    } else if (r.status === 'completed') {
                        badge.className = 'ml-auto text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700';
                        badge.textContent = '🔵 Completed';
                    }
                }

            } catch (e) { console.error('Stats error', e); }
        }

        // ══ REUNION FORM SAVE ═════════════════════════════════════════════════════
        document.getElementById('reunionForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('saveBtn');
            const status = document.getElementById('saveStatus');
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline mr-1"></i> Saving…';
            lucide.createIcons();

            try {
                const res = await fetch('../../api/admin/reunion_update.php', { method: 'POST', body: new FormData(this) });
                const json = await res.json();
                status.classList.remove('hidden');
                if (json.success) {
                    status.className = 'text-sm font-semibold text-green-600';
                    status.textContent = '✓ Saved successfully!';
                } else {
                    status.className = 'text-sm font-semibold text-red-600';
                    status.textContent = json.message || 'Save failed';
                }
                setTimeout(() => status.classList.add('hidden'), 4000);
            } catch (err) {
                status.classList.remove('hidden');
                status.className = 'text-sm font-semibold text-red-600';
                status.textContent = 'Connection error';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="save" class="w-4 h-4 inline mr-1"></i> Save Reunion Details';
                lucide.createIcons();
            }
        });

        // ══ REGISTRATIONS TABLE ═══════════════════════════════════════════════════
        let currentPage = 1;
        const PER_PAGE = 20;

        const BADGE = {
            completed: '<span class="badge-paid   text-[10px] font-bold px-2 py-0.5 rounded-full">Paid</span>',
            pending: '<span class="badge-pending text-[10px] font-bold px-2 py-0.5 rounded-full">Pending</span>',
            failed: '<span class="badge-failed  text-[10px] font-bold px-2 py-0.5 rounded-full">Failed</span>',
        };

        // Store fetched data for modal usage
        let _regsCache = [];

        async function loadRegistrations(page = 1) {
            currentPage = page;
            const tbody = document.getElementById('regTableBody');
            const loader = document.getElementById('regLoading');
            const tableW = document.getElementById('regTableWrap');

            loader.classList.remove('hidden');
            tableW.classList.add('hidden');

            try {
                const res = await fetch(`../../api/admin/get_reunion_registrations.php?page=${page}&limit=${PER_PAGE}`);
                const json = await res.json();

                if (!json.success) { tbody.innerHTML = `<tr><td colspan="8" class="px-5 py-10 text-center text-slate-400 italic">Failed to load data.</td></tr>`; return; }

                const regs = json.data.registrations;
                const meta = json.data.pagination;
                _regsCache = regs;

                if (regs.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="px-5 py-10 text-center text-slate-400 italic">No registrations yet.</td></tr>`;
                } else {
                    tbody.innerHTML = regs.map((r, idx) => {
                        const avatar = r.profile_photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(r.full_name)}&background=e2e8f0&color=475569&size=80`;
                        const date = new Date(r.created_at);
                        return `
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <img src="${avatar}" class="w-8 h-8 rounded-full object-cover border border-slate-200 shrink-0"
                                 onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(r.full_name)}&background=e2e8f0&color=475569'">
                            <div>
                                <div class="font-semibold text-slate-900 text-xs leading-tight">${r.full_name}</div>
                                <div class="text-slate-400 text-[10px]">${r.mobile}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full tracking-wider">${r.ticket_number}</span>
                    </td>
                    <td class="px-5 py-3 text-xs text-slate-600 font-medium">${r.tshirt_size}</td>
                    <td class="px-5 py-3 text-xs text-slate-700">
                        ${r.guest_count > 0 ? `<span class="font-bold">${r.guest_count}</span> guest${r.guest_count > 1 ? 's' : ''}` : '<span class="text-slate-400">None</span>'}
                    </td>
                    <td class="px-5 py-3 text-xs font-bold text-slate-900">৳ ${parseFloat(r.total_amount).toLocaleString()}</td>
                    <td class="px-5 py-3">${BADGE[r.payment_status] ?? r.payment_status}</td>
                    <td class="px-5 py-3 text-[11px] text-slate-500">${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                    <td class="px-5 py-3 text-center">
                        <button onclick="openModal(${idx})"
                            class="text-indigo-600 hover:text-indigo-800 text-[11px] font-bold border border-indigo-100 hover:border-indigo-300 px-3 py-1 rounded-lg transition">
                            View
                        </button>
                    </td>
                </tr>`;
                    }).join('');
                }

                // Pagination
                const start = (meta.current_page - 1) * meta.limit + 1;
                const end = Math.min(meta.current_page * meta.limit, meta.total_count);
                document.getElementById('regRange').textContent = `${start}–${end}`;
                document.getElementById('regTotal').textContent = meta.total_count;
                document.getElementById('regPage').textContent = meta.current_page;
                document.getElementById('prevBtn').disabled = meta.current_page <= 1;
                document.getElementById('nextBtn').disabled = meta.current_page >= meta.total_pages;
                document.getElementById('regCountBadge').textContent = meta.total_count + ' total';
                document.getElementById('regCountBadge').classList.remove('hidden');

            } catch (e) { console.error('Reg load error', e); }
            finally {
                loader.classList.add('hidden');
                tableW.classList.remove('hidden');
                lucide.createIcons();
            }
        }

        function changePage(dir) { loadRegistrations(currentPage + dir); }

        // ══ DETAIL MODAL ══════════════════════════════════════════════════════════
        function openModal(idx) {
            const r = _regsCache[idx];
            if (!r) return;

            const guests = (r.guests_data ?? []);
            const guestHtml = guests.length > 0
                ? guests.map((g, i) => `
            <div class="flex items-center gap-3 bg-slate-50 rounded-lg px-4 py-2.5 border border-slate-100">
                <span class="text-xs font-bold text-slate-500 w-5">${i + 2}</span>
                <div class="flex-1">
                    <span class="font-semibold text-slate-800">${g.name || '—'}</span>
                    <span class="ml-2 text-slate-400 text-[11px]">${g.gender ?? ''}</span>
                </div>
                <span class="text-[11px] font-bold bg-slate-200 text-slate-600 px-2 py-0.5 rounded">${g.tshirt ?? ''}</span>
            </div>`).join('')
                : '<p class="text-slate-400 text-xs italic">No additional guests</p>';

            document.getElementById('modalBody').innerHTML = `
        <!-- Header info -->
        <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
            <img src="${r.profile_photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(r.full_name)}&background=0f172a&color=fff&size=120`}"
                 class="w-16 h-16 rounded-full object-cover border-2 border-slate-200"
                 onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(r.full_name)}&background=0f172a&color=fff'">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">${r.full_name}</h3>
                <p class="text-slate-500 text-xs">${r.mobile} · ${r.email ?? '—'}</p>
                <div class="mt-1 flex gap-1.5 flex-wrap">
                    <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">${r.ticket_number}</span>
                    ${BADGE[r.payment_status] ?? ''}
                </div>
            </div>
        </div>

        <!-- Grid details -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            ${infoCell('Gender', r.gender ?? '—', 'user')}
            ${infoCell('T-Shirt (Self)', r.tshirt_size, 'shirt')}
            ${infoCell('Total Amount', '৳ ' + parseFloat(r.total_amount).toLocaleString(), 'banknote')}
            ${infoCell('Transaction ID', r.transaction_id || '—', 'hash')}
            ${infoCell('Division', r.current_location || '—', 'map-pin')}
            ${infoCell('School / Zilla', (r.school_name ? r.school_name + ', ' : '') + (r.zilla || '—'), 'school')}
            ${infoCell('Blood Group', r.blood_group || '—', 'droplet')}
            ${infoCell('Registered', new Date(r.created_at).toLocaleString(), 'calendar')}
        </div>

        <!-- Guests -->
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Guests (${guests.length + 1} total incl. member)</p>
            <div class="mb-2 flex items-center gap-3 bg-slate-900 rounded-lg px-4 py-2.5 text-white">
                <span class="text-xs font-bold text-slate-400 w-5">1</span>
                <div class="flex-1 text-sm font-semibold">${r.full_name} <span class="text-slate-400 text-xs font-normal">(Member)</span></div>
                <span class="text-[11px] font-bold bg-slate-700 text-slate-300 px-2 py-0.5 rounded">${r.tshirt_size}</span>
            </div>
            ${guestHtml}
        </div>
    `;

            document.getElementById('detailModal').classList.add('open');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        }

        function infoCell(label, value, icon) {
            return `
    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1">
            <i data-lucide="${icon}" class="w-3 h-3"></i>${label}
        </p>
        <p class="text-sm font-semibold text-slate-800 truncate">${value}</p>
    </div>`;
        }

        function closeModal() {
            document.getElementById('detailModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        // Close on backdrop click
        document.getElementById('detailModal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        // ══ INIT ══════════════════════════════════════════════════════════════════
        loadStats();
        loadRegistrations(1);
    </script>
</body>

</html>