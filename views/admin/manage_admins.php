<?php
/**
 * Admin Management Page
 * Only for Super Admins
 */
require_once '../../config/config.php';

// Security: ONLY Super Admin can see this page
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

$adminName = $_SESSION['admin_name'] ?? 'Super Admin';
$adminRole = $_SESSION['admin_role'] ?? 'Super Admin';

// Page Info
$pageTitle = "Admin Management";
$pageSubtitle = "Control permissions and monitor other admin users";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layout/head.php'; ?>
    <title>Manage Admins | Admin Portal — SSC Batch '94</title>
    <style>
        /* Modal Animations & Layout */
        #permModal {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #permModal.open {
            display: flex;
            opacity: 1;
        }

        .modal-container {
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            max-height: 90vh;
        }

        #permModal.open .modal-container {
            transform: translateY(0);
        }

        @media (min-width: 640px) {
            .modal-container {
                transform: scale(0.9) translateY(20px);
                max-height: 85vh;
            }

            #permModal.open .modal-container {
                transform: scale(1) translateY(0);
            }
        }

        /* Sleek Scrollbar */
        .modal-body::-webkit-scrollbar {
            width: 5px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        .toggle-switch {
            position: relative;
            width: 52px;
            height: 28px;
            display: block;
            border-radius: 99px;
            cursor: pointer;
            background: #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            left: 3px;
            top: 3px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toggle-checkbox {
            display: none;
        }

        .toggle-checkbox:checked+.toggle-switch {
            background: #10b981;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);
        }

        .toggle-checkbox:checked+.toggle-switch::after {
            transform: translateX(24px);
            width: 18px;
        }

        .permission-row {
            transition: all 0.2s ease;
            user-select: none;
        }

        .permission-row:active {
            transform: scale(0.98);
        }

        .category-header {
            position: relative;
            padding-left: 12px;
        }

        .category-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 12px;
            background: #6366f1;
            border-radius: 99px;
        }
    </style>
</head>

<body class="flex min-h-screen bg-[#f8fafc]">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-1 flex flex-col min-w-0 font-inter">
        <?php include 'layout/header.php'; ?>

        <div class="p-6 lg:p-8 space-y-6 overflow-y-auto">
            <div id="loading" class="flex flex-col items-center justify-center py-20 text-slate-300">
                <i data-lucide="loader-2" class="w-8 h-8 animate-spin mb-3"></i>
                <p class="text-sm font-medium animate-pulse">Fetching admin accounts...</p>
            </div>

            <div id="adminList" class="hidden grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-6">
                <!-- Admins will be injected here -->
            </div>
        </div>
    </main>

    <!-- ══ PERMISSION SETTINGS MODAL ══════════════════════════════════════════ -->
    <div id="permModal"
        class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="modal-container bg-white w-full max-w-lg rounded-t-[32px] sm:rounded-[40px] shadow-2xl overflow-hidden flex flex-col">
            <!-- Modal Header - Sticky -->
            <div class="bg-white border-b border-slate-50 px-6 py-5 flex items-center justify-between shrink-0 sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-inner">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-slate-900 font-black text-sm leading-tight" id="mAdminName">—</h3>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.1em] mt-0.5" id="mAdminEmail">—</p>
                    </div>
                </div>
                <button onclick="closePermModal()"
                    class="text-slate-400 hover:text-slate-900 transition bg-slate-50 hover:bg-slate-100 p-2 rounded-full">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Content - Scrollable -->
            <div class="modal-body p-6 sm:p-8 overflow-y-auto space-y-8 flex-1">
                <div>
                    <div class="space-y-6">
                        <?php
                        $permsGrouped = [
                            'Members' => [
                                ['key' => 'view_members', 'icon' => 'users', 'color' => 'indigo', 'label' => 'View Members', 'desc' => 'Registration list access'],
                                ['key' => 'mark_as_paid', 'icon' => 'check-circle', 'color' => 'emerald', 'label' => 'Mark as Paid', 'desc' => 'Approve member signups'],
                            ],
                            'Reunions' => [
                                ['key' => 'view_reunions', 'icon' => 'party-popper', 'color' => 'amber', 'label' => 'View Reunions', 'desc' => 'See list & attendees'],
                                ['key' => 'edit_reunions', 'icon' => 'pencil', 'color' => 'blue', 'label' => 'Edit Reunions', 'desc' => 'Update event information'],
                                ['key' => 'delete_reunions', 'icon' => 'trash-2', 'color' => 'red', 'label' => 'Delete Reunions', 'desc' => 'Remove events permanently'],
                            ],
                            'Payments' => [
                                ['key' => 'view_payments', 'icon' => 'credit-card', 'color' => 'slate', 'label' => 'View Gateways', 'desc' => 'Read-only API view'],
                                ['key' => 'manage_payments', 'icon' => 'settings', 'color' => 'teal', 'label' => 'Manage Gateways', 'desc' => 'Update keys & toggles'],
                            ]
                        ];
                        foreach ($permsGrouped as $category => $perms): ?>
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="category-header text-[11px] font-black text-slate-400 uppercase tracking-widest"><?= $category ?></h4>
                                    <div class="h-px bg-slate-100 flex-1 ml-4"></div>
                                </div>
                                <div class="space-y-2">
                                    <?php foreach ($perms as $p): ?>
                                        <label
                                            class="permission-row flex items-center justify-between p-4 bg-white border border-slate-100 rounded-2xl cursor-pointer hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-500/[0.04] transition-all duration-300 group">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-11 h-11 bg-slate-50 rounded-2xl flex items-center justify-center group-hover:bg-<?= $p['color'] ?>-50 transition-colors duration-300">
                                                    <i data-lucide="<?= $p['icon'] ?>" class="w-5 h-5 text-<?= $p['color'] ?>-500"></i>
                                                </div>
                                                <div class="pr-2">
                                                    <p class="text-xs font-black text-slate-900 leading-tight mb-0.5">
                                                        <?= $p['label'] ?>
                                                    </p>
                                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                                                        <?= $p['desc'] ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="shrink-0 flex items-center gap-4">
                                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-200 group-hover:text-slate-300 transition-colors duration-300 hidden sm:inline" id="status_text_<?= $p['key'] ?>">OFF</span>
                                                <div class="relative">
                                                    <input type="checkbox" id="chk_<?= $p['key'] ?>" class="toggle-checkbox perm-checkbox"
                                                        data-perm="<?= $p['key'] ?>" onchange="updateToggleText(this, '<?= $p['key'] ?>')">
                                                    <span class="toggle-switch"></span>
                                                </div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-2">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="category-header text-[11px] font-black text-slate-400 uppercase tracking-widest">Account Status</h4>
                        <div class="h-px bg-slate-100 flex-1 ml-4"></div>
                    </div>
                    <div class="relative">
                        <select id="mAdminStatus"
                            class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl px-5 text-xs font-black uppercase tracking-widest text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer">
                            <option value="active">🟢 Account Active</option>
                            <option value="suspended">🔴 Account Suspended</option>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- Modal Footer - Sticky -->
            <div class="px-6 sm:px-8 py-5 border-t border-slate-50 bg-white/80 backdrop-blur-md flex items-center justify-between shrink-0 sticky bottom-0 z-10 mt-auto">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:block">Changes save instantly on server</p>
                <button id="saveBtn" onclick="savePermissions()"
                    class="w-full sm:w-auto bg-slate-900 hover:bg-black text-white px-8 h-12 rounded-2xl font-black text-xs uppercase tracking-[0.15em] transition duration-300 shadow-2xl shadow-slate-900/20 active:scale-95 flex items-center justify-center gap-3">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Permissions
                </button>
            </div>
        </div>
    </div>

    <!-- ══ TOAST NOTIFICATION ═════════════════════════════════════════════════════ -->
    <div id="toast"
        class="fixed bottom-8 left-1/2 -translate-x-1/2 px-6 py-3 rounded-2xl bg-slate-900 text-white text-xs font-bold shadow-2xl z-[100] hidden transition-all duration-300 translate-y-20">
    </div>

    <?php include 'layout/settings_modal.php'; ?>
    <?php include 'layout/scripts.php'; ?>

    <script>
        let allAdmins = []; let currentAdminId = null;
        document.addEventListener('DOMContentLoaded', () => loadAdmins());

        async function loadAdmins() {
            try {
                const res = await fetch('../../api/admin/get_admins.php');
                const json = await res.json();
                document.getElementById('loading').classList.add('hidden');
                if (json.success) {
                    allAdmins = json.data;
                    renderAdmins();
                } else { showToast(json.message, 'error'); }
            } catch (e) { showToast('Connection failed', 'error'); }
        }

        function renderAdmins() {
            const list = document.getElementById('adminList');
            if (allAdmins.length === 0) {
                list.innerHTML = `<div class="col-span-full py-20 text-center"><p class="text-slate-400">No admin users found.</p></div>`;
            } else {
                list.innerHTML = allAdmins.map(admin => {
                    const isSelf = admin.admin_id == <?= $_SESSION['admin_id'] ?>;
                    const isSuper = admin.role === 'super_admin';
                    const perms = admin.permissions || [];
                    const statusClass = admin.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                    return `
                        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-lg">
                                        ${admin.full_name[0]}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm leading-none">${admin.full_name} ${isSelf ? ' (You)' : ''}</h3>
                                        <p class="text-xs text-slate-400 font-medium mt-1">${admin.email}</p>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-lg ${statusClass}">${admin.status}</span>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mb-6">
                                ${isSuper ? `<span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-full border border-indigo-100 uppercase tracking-widest">🎖️ SUPER ADMIN</span>` :
                            perms.map(p => `<span class="px-3 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold rounded-full border border-slate-100 uppercase tracking-widest flex items-center gap-1">${getPermIcon(p)} ${getPermLabel(p)}</span>`).join('') || '<span class="text-[10px] text-slate-300 italic">No permissions granted</span>'}
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-widest">Last seen: ${admin.last_login ? formatDate(admin.last_login) : 'Never'}</p>
                                ${!isSuper ? `<button onclick="openPermModal(${admin.admin_id})" class="text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-100 px-4 py-2 rounded-xl text-xs font-bold transition">Set Permissions</button>` : ''}
                            </div>
                        </div>
                    `;
                }).join('');
            }
            list.classList.remove('hidden');
            lucide.createIcons();
        }

        function getPermLabel(key) {
            const map = { 
                'view_members': 'View Members', 
                'mark_as_paid': 'Mark Paid',
                'view_reunions': 'View Reunion',
                'edit_reunions': 'Edit Reunion',
                'delete_reunions': 'Delete Reunion',
                'view_payments': 'View Payments',
                'manage_payments': 'Manage Payments'
            };
            return map[key] || key;
        }
        function getPermIcon(key) {
            const map = { 
                'view_members': '👤', 
                'mark_as_paid': '✅',
                'view_reunions': '🎉',
                'edit_reunions': '✍️',
                'delete_reunions': '⚠️',
                'view_payments': '👁️',
                'manage_payments': '⚙️'
            };
            return map[key] || '🔑';
        }

        function openPermModal(id) {
            const admin = allAdmins.find(a => a.admin_id == id);
            if (!admin) return;
            currentAdminId = id;
            document.getElementById('mAdminName').textContent = admin.full_name;
            document.getElementById('mAdminEmail').textContent = admin.email;
            document.getElementById('mAdminStatus').value = admin.status;

            const perms = admin.permissions || [];
            document.querySelectorAll('.perm-checkbox').forEach(chk => {
                const isChecked = perms.includes(chk.dataset.perm);
                chk.checked = isChecked;
                updateToggleText(chk, chk.dataset.perm);
            });

            document.getElementById('permModal').classList.add('open');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        }

        function updateToggleText(chk, key) {
            const el = document.getElementById('status_text_' + key);
            if (!el) return;
            if (chk.checked) {
                el.textContent = 'ON';
                el.classList.remove('text-slate-300');
                el.classList.add('text-emerald-500');
            } else {
                el.textContent = 'OFF';
                el.classList.remove('text-emerald-500');
                el.classList.add('text-slate-300');
            }
        }

        function closePermModal() {
            document.getElementById('permModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        async function savePermissions() {
            const btn = document.getElementById('saveBtn');
            const perms = [];
            document.querySelectorAll('.perm-checkbox:checked').forEach(chk => perms.push(chk.dataset.perm));

            const status = document.getElementById('mAdminStatus').value;
            const fd = new FormData();
            fd.append('admin_id', currentAdminId);
            fd.append('status', status);
            perms.forEach(p => fd.append('permissions[]', p));

            btn.disabled = true; btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Saving…';
            lucide.createIcons();

            try {
                const res = await fetch('../../api/admin/update_admin_permissions.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    showToast('Permissions updated successfully!', 'success');
                    closePermModal();
                    loadAdmins();
                } else { showToast(json.message, 'error'); }
            } catch (e) { showToast('Connection failed', 'error'); }
            finally { btn.disabled = false; btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Save Permissions'; lucide.createIcons(); }
        }

        function showToast(m, type) {
            const t = document.getElementById('toast');
            t.textContent = m;
            t.className = `fixed bottom-8 left-1/2 -translate-x-1/2 px-6 py-3 rounded-2xl text-white text-xs font-bold shadow-2xl z-[100] transition-all duration-300 ${type === 'success' ? 'bg-emerald-500' : 'bg-red-500'}`;
            t.classList.remove('hidden');
            setTimeout(() => { t.classList.remove('translate-y-20'); t.classList.add('translate-y-0'); }, 10);
            setTimeout(() => { t.classList.add('translate-y-20'); setTimeout(() => t.classList.add('hidden'), 300); }, 3000);
        }

        function formatDate(dstr) {
            const d = new Date(dstr);
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
        }
    </script>
    <style>
        @keyframes scaleUp {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>
</body>

</html>