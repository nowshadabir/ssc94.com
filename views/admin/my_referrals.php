<?php
/**
 * My Referrals View
 * SSC Batch '94
 */
require_once '../../config/config.php';

requireAdmin();

$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$pageTitle = "My Referrals";
$pageSubtitle = "Users who registered using your Member ID";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layout/head.php'; ?>
    <title>My Referrals | Admin Portal — SSC Batch '94</title>
</head>

<body class="flex min-h-screen">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-1 flex flex-col min-w-0">
        <?php include 'layout/header.php'; ?>

        <div class="p-6 lg:p-8 space-y-8 overflow-y-auto">
            <!-- Referral Lookup Form -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="flex-1 max-w-md">
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Lookup
                            by Member ID</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <i data-lucide="search" class="absolute left-3.5 top-3.5 w-4 h-4 text-slate-400"></i>
                                <input type="text" id="member-lookup-input" placeholder="Enter 6-digit Member ID..."
                                    class="w-full h-11 pl-11 pr-4 bg-slate-50 border border-slate-100 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-slate-900/10 transition">
                            </div>
                            <button onclick="loadReferrals()"
                                class="h-11 px-6 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-black transition shadow-sm uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i> Explore
                            </button>
                        </div>
                    </div>

                    <div id="referrer-info"
                        class="hidden flex items-center gap-4 bg-indigo-50/50 border border-indigo-100/50 rounded-2xl p-4 pr-6 shrink-0">
                        <div
                            class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="user-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p
                                class="text-[9px] text-indigo-400 font-black uppercase tracking-widest leading-none mb-1">
                                Results for</p>
                            <h3 id="referrer-name"
                                class="text-sm font-extrabold text-slate-900 uppercase tracking-tight">-</h3>
                        </div>
                        <div class="ml-4 pl-4 border-l border-indigo-200/30 text-center">
                            <p id="stat-total" class="text-xl font-black text-slate-900 leading-none">0</p>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-1">Referrals</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referrals Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden text-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead
                            class="bg-slate-50 text-[10px] text-slate-400 font-bold uppercase tracking-widest border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Referred Member</th>
                                <th class="px-6 py-4">Contact Info</th>
                                <th class="px-6 py-4">Member ID</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Joined Date</th>
                            </tr>
                        </thead>
                        <tbody id="referrals-table-body" class="divide-y divide-slate-50 text-sm">
                            <tr id="loading-row">
                                <td colspan="6" class="px-6 py-12 text-center text-xs">
                                    <div class="flex items-center justify-center gap-3">
                                        <i data-lucide="loader-2" class="w-6 h-6 text-slate-300 animate-spin"></i>
                                        <span class="text-slate-400 italic font-medium">Fetching your
                                            referrals...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="no-results" class="hidden bg-white rounded-2xl border border-slate-100 p-16 text-center shadow-sm">
                <div
                    class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <i data-lucide="users" class="w-10 h-10 text-slate-200"></i>
                </div>
                <h3 class="text-lg font-extrabold text-slate-600 mb-1 uppercase tracking-tight">No referrals yet</h3>
                <p class="text-sm text-slate-400 font-medium">When users register using your Member ID, they will appear
                    here.</p>
            </div>
        </div>
    </main>

    <?php include 'layout/settings_modal.php'; ?>
    <?php include 'layout/scripts.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Add Enter key support
            document.getElementById('member-lookup-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') loadReferrals();
            });
        });

        async function loadReferrals() {
            const memberId = document.getElementById('member-lookup-input').value.trim();
            if (!memberId) {
                showToast('Please enter a Member ID', 'error');
                return;
            }

            const tbody = document.getElementById('referrals-table-body');
            const noResults = document.getElementById('no-results');
            const referrerInfo = document.getElementById('referrer-info');

            // Show loading state
            tbody.innerHTML = `
                <tr id="loading-row">
                    <td colspan="6" class="px-6 py-12 text-center text-xs">
                        <div class="flex items-center justify-center gap-3">
                            <i data-lucide="loader-2" class="w-6 h-6 text-slate-300 animate-spin"></i>
                            <span class="text-slate-400 italic font-medium">Fetching referrals...</span>
                        </div>
                    </td>
                </tr>`;
            lucide.createIcons();
            noResults.classList.add('hidden');
            referrerInfo.classList.add('hidden');

            try {
                const res = await fetch(`../../api/admin/get_my_referrals.php?member_id=${encodeURIComponent(memberId)}`);
                const data = await res.json();

                if (data.success) {
                    document.getElementById('referrer-name').textContent = data.referrer_name;
                    document.getElementById('stat-total').textContent = data.users.length;
                    referrerInfo.classList.remove('hidden');
                    displayReferrals(data.users);
                } else {
                    tbody.innerHTML = '';
                    noResults.classList.remove('hidden');
                    showToast(data.message || 'No member found', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred while fetching data');
            }
        }

        function displayReferrals(users) {
            const tbody = document.getElementById('referrals-table-body');
            const loadingRow = document.getElementById('loading-row');
            const noResults = document.getElementById('no-results');

            loadingRow.classList.add('hidden');

            if (users.length === 0) {
                tbody.innerHTML = '';
                noResults.classList.remove('hidden');
                return;
            }

            noResults.classList.add('hidden');
            tbody.innerHTML = users.map(user => `
                <tr class="hover:bg-slate-50/70 transition-colors border-b border-slate-50 last:border-0">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="${user.profile_photo || '../../assets/images/default-avatar.svg'}" 
                                 onerror="this.onerror=null;this.src='../../assets/images/default-avatar.svg';" 
                                 class="w-9 h-9 rounded-full object-cover border border-slate-200 shrink-0" alt="">
                            <div>
                                <div class="font-extrabold text-slate-900 text-[11px] uppercase tracking-tight">${user.full_name}</div>
                                <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">UID: ${user.user_id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-[11px] font-bold text-slate-900">${user.mobile}</div>
                        <div class="text-[10px] text-slate-500 truncate max-w-[120px] mt-0.5 font-medium">${user.email}</div>
                    </td>
                    <td class="px-6 py-4">
                        <code class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-lg font-mono text-[10px] font-extrabold border border-slate-200/50">${user.user_code || 'PENDING'}</code>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-widest ${user.status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100'}">
                            ${user.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-[10px] text-slate-500 font-bold uppercase tracking-tighter">${formatDate(user.created_at)}</td>
                </tr>
            `).join('');

            lucide.createIcons();
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function showError(message) {
            const tbody = document.getElementById('referrals-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-red-500 font-bold italic text-xs uppercase tracking-widest">${message}</td></tr>`;
        }
    </script>
</body>

</html>