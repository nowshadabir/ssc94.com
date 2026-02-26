<?php
/**
 * User Registrations Management
 * SSC Batch '94
 */
require_once '../../config/config.php';

requireAdmin('view_members');

$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$adminRole = $_SESSION['admin_role'] ?? 'Admin';

// Page Info
$pageTitle = "User Registrations";
$pageSubtitle = "Monitor new member signups and referrals";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layout/head.php'; ?>
    <title>User Registrations | Admin Portal — SSC Batch '94</title>
    <style>
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body class="flex min-h-screen">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-1 flex flex-col min-w-0">
        <?php include 'layout/header.php'; ?>

        <div class="p-6 lg:p-8 space-y-8 overflow-y-auto">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="stat-card bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900" id="stat-total">0</p>
                    <p class="text-xs text-slate-500 mt-1">Total Users</p>
                </div>
                <div class="stat-card bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center"><i
                                data-lucide="check-circle" class="w-5 h-5"></i></div>
                    </div>
                    <p class="text-3xl font-extrabold text-green-600" id="stat-active">0</p>
                    <p class="text-xs text-slate-500 mt-1">Active</p>
                </div>
                <div class="stat-card bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-yellow-600" id="stat-pending">0</p>
                    <p class="text-xs text-slate-500 mt-1">Pending Payment</p>
                </div>
                <div class="stat-card bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="gift" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-purple-600" id="stat-referrals">0</p>
                    <p class="text-xs text-slate-500 mt-1">With Referrals</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 overflow-hidden">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex-1 min-w-[200px]">
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Search
                            Members</label>
                        <input type="text" id="search" placeholder="Search by name, mobile, or email..."
                            class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition"
                            onkeyup="filterUsers()">
                    </div>
                    <div class="w-full sm:w-auto">
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Status</label>
                        <select id="status-filter" onchange="filterUsers()"
                            class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm font-semibold focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-auto">
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Referral</label>
                        <select id="referral-filter" onchange="filterUsers()"
                            class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm font-semibold focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition">
                            <option value="">All Users</option>
                            <option value="with">With Referral</option>
                            <option value="without">Without Referral</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-inter">
                        <thead
                            class="bg-slate-50 text-[10px] text-slate-400 font-bold uppercase tracking-widest border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Contact</th>
                                <th class="px-6 py-4">Referral Code</th>
                                <th class="px-6 py-4">Referred By</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Payment</th>
                                <th class="px-6 py-4">Registered</th>
                                <th class="px-6 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body" class="divide-y divide-slate-50 text-sm">
                            <tr id="loading-row">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex items-center justify-center gap-3"><i data-lucide="loader-2"
                                            class="w-6 h-6 text-slate-300 animate-spin"></i><span
                                            class="text-slate-400 italic">Loading users...</span></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="no-results" class="hidden bg-white rounded-2xl border border-slate-100 p-16 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4"><i
                        data-lucide="inbox" class="w-10 h-10 text-slate-200"></i></div>
                <h3 class="text-lg font-bold text-slate-600 mb-1">No users found</h3>
                <p class="text-sm text-slate-400">Try adjusting your filters or search terms</p>
            </div>
        </div>
    </main>

    <?php include 'layout/settings_modal.php'; ?>
    <?php include 'layout/scripts.php'; ?>

    <script>
        let allUsers = []; let filteredUsers = [];
        document.addEventListener('DOMContentLoaded', () => loadUsers());
        async function loadUsers() {
            try {
                const res = await fetch('../../api/admin/get_registrations.php');
                const data = await res.json();
                if (data.success) { allUsers = data.users; filteredUsers = allUsers; updateStats(data.stats); displayUsers(filteredUsers); }
                else { showError('Failed to load users'); }
            } catch (error) { console.error('Error loading users:', error); showError('Error loading users'); }
        }
        function updateStats(stats) {
            document.getElementById('stat-total').textContent = (stats.total || 0).toLocaleString();
            document.getElementById('stat-active').textContent = (stats.active || 0).toLocaleString();
            document.getElementById('stat-pending').textContent = (stats.pending || 0).toLocaleString();
            document.getElementById('stat-referrals').textContent = (stats.with_referrals || 0).toLocaleString();
        }
        function displayUsers(users) {
            const tbody = document.getElementById('users-table-body'); const loadingRow = document.getElementById('loading-row'); const noResults = document.getElementById('no-results');
            loadingRow.classList.add('hidden');
            if (users.length === 0) { tbody.innerHTML = ''; noResults.classList.remove('hidden'); return; }
            noResults.classList.add('hidden');
            tbody.innerHTML = users.map(user => `
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4"><div class="flex items-center gap-3"><img src="${user.profile_photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.full_name) + '&background=e2e8f0&color=475569'}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0" alt="${user.full_name}"><div><div class="font-bold text-slate-900 text-xs">${user.full_name}</div><div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">ID: ${user.user_id}</div></div></div></td>
                    <td class="px-6 py-4"><div class="text-xs font-bold text-slate-900">${user.mobile}</div><div class="text-[11px] text-slate-500">${user.email}</div></td>
                    <td class="px-6 py-4"><div class="flex items-center gap-2"><code class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-lg font-mono text-[11px] font-bold">${user.referral_code || 'N/A'}</code>${user.referral_code ? `<button onclick="copyReferralCode('${user.referral_code}')" class="text-slate-300 hover:text-indigo-600 transition" title="Copy code"><i data-lucide="copy" class="w-3.5 h-3.5"></i></button>` : ''}</div></td>
                    <td class="px-6 py-4">${user.referred_by_name ? `<div class="flex items-center gap-2 text-xs text-slate-700"><i data-lucide="user-check" class="w-3.5 h-3.5 text-purple-600"></i><span class="font-medium">${user.referred_by_name}</span></div>` : '<span class="text-slate-400 text-[11px] italic">Direct signup</span>'}</td>
                    <td class="px-6 py-4"><span class="status-badge status-${user.status} text-[10px] uppercase tracking-widest px-2.5 py-1">${user.status === 'active' ? '🟢 Active' : '🟡 Pending'}</span></td>
                    <td class="px-6 py-4">${user.payment_status ? `<div class="text-xs"><div class="font-bold text-slate-900">${user.payment_status}</div>${user.payment_amount ? `<div class="text-[11px] text-slate-500 font-medium">৳${user.payment_amount}</div>` : ''}</div>` : '<span class="text-slate-400 text-xs">—</span>'}</td>
                    <td class="px-6 py-4 text-[11px] text-slate-500 font-medium">${formatDate(user.created_at)}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            ${user.status === 'pending' && <?= hasPermission('mark_as_paid') ? 'true' : 'false' ?> ? `<button onclick="markAsPaid(${user.user_id}, '${user.full_name}')" class="h-8 px-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold text-[11px] flex items-center gap-1 transition shadow-sm"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i>Paid</button>` : ''}
                            <button onclick="viewUser(${user.user_id})" class="h-8 px-3 bg-slate-900 hover:bg-black text-white rounded-lg font-bold text-[11px] transition shadow-sm">View</button>
                        </div>
                    </td>
                </tr>`).join('');
            lucide.createIcons();
        }
        function filterUsers() {
            const searchTerm = document.getElementById('search').value.toLowerCase(); const statusFilter = document.getElementById('status-filter').value; const referralFilter = document.getElementById('referral-filter').value;
            filteredUsers = allUsers.filter(user => {
                const matchesSearch = !searchTerm || user.full_name.toLowerCase().includes(searchTerm) || user.mobile.includes(searchTerm) || user.email.toLowerCase().includes(searchTerm) || (user.referral_code && user.referral_code.toLowerCase().includes(searchTerm));
                const matchesStatus = !statusFilter || user.status === statusFilter;
                let matchesReferral = true;
                if (referralFilter === 'with') matchesReferral = user.referred_by_name !== null; else if (referralFilter === 'without') matchesReferral = user.referred_by_name === null;
                return matchesSearch && matchesStatus && matchesReferral;
            });
            displayUsers(filteredUsers);
        }
        function copyReferralCode(code) { navigator.clipboard.writeText(code).then(() => { const toast = document.createElement('div'); toast.className = 'fixed bottom-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-2xl z-50'; toast.textContent = '✓ Referral code copied: ' + code; document.body.appendChild(toast); setTimeout(() => toast.remove(), 2000); }); }
        async function markAsPaid(userId, userName) {
            if (!confirm(`Mark payment as complete for ${userName}?\n\nThis will activate the user account.`)) return;
            try {
                const fd = new FormData(); fd.append('action', 'mark_paid'); fd.append('user_id', userId);
                const res = await fetch('../../api/admin/mark_payment.php', { method: 'POST', body: fd });
                const result = await res.json();
                if (result.success) loadUsers(); else alert('Error: ' + (result.message || 'Failed to mark payment'));
            } catch (error) { console.error('Error marking payment:', error); alert('An error occurred.'); }
        }
        function viewUser(userId) { window.location.href = `../profile.php?id=${userId}`; }
        function formatDate(dateString) { const date = new Date(dateString); const now = new Date(); const diffTime = Math.abs(now - date); const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)); if (diffDays === 0) return 'Today'; else if (diffDays === 1) return 'Yesterday'; else if (diffDays < 7) return diffDays + ' days ago'; else return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }); }
        function showError(message) { const tbody = document.getElementById('users-table-body'); tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-12 text-center text-red-500 font-medium italic">${message}</td></tr>`; }
    </script>
</body>

</html>