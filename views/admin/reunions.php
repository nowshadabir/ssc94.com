<?php
/**
 * Admin — Reunions Management (Full CRUD)
 * SSC Batch '94
 */
require_once '../../config/config.php';

requireAdmin('view_reunions');

$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$adminRole = $_SESSION['admin_role'] ?? 'Admin';

// Page Info
$pageTitle = "Reunions";
$pageSubtitle = "Create, edit & review all reunions";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layout/head.php'; ?>
    <title>Reunions | Admin Portal — SSC Batch '94</title>
    <style>
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

        #detailPanel {
            transition: transform .3s cubic-bezier(.4, 0, .2, 1);
        }

        .panel-closed {
            transform: translateX(100%);
        }

        .panel-open {
            transform: translateX(0);
        }
    </style>
</head>

<body class="flex min-h-screen">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-1 flex flex-col min-w-0">
        <?php include 'layout/header.php'; ?>

        <div class="p-6 lg:p-8 space-y-6 overflow-y-auto">
            <?php if (hasPermission('edit_reunions')): ?>
                <div class="flex items-center justify-between mb-4 lg:hidden">
                    <button onclick="openForm(null)"
                        class="w-full bg-slate-900 text-white text-xs font-bold px-4 py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg">
                        <i data-lucide="plus" class="w-4 h-4"></i> New Reunion
                    </button>
                </div>
            <?php endif; ?>

            <!-- Summary Cards -->
            <div id="summaryCards" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ([
                    ['id' => 'cTotal', 'icon' => 'calendar-check', 'color' => 'indigo', 'label' => 'Total Reunions'],
                    ['id' => 'cRegs', 'icon' => 'ticket', 'color' => 'yellow', 'label' => 'All Registrations'],
                    ['id' => 'cRevenue', 'icon' => 'banknote', 'color' => 'green', 'label' => 'Confirmed Revenue (৳)'],
                    ['id' => 'cGuests', 'icon' => 'users', 'color' => 'purple', 'label' => 'Total Attendees'],
                ] as $c): ?>
                    <div class="stat-card bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
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
            <div id="mainLoader" class="flex items-center justify-center py-20"><i data-lucide="loader-2"
                    class="w-8 h-8 animate-spin text-slate-300"></i></div>
            <!-- Reunion Cards -->
            <div id="reunionList" class="hidden space-y-4 font-inter"></div>
            <!-- Empty -->
            <div id="emptyState" class="hidden flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4"><i
                        data-lucide="party-popper" class="w-10 h-10 text-slate-300"></i></div>
                <p class="font-bold text-slate-600 text-lg">No reunions yet</p>
                <p class="text-sm text-slate-400 mb-6">Create the first reunion to get started.</p>
                <button onclick="openForm(null)"
                    class="bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-xl hover:bg-black transition flex items-center gap-2"><i
                        data-lucide="plus" class="w-4 h-4"></i> Create First Reunion</button>
            </div>
        </div>
    </main>

    <!-- ══ SLIDE DETAIL PANEL ════════════════════════════════════════════════════ -->
    <div id="overlay" class="fixed inset-0 bg-slate-900/40 z-30 hidden" onclick="closePanel()"></div>
    <aside id="detailPanel"
        class="panel-closed fixed top-0 right-0 h-full w-full max-w-xl bg-white z-40 shadow-2xl flex flex-col">
        <div class="bg-slate-900 px-6 py-4 flex items-center gap-3 shrink-0">
            <i data-lucide="party-popper" class="w-5 h-5 text-yellow-400"></i>
            <div class="flex-1 min-w-0">
                <p id="panelTitle" class="text-white font-bold text-sm truncate">Reunion</p>
                <p id="panelSub" class="text-slate-400 text-[11px]"></p>
            </div>
            <div class="flex items-center gap-2 shrink-0"><button id="panelEditBtn" onclick="editCurrentFromPanel()"
                    class="flex items-center gap-1.5 text-[11px] font-bold text-slate-300 hover:text-white border border-slate-700 hover:border-slate-500 px-3 py-1.5 rounded-lg transition"><i
                        data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit</button><button onclick="closePanel()"
                    class="text-slate-400 hover:text-white transition p-1"><i data-lucide="x"
                        class="w-5 h-5"></i></button></div>
        </div>
        <div class="flex border-b border-slate-100 shrink-0 bg-slate-50"><button
                class="tab-btn flex-1 py-3 text-xs font-bold uppercase tracking-widest transition" data-tab="overview"
                onclick="switchTab('overview')">Overview</button><button
                class="tab-btn flex-1 py-3 text-xs font-bold uppercase tracking-widest transition"
                data-tab="registrants" onclick="switchTab('registrants')">Registrants</button><button
                class="tab-btn flex-1 py-3 text-xs font-bold uppercase tracking-widest transition" data-tab="tshirts"
                onclick="switchTab('tshirts')">T-Shirts</button></div>
        <div id="panelBody" class="flex-1 overflow-y-auto p-6 space-y-4 text-sm"></div>
    </aside>

    <!-- ══ ADD / EDIT MODAL ══════════════════════════════════════════════════════ -->
    <div id="formModal"
        class="modal-wrap fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col">
            <div class="bg-slate-900 px-6 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3"><i data-lucide="calendar-plus"
                        class="w-5 h-5 text-yellow-400"></i><span id="formModalTitle"
                        class="text-white font-bold text-sm">New Reunion</span></div><button onclick="closeForm()"
                    class="text-slate-400 hover:text-white transition"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="reunionForm" class="overflow-y-auto p-6 space-y-5"><input type="hidden" id="formReunionId"
                    name="reunion_id" value="">
                <div><label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Reunion
                        Title *</label><input type="text" name="title" id="fTitle" required
                        placeholder="e.g. Grand Reunion 2025"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div><label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Date
                            *</label><input type="date" name="reunion_date" id="fDate" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                    <div><label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Time</label><input
                            type="text" name="reunion_time" id="fTime" placeholder="09:00 AM"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                    <div><label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Reg.
                            Deadline</label><input type="date" name="registration_deadline" id="fDeadline"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2"><label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Venue
                            Name *</label><input type="text" name="venue" id="fVenue" required
                            placeholder="e.g. Hotel Radisson Blu"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                    <div><label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Status</label><select
                            name="status" id="fStatus"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="active">🟢 Active</option>
                            <option value="inactive">⚪ Inactive</option>
                            <option value="completed">🔵 Completed</option>
                        </select></div>
                </div>
                <div><label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Venue
                        Address / Details</label><input type="text" name="venue_details" id="fVenueDetails"
                        placeholder="Full address or area"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Cost
                            — Alumnus (৳) *</label><input type="number" name="cost_alumnus" id="fCostA" required min="0"
                            placeholder="2500"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                    <div><label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Cost
                            — Guest/Spouse (৳) *</label><input type="number" name="cost_guest" id="fCostG" required
                            min="0" placeholder="1500"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Food
                            Menu / Feast</label><textarea name="food_menu" id="fFood" rows="3"
                            placeholder="e.g. Breakfast, Grand Buffet Lunch & Evening Snacks"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition resize-none"></textarea>
                    </div>
                    <div><label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Activities
                            / Entertainment</label><textarea name="activities" id="fActivities" rows="3"
                            placeholder="e.g. Live Band, Raffle Draw & Cultural Program"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition resize-none"></textarea>
                    </div>
                </div>
            </form>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0">
                <span id="formError" class="text-xs text-red-600 font-semibold hidden"></span>
                <div class="flex items-center gap-3 ml-auto"><button onclick="closeForm()"
                        class="text-slate-600 hover:text-slate-900 text-sm font-semibold px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 transition">Cancel</button><button
                        onclick="submitForm()" id="formSaveBtn"
                        class="bg-slate-900 hover:bg-black text-white text-sm font-bold px-6 py-2 rounded-xl transition flex items-center gap-2"><i
                            data-lucide="save" class="w-4 h-4"></i><span id="formSaveTxt">Save Reunion</span></button>
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
                <div class="flex gap-3"><button onclick="closeDeleteModal()"
                        class="flex-1 border border-slate-200 text-slate-700 text-sm font-semibold py-2.5 rounded-xl hover:bg-slate-50 transition">Cancel</button><button
                        onclick="confirmDelete()" id="deleteConfirmBtn"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2"><i
                            data-lucide="trash-2" class="w-4 h-4"></i> Delete</button></div>
            </div>
        </div>
    </div>

    <!-- ══ REGISTRANT DETAIL MODAL ════════════════════════════════════════════════ -->
    <div id="regModal"
        class="modal-wrap fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden max-h-[85vh] flex flex-col">
            <div class="bg-slate-900 px-6 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3"><i data-lucide="user" class="w-5 h-5 text-yellow-400"></i><span
                        class="text-white font-bold text-sm">Registrant Detail</span></div><button
                    onclick="closeRegModal()" class="text-slate-400 hover:text-white transition"><i data-lucide="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div id="regModalBody" class="overflow-y-auto p-6 space-y-4 text-sm"></div>
        </div>
    </div>

    <?php include 'layout/settings_modal.php'; ?>
    <?php include 'layout/scripts.php'; ?>

    <script>
        let _all = []; let _current = null; let _regs = []; let _curTab = 'overview'; let _deleteId = null;
        const BADGE = { completed: '<span class="badge-paid text-[10px] font-bold px-2 py-0.5 rounded-full">Paid</span>', pending: '<span class="badge-pending text-[10px] font-bold px-2 py-0.5 rounded-full">Pending</span>', failed: '<span class="badge-failed text-[10px] font-bold px-2 py-0.5 rounded-full">Failed</span>' };
        const STATUS_BADGE = { active: '<span class="badge-active text-[10px] font-bold px-2.5 py-0.5 rounded-full">🟢 Active</span>', completed: '<span class="badge-completed text-[10px] font-bold px-2.5 py-0.5 rounded-full">🔵 Completed</span>', inactive: '<span class="badge-inactive text-[10px] font-bold px-2.5 py-0.5 rounded-full">⚪ Inactive</span>' };
        document.addEventListener('DOMContentLoaded', () => loadAll());
        async function loadAll() {
            document.getElementById('mainLoader').classList.remove('hidden'); document.getElementById('reunionList').classList.add('hidden'); document.getElementById('emptyState').classList.add('hidden');
            try {
                const res = await fetch('../../api/admin/get_all_reunions.php'); const json = await res.json(); document.getElementById('mainLoader').classList.add('hidden');
                if (!json.success || json.data.length === 0) { document.getElementById('emptyState').classList.remove('hidden'); resetSummary(); return; }
                _all = json.data;
                const totRegs = _all.reduce((a, r) => a + parseInt(r.total_registrations), 0); const totRevenue = _all.reduce((a, r) => a + parseFloat(r.confirmed_revenue), 0); const totGuests = _all.reduce((a, r) => a + parseInt(r.total_registrations) + parseInt(r.total_guests), 0);
                document.getElementById('cTotal').textContent = _all.length; document.getElementById('cRegs').textContent = totRegs.toLocaleString(); document.getElementById('cRevenue').textContent = '৳ ' + totRevenue.toLocaleString(); document.getElementById('cGuests').textContent = totGuests.toLocaleString();
                const list = document.getElementById('reunionList'); list.innerHTML = _all.map((r, idx) => renderCard(r, idx)).join(''); list.classList.remove('hidden'); lucide.createIcons();
            } catch (e) { console.error(e); }
        }
        function resetSummary() { ['cTotal', 'cRegs', 'cRevenue', 'cGuests'].forEach(id => document.getElementById(id).textContent = '0'); }
        function renderCard(r, idx) {
            const d = new Date(r.reunion_date); const paid = parseInt(r.paid_count); const pct = r.total_registrations > 0 ? Math.round((paid / r.total_registrations) * 100) : 0;
            const canEdit = <?= hasPermission('edit_reunions') ? 'true' : 'false' ?>;
            const canDelete = <?= hasPermission('delete_reunions') ? 'true' : 'false' ?>;
            const editBtn = canEdit ? `<button onclick="event.stopPropagation(); openForm(${idx})" class="text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 px-3 py-1.5 rounded-lg font-bold transition">Edit</button>` : '';
            const deleteBtn = canDelete ? `<button onclick="event.stopPropagation(); promptDelete(${idx})" class="text-red-700 bg-red-50 hover:bg-red-100 border border-red-100 px-3 py-1.5 rounded-lg font-bold transition">Delete</button>` : '';

            return `<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden group hover:shadow-md transition"><div class="flex items-start gap-5 p-6 cursor-pointer" onclick="openPanel(${idx})"><div class="shrink-0 w-16 text-center bg-slate-50 rounded-xl p-2 border border-slate-100"><div class="text-3xl font-black text-slate-900 leading-none">${d.getDate()}</div><div class="text-[10px] font-bold text-slate-500 uppercase mt-0.5">${d.toLocaleString('en-GB', { month: 'short' })}</div><div class="text-[10px] text-slate-400">${d.getFullYear()}</div></div><div class="flex-1 min-w-0"><div class="flex flex-wrap items-start gap-2 mb-1"><h3 class="font-extrabold text-slate-900 text-base leading-tight">${r.title}</h3>${STATUS_BADGE[r.status] ?? ''}</div><p class="text-xs text-slate-500 flex items-center gap-1"><i data-lucide="map-pin" class="w-3 h-3 shrink-0"></i><span class="truncate">${r.venue}${r.venue_details ? ' — ' + r.venue_details : ''}</span></p><div class="flex flex-wrap gap-4 mt-3 text-xs"><span class="text-slate-600"><strong class="text-slate-900">${r.total_registrations}</strong> regs</span><span class="text-slate-600"><strong class="text-green-700">${r.paid_count}</strong> paid</span><span class="text-slate-600"><strong class="text-slate-900">৳ ${parseFloat(r.confirmed_revenue).toLocaleString()}</strong></span></div><div class="mt-3"><div class="w-full bg-slate-100 rounded-full h-1.5"><div class="bg-green-500 h-1.5 rounded-full" style="width:${pct}%"></div></div></div></div><i data-lucide="chevron-right" class="w-5 h-5 text-slate-300 shrink-0 mt-1"></i></div><div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center gap-4 text-[11px] text-slate-500"><span>💰 Member <strong class="text-slate-800">৳ ${parseFloat(r.cost_alumnus).toLocaleString()}</strong></span><div class="ml-auto flex items-center gap-2">${editBtn}${deleteBtn}</div></div></div>`;
        }
        function openForm(idx) {
            const form = document.getElementById('reunionForm'); form.reset(); document.getElementById('formError').classList.add('hidden');
            if (idx !== null) { const r = _all[idx]; document.getElementById('formModalTitle').textContent = 'Edit Reunion'; document.getElementById('formSaveTxt').textContent = 'Save Changes'; document.getElementById('formReunionId').value = r.reunion_id; document.getElementById('fTitle').value = r.title; document.getElementById('fDate').value = r.reunion_date; document.getElementById('fTime').value = r.reunion_time; document.getElementById('fDeadline').value = r.registration_deadline; document.getElementById('fVenue').value = r.venue; document.getElementById('fVenueDetails').value = r.venue_details; document.getElementById('fStatus').value = r.status; document.getElementById('fCostA').value = r.cost_alumnus; document.getElementById('fCostG').value = r.cost_guest; document.getElementById('fFood').value = r.food_menu; document.getElementById('fActivities').value = r.activities; }
            else { document.getElementById('formModalTitle').textContent = 'New Reunion'; document.getElementById('formSaveTxt').textContent = 'Create Reunion'; document.getElementById('formReunionId').value = ''; }
            document.getElementById('formModal').classList.add('open'); document.body.style.overflow = 'hidden'; lucide.createIcons();
        }
        function closeForm() { document.getElementById('formModal').classList.remove('open'); document.body.style.overflow = ''; }
        async function submitForm() {
            const btn = document.getElementById('formSaveBtn'); const txtSpan = document.getElementById('formSaveTxt'); const errEl = document.getElementById('formError'); const form = document.getElementById('reunionForm'); if (!form.checkValidity()) { form.reportValidity(); return; }
            const origTxt = txtSpan.textContent; btn.disabled = true; btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline mr-1"></i> Saving…'; lucide.createIcons(); errEl.classList.add('hidden');
            try { const res = await fetch('../../api/admin/save_reunion.php', { method: 'POST', body: new FormData(form) }); const json = await res.json(); if (json.success) { closeForm(); await loadAll(); } else { errEl.textContent = json.message; errEl.classList.remove('hidden'); } }
            catch (e) { errEl.textContent = 'Connection error.'; errEl.classList.remove('hidden'); } finally { btn.disabled = false; btn.innerHTML = `<i data-lucide="save" class="w-4 h-4 inline mr-1"></i><span id="formSaveTxt">${origTxt}</span>`; lucide.createIcons(); }
        }
        function promptDelete(idx) { _deleteId = _all[idx].reunion_id; document.getElementById('deleteModalMsg').textContent = `"${_all[idx].title}" · ${_all[idx].total_registrations} regs`; document.getElementById('deleteModal').classList.add('open'); lucide.createIcons(); }
        function closeDeleteModal() { document.getElementById('deleteModal').classList.remove('open'); }
        async function confirmDelete() {
            const btn = document.getElementById('deleteConfirmBtn'); btn.disabled = true; btn.innerHTML = '…';
            try { const fd = new FormData(); fd.append('reunion_id', _deleteId); const res = await fetch('../../api/admin/delete_reunion.php', { method: 'POST', body: fd }); const json = await res.json(); closeDeleteModal(); if (json.success) await loadAll(); } finally { btn.disabled = false; btn.innerHTML = 'Delete'; }
        }
        async function openPanel(idx) {
            _current = _all[idx]; document.getElementById('panelTitle').textContent = _current.title; document.getElementById('panelSub').textContent = _current.venue;
            document.getElementById('detailPanel').classList.remove('panel-closed'); document.getElementById('detailPanel').classList.add('panel-open'); document.getElementById('overlay').classList.remove('hidden'); document.body.style.overflow = 'hidden';
            try { const res = await fetch(`../../api/admin/get_reunion_registrations.php?limit=200`); const json = await res.json(); _regs = json.success ? json.data.registrations : []; } catch (e) { _regs = []; }
            switchTab('overview');
        }
        function closePanel() { document.getElementById('detailPanel').classList.add('panel-closed'); document.getElementById('detailPanel').classList.remove('panel-open'); document.getElementById('overlay').classList.add('hidden'); document.body.style.overflow = ''; }
        function editCurrentFromPanel() { const idx = _all.findIndex(r => r.reunion_id === _current.reunion_id); closePanel(); openForm(idx); }
        function switchTab(tab) { _curTab = tab; document.querySelectorAll('.tab-btn').forEach(b => b.classList.toggle('tab-active', b.dataset.tab === tab)); renderTab(tab); }
        function renderTab(tab) {
            const r = _current; const body = document.getElementById('panelBody'); if (!r) return;
            if (tab === 'overview') {
                body.innerHTML = `<div class="grid grid-cols-2 gap-3">${mStat('Registered', r.total_registrations, 'ticket', '#6366f1')}${mStat('Paid', r.paid_count, 'check-circle', '#16a34a')}${mStat('Pending', r.pending_count, 'clock', '#d97706')}${mStat('Revenue', '৳ ' + parseFloat(r.confirmed_revenue).toLocaleString(), 'banknote', '#0ea5e9')}</div><hr class="border-slate-100"><div class="space-y-3">${dRow('calendar', 'Date', r.reunion_date)}${dRow('map-pin', 'Venue', r.venue)}${r.food_menu ? dRow('utensils', 'Food', r.food_menu) : ''}</div>`;
            } else if (tab === 'registrants') {
                if (_regs.length === 0) body.innerHTML = '<p class="text-slate-400 italic">No registrants found.</p>';
                else body.innerHTML = `<div class="divide-y divide-slate-50">${_regs.map(u => `<div class="py-3 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition px-2 rounded-lg" onclick="viewRegDetail(${u.id})"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-[10px] text-slate-500">${u.full_name[0]}</div><div><p class="font-bold text-slate-900 leading-tight">${u.full_name}</p><p class="text-[10px] text-slate-400 font-medium">${u.ticket_number} · ${u.tshirt_size}</p></div></div>${BADGE[u.payment_status] ?? ''}</div>`).join('')}</div>`;
            } else if (tab === 'tshirts') {
                const sizes = {}; _regs.forEach(u => { sizes[u.tshirt_size] = (sizes[u.tshirt_size] || 0) + 1; (u.guests_data || []).forEach(g => { sizes[g.tshirt] = (sizes[g.tshirt] || 0) + 1; }); });
                body.innerHTML = `<div class="space-y-3">${Object.entries(sizes).sort().map(([s, c]) => `<div class="flex items-center justify-between bg-white border border-slate-100 p-3 rounded-xl shadow-sm"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-slate-900 text-white flex items-center justify-center font-black text-xs">${s}</div><p class="font-bold text-slate-700">Size ${s}</p></div><p class="text-xl font-black text-slate-900">${c}</p></div>`).join('')}</div>`;
            }
            lucide.createIcons();
        }
        function mStat(l, v, i, c) { return `<div class="bg-slate-50 rounded-xl p-4 border border-slate-100 transition hover:bg-white hover:shadow-sm"><div class="w-8 h-8 rounded-lg flex items-center justify-center mb-3" style="background:${c}15; color:${c}"><i data-lucide="${i}" class="w-4 h-4"></i></div><p class="text-lg font-black text-slate-900">${v}</p><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">${l}</p></div>`; }
        function dRow(i, l, v) { return `<div class="flex items-start gap-3"><div class="mt-1 text-slate-300"><i data-lucide="${i}" class="w-4 h-4"></i></div><div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${l}</p><p class="text-sm font-semibold text-slate-700">${v}</p></div></div>`; }
        function showToast(m, c) { const t = document.createElement('div'); t.className = `fixed bottom-8 left-1/2 -translate-x-1/2 px-5 py-2.5 rounded-2xl shadow-2xl z-[100] text-sm font-bold text-white ${c === 'green' ? 'bg-emerald-600' : 'bg-red-600'}`; t.textContent = m; document.body.appendChild(t); setTimeout(() => t.remove(), 3000); }
    </script>
</body>

</html>