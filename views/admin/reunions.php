<?php
/**
 * Admin — Reunions Management (Full CRUD)
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
    <title>Reunions | Admin Portal — SSC Batch '94</title>
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

        /* Modals */
        .modal-wrap {
            display: none;
        }

        .modal-wrap.open {
            display: flex;
        }

        /* Slide panel */
        #detailPanel {
            transition: transform .3s cubic-bezier(.4, 0, .2, 1);
        }

        .panel-closed {
            transform: translateX(100%);
        }

        .panel-open {
            transform: translateX(0);
        }

        /* Badges */
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

        .badge-active {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-completed {
            background: #ede9fe;
            color: #5b21b6;
        }

        .badge-inactive {
            background: #f1f5f9;
            color: #64748b;
        }

        .tab-active {
            background: #0f172a;
            color: #fff;
        }

        .tab-inactive {
            background: #f8fafc;
            color: #64748b;
        }

        /* Input focus ring */
        .field {
            @apply w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition;
        }

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
            <a href="dashboard.php"
                class="flex items-center gap-3 px-4 py-2.5 hover:text-white hover:bg-slate-800 rounded-xl transition text-sm font-medium">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Overview
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
                class="flex items-center gap-3 px-4 py-2.5 text-white bg-slate-800 rounded-xl text-sm font-semibold">
                <i data-lucide="party-popper" class="w-4 h-4 text-yellow-400"></i> Reunions
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

        <header
            class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 lg:px-8 shrink-0 sticky top-0 z-20">
            <div>
                <h1 class="text-sm font-bold text-slate-900">Reunions</h1>
                <p class="text-[11px] text-slate-400">Create, edit & review all reunions</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openForm(null)"
                    class="flex items-center gap-2 bg-slate-900 hover:bg-black text-white text-xs font-bold px-4 py-2 rounded-xl transition mr-4">
                    <i data-lucide="plus" class="w-4 h-4"></i> New Reunion
                </button>
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

        <div class="p-6 lg:p-8 space-y-6 overflow-y-auto">

            <!-- Summary Cards -->
            <div id="summaryCards" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ([
                    ['id' => 'cTotal', 'icon' => 'calendar-check', 'color' => 'indigo', 'label' => 'Total Reunions'],
                    ['id' => 'cRegs', 'icon' => 'ticket', 'color' => 'yellow', 'label' => 'All Registrations'],
                    ['id' => 'cRevenue', 'icon' => 'banknote', 'color' => 'green', 'label' => 'Confirmed Revenue (৳)'],
                    ['id' => 'cGuests', 'icon' => 'users', 'color' => 'purple', 'label' => 'Total Attendees'],
                ] as $c): ?>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <div
                            class="w-9 h-9 bg-<?= $c['color'] ?>-50 text-<?= $c['color'] ?>-600 rounded-xl flex items-center justify-center mb-3">
                            <i data-lucide="<?= $c['icon'] ?>" class="w-4 h-4"></i>
                        </div>
                        <p class="text-2xl font-extrabold text-slate-900" id="<?= $c['id'] ?>">—</p>
                        <p class="text-xs text-slate-500 mt-0.5"><?= $c['label'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Loader -->
            <div id="mainLoader" class="flex items-center justify-center py-20">
                <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-slate-300"></i>
            </div>

            <!-- Reunion Cards -->
            <div id="reunionList" class="hidden space-y-4"></div>

            <!-- Empty -->
            <div id="emptyState" class="hidden flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="party-popper" class="w-10 h-10 text-slate-300"></i>
                </div>
                <p class="font-bold text-slate-600 text-lg">No reunions yet</p>
                <p class="text-sm text-slate-400 mb-6">Create the first reunion to get started.</p>
                <button onclick="openForm(null)"
                    class="bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-xl hover:bg-black transition flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> Create First Reunion
                </button>
            </div>

        </div>
    </main>

    <!-- ══ SLIDE DETAIL PANEL ════════════════════════════════════════════════════ -->
    <div id="overlay" class="fixed inset-0 bg-slate-900/40 z-30 hidden" onclick="closePanel()"></div>

    <aside id="detailPanel"
        class="panel-closed fixed top-0 right-0 h-full w-full max-w-xl bg-white z-40 shadow-2xl flex flex-col">
        <!-- Panel Header -->
        <div class="bg-slate-900 px-6 py-4 flex items-center gap-3 shrink-0">
            <i data-lucide="party-popper" class="w-5 h-5 text-yellow-400"></i>
            <div class="flex-1 min-w-0">
                <p id="panelTitle" class="text-white font-bold text-sm truncate">Reunion</p>
                <p id="panelSub" class="text-slate-400 text-[11px]"></p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button id="panelEditBtn" onclick="editCurrentFromPanel()"
                    class="flex items-center gap-1.5 text-[11px] font-bold text-slate-300 hover:text-white border border-slate-700 hover:border-slate-500 px-3 py-1.5 rounded-lg transition">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                </button>
                <button onclick="closePanel()" class="text-slate-400 hover:text-white transition p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-slate-100 shrink-0 bg-slate-50">
            <button class="tab-btn flex-1 py-3 text-xs font-bold uppercase tracking-widest transition"
                data-tab="overview" onclick="switchTab('overview')">Overview</button>
            <button class="tab-btn flex-1 py-3 text-xs font-bold uppercase tracking-widest transition"
                data-tab="registrants" onclick="switchTab('registrants')">Registrants</button>
            <button class="tab-btn flex-1 py-3 text-xs font-bold uppercase tracking-widest transition"
                data-tab="tshirts" onclick="switchTab('tshirts')">T-Shirts</button>
        </div>

        <div id="panelBody" class="flex-1 overflow-y-auto p-6 space-y-4 text-sm"></div>
    </aside>

    <!-- ══ ADD / EDIT MODAL ══════════════════════════════════════════════════════ -->
    <div id="formModal"
        class="modal-wrap fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col">

            <!-- Header -->
            <div class="bg-slate-900 px-6 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <i data-lucide="calendar-plus" class="w-5 h-5 text-yellow-400"></i>
                    <span id="formModalTitle" class="text-white font-bold text-sm">New Reunion</span>
                </div>
                <button onclick="closeForm()" class="text-slate-400 hover:text-white transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Body -->
            <form id="reunionForm" class="overflow-y-auto p-6 space-y-5">
                <input type="hidden" id="formReunionId" name="reunion_id" value="">

                <!-- Title -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Reunion
                        Title *</label>
                    <input type="text" name="title" id="fTitle" required placeholder="e.g. Grand Reunion 2025"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Date
                            *</label>
                        <input type="date" name="reunion_date" id="fDate" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Time</label>
                        <input type="text" name="reunion_time" id="fTime" placeholder="09:00 AM"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Reg.
                            Deadline</label>
                        <input type="date" name="registration_deadline" id="fDeadline"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Venue
                            Name *</label>
                        <input type="text" name="venue" id="fVenue" required placeholder="e.g. Hotel Radisson Blu"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Status</label>
                        <select name="status" id="fStatus"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="active">🟢 Active</option>
                            <option value="inactive">⚪ Inactive</option>
                            <option value="completed">🔵 Completed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Venue
                        Address / Details</label>
                    <input type="text" name="venue_details" id="fVenueDetails" placeholder="Full address or area"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Cost
                            — Alumnus (৳) *</label>
                        <input type="number" name="cost_alumnus" id="fCostA" required min="0" placeholder="2500"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Cost
                            — Guest/Spouse (৳) *</label>
                        <input type="number" name="cost_guest" id="fCostG" required min="0" placeholder="1500"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Food
                            Menu / Feast</label>
                        <textarea name="food_menu" id="fFood" rows="3"
                            placeholder="e.g. Breakfast, Grand Buffet Lunch & Evening Snacks"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition resize-none"></textarea>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Activities
                            / Entertainment</label>
                        <textarea name="activities" id="fActivities" rows="3"
                            placeholder="e.g. Live Band, Raffle Draw & Cultural Program"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition resize-none"></textarea>
                    </div>
                </div>
            </form>

            <!-- Footer Actions -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0">
                <span id="formError" class="text-xs text-red-600 font-semibold hidden"></span>
                <div class="flex items-center gap-3 ml-auto">
                    <button onclick="closeForm()" type="button"
                        class="text-slate-600 hover:text-slate-900 text-sm font-semibold px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button onclick="submitForm()" id="formSaveBtn" type="button"
                        class="bg-slate-900 hover:bg-black text-white text-sm font-bold px-6 py-2 rounded-xl transition flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span id="formSaveTxt">Save Reunion</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ DELETE CONFIRM MODAL ══════════════════════════════════════════════════ -->
    <div id="deleteModal"
        class="modal-wrap fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-6 text-center">
                <div
                    class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="trash-2" class="w-7 h-7"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-lg mb-1">Delete Reunion?</h3>
                <p id="deleteModalMsg" class="text-sm text-slate-500 mb-6">This action cannot be undone.</p>
                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()"
                        class="flex-1 border border-slate-200 text-slate-700 text-sm font-semibold py-2.5 rounded-xl hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" id="deleteConfirmBtn"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ REGISTRANT DETAIL MODAL ════════════════════════════════════════════════ -->
    <div id="regModal"
        class="modal-wrap fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden max-h-[85vh] flex flex-col">
            <div class="bg-slate-900 px-6 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <i data-lucide="user" class="w-5 h-5 text-yellow-400"></i>
                    <span class="text-white font-bold text-sm">Registrant Detail</span>
                </div>
                <button onclick="closeRegModal()" class="text-slate-400 hover:text-white transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div id="regModalBody" class="overflow-y-auto p-6 space-y-4 text-sm"></div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // ──────────────────────────────────────────────────────────
        // STATE
        // ──────────────────────────────────────────────────────────
        let _all = [];
        let _current = null;   // reunion open in panel
        let _regs = [];     // registrations for open reunion
        let _curTab = 'overview';
        let _deleteId = null;

        const BADGE = {
            completed: '<span class="badge-paid    text-[10px] font-bold px-2 py-0.5 rounded-full">Paid</span>',
            pending: '<span class="badge-pending text-[10px] font-bold px-2 py-0.5 rounded-full">Pending</span>',
            failed: '<span class="badge-failed  text-[10px] font-bold px-2 py-0.5 rounded-full">Failed</span>',
        };
        const STATUS_BADGE = {
            active: '<span class="badge-active    text-[10px] font-bold px-2.5 py-0.5 rounded-full">🟢 Active</span>',
            completed: '<span class="badge-completed text-[10px] font-bold px-2.5 py-0.5 rounded-full">🔵 Completed</span>',
            inactive: '<span class="badge-inactive  text-[10px] font-bold px-2.5 py-0.5 rounded-full">⚪ Inactive</span>',
        };

        // ──────────────────────────────────────────────────────────
        // LOAD ALL
        // ──────────────────────────────────────────────────────────
        async function loadAll() {
            document.getElementById('mainLoader').classList.remove('hidden');
            document.getElementById('reunionList').classList.add('hidden');
            document.getElementById('emptyState').classList.add('hidden');

            try {
                const res = await fetch('../../api/admin/get_all_reunions.php');
                const json = await res.json();

                document.getElementById('mainLoader').classList.add('hidden');

                if (!json.success || json.data.length === 0) {
                    document.getElementById('emptyState').classList.remove('hidden');
                    resetSummary();
                    return;
                }

                _all = json.data;

                // Summary
                const totRegs = _all.reduce((a, r) => a + parseInt(r.total_registrations), 0);
                const totRevenue = _all.reduce((a, r) => a + parseFloat(r.confirmed_revenue), 0);
                const totGuests = _all.reduce((a, r) => a + parseInt(r.total_registrations) + parseInt(r.total_guests), 0);
                document.getElementById('cTotal').textContent = _all.length;
                document.getElementById('cRegs').textContent = totRegs.toLocaleString();
                document.getElementById('cRevenue').textContent = '৳ ' + totRevenue.toLocaleString();
                document.getElementById('cGuests').textContent = totGuests.toLocaleString();

                // Render cards
                const list = document.getElementById('reunionList');
                list.innerHTML = _all.map((r, idx) => renderCard(r, idx)).join('');
                list.classList.remove('hidden');
                lucide.createIcons();

            } catch (e) {
                console.error(e);
                document.getElementById('mainLoader').innerHTML = '<p class="text-red-500 text-sm font-medium">Failed to load reunions.</p>';
            }
        }

        function resetSummary() {
            ['cTotal', 'cRegs', 'cRevenue', 'cGuests'].forEach(id => document.getElementById(id).textContent = '0');
        }

        function renderCard(r, idx) {
            const d = new Date(r.reunion_date);
            const paid = parseInt(r.paid_count);
            const pct = r.total_registrations > 0 ? Math.round((paid / r.total_registrations) * 100) : 0;
            const dateStr = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

            return `
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden group hover:shadow-md transition">
        <!-- Card Top -->
        <div class="flex items-start gap-5 p-6 cursor-pointer" onclick="openPanel(${idx})">
            <!-- Date Block -->
            <div class="shrink-0 w-16 text-center bg-slate-50 rounded-xl p-2 border border-slate-100">
                <div class="text-3xl font-black text-slate-900 leading-none">${d.getDate()}</div>
                <div class="text-[10px] font-bold text-slate-500 uppercase mt-0.5">${d.toLocaleString('en-GB', { month: 'short' })}</div>
                <div class="text-[10px] text-slate-400">${d.getFullYear()}</div>
            </div>
            <!-- Info -->
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-start gap-2 mb-1">
                    <h3 class="font-extrabold text-slate-900 text-base leading-tight">${r.title}</h3>
                    ${STATUS_BADGE[r.status] ?? ''}
                </div>
                <p class="text-xs text-slate-500 flex items-center gap-1">
                    <i data-lucide="map-pin" class="w-3 h-3 shrink-0"></i>
                    <span class="truncate">${r.venue}${r.venue_details ? ' — ' + r.venue_details : ''}</span>
                </p>
                ${r.reunion_time ? `<p class="text-[11px] text-slate-400 mt-0.5">⏰ ${r.reunion_time}</p>` : ''}

                <!-- Stats row -->
                <div class="flex flex-wrap gap-4 mt-3 text-xs">
                    <span class="text-slate-600"><strong class="text-slate-900">${r.total_registrations}</strong> registered</span>
                    <span class="text-slate-600"><strong class="text-green-700">${r.paid_count}</strong> paid</span>
                    <span class="text-slate-600"><strong class="text-amber-600">${r.pending_count}</strong> pending</span>
                    <span class="text-slate-600"><strong class="text-slate-900">৳ ${parseFloat(r.confirmed_revenue).toLocaleString()}</strong> collected</span>
                </div>

                <!-- Progress -->
                <div class="mt-3">
                    <div class="flex justify-between mb-1">
                        <span class="text-[10px] text-slate-400">Payment completion</span>
                        <span class="text-[10px] font-bold text-slate-600">${pct}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width:${pct}%"></div>
                    </div>
                </div>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5 text-slate-300 shrink-0 mt-1"></i>
        </div>

        <!-- Action Footer -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center gap-4">
            <span class="text-[11px] text-slate-500">
                💰 Member <strong class="text-slate-800">৳ ${parseFloat(r.cost_alumnus).toLocaleString()}</strong>
                &nbsp;·&nbsp;
                👥 Guest <strong class="text-slate-800">৳ ${parseFloat(r.cost_guest).toLocaleString()}</strong>
            </span>
            <div class="ml-auto flex items-center gap-2">
                <button onclick="event.stopPropagation(); openForm(${idx})"
                    class="flex items-center gap-1.5 text-[11px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 px-3 py-1.5 rounded-lg transition">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                </button>
                <button onclick="event.stopPropagation(); promptDelete(${idx})"
                    class="flex items-center gap-1.5 text-[11px] font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-100 px-3 py-1.5 rounded-lg transition">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Delete
                </button>
            </div>
        </div>
    </div>`;
        }

        // ──────────────────────────────────────────────────────────
        // ADD / EDIT FORM MODAL
        // ──────────────────────────────────────────────────────────
        function openForm(idx) {
            const form = document.getElementById('reunionForm');
            form.reset();
            document.getElementById('formError').classList.add('hidden');

            if (idx !== null) {
                // Edit mode
                const r = _all[idx];
                document.getElementById('formModalTitle').textContent = 'Edit Reunion';
                document.getElementById('formSaveTxt').textContent = 'Save Changes';
                document.getElementById('formReunionId').value = r.reunion_id;
                document.getElementById('fTitle').value = r.title || '';
                document.getElementById('fDate').value = r.reunion_date || '';
                document.getElementById('fTime').value = r.reunion_time || '';
                document.getElementById('fDeadline').value = r.registration_deadline || '';
                document.getElementById('fVenue').value = r.venue || '';
                document.getElementById('fVenueDetails').value = r.venue_details || '';
                document.getElementById('fStatus').value = r.status || 'active';
                document.getElementById('fCostA').value = r.cost_alumnus || '';
                document.getElementById('fCostG').value = r.cost_guest || '';
                document.getElementById('fFood').value = r.food_menu || '';
                document.getElementById('fActivities').value = r.activities || '';
            } else {
                // Create mode
                document.getElementById('formModalTitle').textContent = 'New Reunion';
                document.getElementById('formSaveTxt').textContent = 'Create Reunion';
                document.getElementById('formReunionId').value = '';
            }

            document.getElementById('formModal').classList.add('open');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        }

        function closeForm() {
            document.getElementById('formModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        async function submitForm() {
            const btn = document.getElementById('formSaveBtn');
            const txtSpan = document.getElementById('formSaveTxt');
            const errEl = document.getElementById('formError');
            const form = document.getElementById('reunionForm');

            if (!form.checkValidity()) { form.reportValidity(); return; }

            const origTxt = txtSpan.textContent;
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline mr-1"></i> Saving…';
            lucide.createIcons();
            errEl.classList.add('hidden');

            try {
                const res = await fetch('../../api/admin/save_reunion.php', {
                    method: 'POST',
                    body: new FormData(form)
                });
                const json = await res.json();

                if (json.success) {
                    closeForm();
                    showToast(json.message, 'green');
                    await loadAll();
                } else {
                    errEl.textContent = json.message || 'Save failed';
                    errEl.classList.remove('hidden');
                }
            } catch (e) {
                errEl.textContent = 'Connection error. Please try again.';
                errEl.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<i data-lucide="save" class="w-4 h-4 inline mr-1"></i><span id="formSaveTxt">${origTxt}</span>`;
                lucide.createIcons();
            }
        }

        // ──────────────────────────────────────────────────────────
        // DELETE
        // ──────────────────────────────────────────────────────────
        function promptDelete(idx) {
            const r = _all[idx];
            _deleteId = r.reunion_id;
            document.getElementById('deleteModalMsg').textContent =
                `"${r.title}" on ${new Date(r.reunion_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })} · ${r.total_registrations} registration(s)`;
            document.getElementById('deleteModal').classList.add('open');
            lucide.createIcons();
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('open');
            _deleteId = null;
        }

        async function confirmDelete() {
            if (!_deleteId) return;
            const btn = document.getElementById('deleteConfirmBtn');
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline mr-1"></i> Deleting…';
            lucide.createIcons();

            try {
                const fd = new FormData();
                fd.append('reunion_id', _deleteId);
                const res = await fetch('../../api/admin/delete_reunion.php', { method: 'POST', body: fd });
                const json = await res.json();

                closeDeleteModal();

                if (json.success) {
                    showToast(json.message, 'green');
                    await loadAll();
                } else {
                    showToast(json.message, 'red');
                }
            } catch (e) {
                showToast('Connection error.', 'red');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i> Delete';
                lucide.createIcons();
            }
        }

        // ──────────────────────────────────────────────────────────
        // DETAIL SLIDE PANEL
        // ──────────────────────────────────────────────────────────
        async function openPanel(idx) {
            _current = _all[idx];
            const r = _current;

            document.getElementById('panelTitle').textContent = r.title;
            document.getElementById('panelSub').textContent =
                new Date(r.reunion_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' }) + ' · ' + r.venue;

            document.getElementById('detailPanel').classList.remove('panel-closed');
            document.getElementById('detailPanel').classList.add('panel-open');
            document.getElementById('overlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Load registrations for this reunion
            try {
                const res = await fetch(`../../api/admin/get_reunion_registrations.php?limit=200&page=1`);
                const json = await res.json();
                _regs = json.success ? json.data.registrations : [];
            } catch (e) { _regs = []; }

            switchTab('overview');
        }

        function closePanel() {
            document.getElementById('detailPanel').classList.add('panel-closed');
            document.getElementById('detailPanel').classList.remove('panel-open');
            document.getElementById('overlay').classList.add('hidden');
            document.body.style.overflow = '';
            _current = null; _regs = [];
        }

        function editCurrentFromPanel() {
            if (!_current) return;
            const idx = _all.findIndex(r => r.reunion_id === _current.reunion_id);
            closePanel();
            openForm(idx);
        }

        // ──────────────────────────────────────────────────────────
        // TABS
        // ──────────────────────────────────────────────────────────
        function switchTab(tab) {
            _curTab = tab;
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.toggle('tab-active', b.dataset.tab === tab);
                b.classList.toggle('tab-inactive', b.dataset.tab !== tab);
            });
            renderTab(tab);
        }

        function renderTab(tab) {
            const r = _current;
            const body = document.getElementById('panelBody');
            if (!r) return;

            if (tab === 'overview') {
                body.innerHTML = `
        <div class="grid grid-cols-2 gap-3">
            ${mStat('Total Registered', r.total_registrations, 'ticket', '#6366f1')}
            ${mStat('Paid', r.paid_count, 'check-circle', '#16a34a')}
            ${mStat('Pending', r.pending_count, 'clock', '#d97706')}
            ${mStat('Failed', r.failed_count, 'x-circle', '#dc2626')}
            ${mStat('Revenue (৳)', '৳ ' + parseFloat(r.confirmed_revenue).toLocaleString(), 'banknote', '#0ea5e9')}
            ${mStat('Attendees', parseInt(r.total_registrations) + parseInt(r.total_guests), 'users', '#a855f7')}
        </div>
        <hr class="border-slate-100">
        <div class="space-y-3">
            ${dRow('calendar', 'Date', new Date(r.reunion_date).toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }))}
            ${r.reunion_time ? dRow('clock', 'Time', r.reunion_time) : ''}
            ${dRow('map-pin', 'Venue', r.venue + (r.venue_details ? ', ' + r.venue_details : ''))}
            ${r.registration_deadline ? dRow('calendar-x', 'Deadline', new Date(r.registration_deadline).toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' })) : ''}
            ${r.food_menu ? dRow('utensils', 'Food', r.food_menu) : ''}
            ${r.activities ? dRow('music', 'Activities', r.activities) : ''}
        </div>
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Pricing</p>
            <div class="flex justify-between text-xs mb-2">
                <span class="text-slate-600">Member (Alumnus)</span>
                <span class="font-bold text-slate-900">৳ ${parseFloat(r.cost_alumnus).toLocaleString()}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-600">Spouse / Guest</span>
                <span class="font-bold text-slate-900">৳ ${parseFloat(r.cost_guest).toLocaleString()}</span>
            </div>
        </div>
        ${STATUS_BADGE[r.status] ?? ''}`;
            }

            else if (tab === 'registrants') {
                if (_regs.length === 0) {
                    body.innerHTML = `<div class="flex flex-col items-center py-16 text-slate-400">
                <i data-lucide="inbox" class="w-10 h-10 mb-3 text-slate-200"></i>
                <p class="font-medium">No registrants yet</p></div>`;
                    lucide.createIcons(); return;
                }
                body.innerHTML = `
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${_regs.length} registrant(s)</p>
        <div class="space-y-2">
        ${_regs.map((reg, i) => {
                    const av = reg.profile_photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(reg.full_name)}&background=e2e8f0&color=475569`;
                    return `<div class="flex items-center gap-3 bg-slate-50 rounded-xl px-4 py-3 border border-slate-100 hover:border-indigo-200 cursor-pointer transition"
                         onclick="openRegModal(${i})">
                <img src="${av}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shrink-0"
                     onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(reg.full_name)}&background=e2e8f0&color=475569'">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-slate-900 text-xs truncate">${reg.full_name}</div>
                    <div class="text-[10px] text-slate-400">${reg.mobile} · T-${reg.tshirt_size}</div>
                </div>
                <div class="text-right shrink-0">
                    ${BADGE[reg.payment_status] ?? ''}
                    <div class="text-[10px] text-slate-400 mt-0.5">${reg.guest_count > 0 ? '+' + reg.guest_count + ' guest' : 'Solo'}</div>
                </div>
            </div>`;
                }).join('')}
        </div>`;
            }

            else if (tab === 'tshirts') {
                const bd = r.tshirt_breakdown ?? {};
                const sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                const total = Object.values(bd).reduce((a, v) => a + parseInt(v), 0);
                const max = Math.max(...Object.values(bd), 1);
                body.innerHTML = `
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">T-Shirt Sizes (Paid Registrants)</p>
        <div class="space-y-3 pt-1">
        ${sizes.map(sz => {
                    const cnt = parseInt(bd[sz] ?? 0);
                    const pct = total > 0 ? Math.round((cnt / total) * 100) : 0;
                    const barW = Math.round((cnt / max) * 100);
                    return `
            <div class="flex items-center gap-3">
                <span class="w-8 text-xs font-black text-slate-700 text-center shrink-0">${sz}</span>
                <div class="flex-1 bg-slate-100 rounded-full h-5 overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full transition-all" style="width:${barW}%"></div>
                </div>
                <span class="w-20 text-xs text-right shrink-0 font-bold text-slate-600">
                    ${cnt} <span class="font-normal text-slate-400">(${pct}%)</span>
                </span>
            </div>`;
                }).join('')}
        </div>
        <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100 text-xs text-indigo-700">
            <strong>Total: ${total} t-shirts</strong> (paid registrations only — guest shirts tracked per-person in registrant detail)
        </div>`;
            }

            lucide.createIcons();
        }

        // ──────────────────────────────────────────────────────────
        // REGISTRANT MODAL
        // ──────────────────────────────────────────────────────────
        function openRegModal(idx) {
            const r = _regs[idx];
            if (!r) return;
            const guests = r.guests_data ?? [];
            const gHtml = guests.length
                ? guests.map((g, i) => `
            <div class="flex items-center gap-3 bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                <span class="text-xs font-bold text-slate-400 w-4">${i + 2}</span>
                <span class="flex-1 font-semibold text-slate-800 text-xs">${g.name || '—'} <span class="text-slate-400">${g.gender ?? ''}</span></span>
                <span class="text-[11px] font-bold bg-slate-200 text-slate-600 px-2 py-0.5 rounded">${g.tshirt ?? ''}</span>
            </div>`).join('')
                : '<p class="text-slate-400 text-xs italic">No additional guests</p>';

            document.getElementById('regModalBody').innerHTML = `
        <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
            <img src="${r.profile_photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(r.full_name)}&background=0f172a&color=fff`}"
                 class="w-14 h-14 rounded-full object-cover border-2 border-slate-200"
                 onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(r.full_name)}&background=0f172a&color=fff'">
            <div>
                <h3 class="font-extrabold text-slate-900">${r.full_name}</h3>
                <p class="text-xs text-slate-500">${r.mobile}</p>
                <div class="flex gap-1.5 mt-1">
                    <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">${r.ticket_number}</span>
                    ${BADGE[r.payment_status] ?? ''}
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs">
            ${mCell2('Gender', r.gender ?? '—')}
            ${mCell2('T-Shirt', r.tshirt_size)}
            ${mCell2('Amount', '৳ ' + parseFloat(r.total_amount).toLocaleString())}
            ${mCell2('Transaction', r.transaction_id || '—')}
            ${mCell2('Division', r.current_location || '—')}
            ${mCell2('Blood Group', r.blood_group || '—')}
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Full Guest List (${guests.length + 1})</p>
            <div class="mb-1.5 flex items-center gap-3 bg-slate-900 rounded-lg px-3 py-2 text-white">
                <span class="text-xs font-bold text-slate-400 w-4">1</span>
                <span class="flex-1 text-xs font-semibold">${r.full_name} <span class="text-slate-400">(Member)</span></span>
                <span class="text-[11px] font-bold bg-slate-700 text-slate-300 px-2 py-0.5 rounded">${r.tshirt_size}</span>
            </div>
            ${gHtml}
        </div>`;

            document.getElementById('regModal').classList.add('open');
            lucide.createIcons();
        }

        function closeRegModal() {
            document.getElementById('regModal').classList.remove('open');
        }

        // ──────────────────────────────────────────────────────────
        // TOAST
        // ──────────────────────────────────────────────────────────
        function showToast(msg, color = 'green') {
            const t = document.createElement('div');
            const bg = color === 'green' ? 'bg-green-600' : 'bg-red-600';
            t.className = `fixed bottom-6 right-6 z-[60] ${bg} text-white text-sm font-bold px-5 py-3 rounded-xl shadow-xl flex items-center gap-2 transition`;
            t.innerHTML = `<i data-lucide="${color === 'green' ? 'check-circle' : 'alert-circle'}" class="w-4 h-4"></i>${msg}`;
            document.body.appendChild(t);
            lucide.createIcons();
            setTimeout(() => t.remove(), 4000);
        }

        // ──────────────────────────────────────────────────────────
        // HELPERS
        // ──────────────────────────────────────────────────────────
        function mStat(label, value, icon, color) {
            return `<div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
        <div class="flex items-center gap-2 mb-1"><i data-lucide="${icon}" class="w-3.5 h-3.5" style="color:${color}"></i>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${label}</span></div>
        <p class="text-lg font-extrabold text-slate-900">${value}</p></div>`;
        }
        function dRow(icon, label, value) {
            return `<div class="flex items-start gap-3">
        <i data-lucide="${icon}" class="w-4 h-4 text-slate-400 mt-0.5 shrink-0"></i>
        <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${label}</p>
        <p class="text-xs text-slate-700 mt-0.5 leading-relaxed">${value}</p></div></div>`;
        }
        function mCell2(label, value) {
            return `<div class="bg-slate-50 rounded-lg p-2.5 border border-slate-100">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${label}</p>
        <p class="font-semibold text-slate-800 truncate mt-0.5">${value}</p></div>`;
        }

        // ──────────────────────────────────────────────────────────
        // INIT
        // ──────────────────────────────────────────────────────────
        loadAll();
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.add('tab-inactive'));
    </script>
</body>

</html>