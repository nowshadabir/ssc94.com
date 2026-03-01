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
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden text-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-inter">
                        <thead
                            class="bg-slate-50 text-[10px] text-slate-400 font-bold uppercase tracking-widest border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-xs">User</th>
                                <th class="px-6 py-4">Contact</th>
                                <th class="px-6 py-4">Registered ID</th>
                                <th class="px-6 py-4">Referred By</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Payment</th>
                                <th class="px-6 py-4 text-xs">Registered</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body" class="divide-y divide-slate-50 text-sm">
                            <tr id="loading-row">
                                <td colspan="8" class="px-6 py-12 text-center text-xs">
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

    <!-- User Details Modal -->
    <div id="userDetailsModal"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-base font-extrabold text-slate-900 uppercase tracking-tight">User Details</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition p-1"><i
                        data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                <form id="editUserForm" class="space-y-6">
                    <input type="hidden" name="user_id" id="modal-user-id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Full
                                Name</label>
                            <input type="text" name="full_name" id="modal-full-name"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-slate-900/10 transition">
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Status</label>
                            <select name="status" id="modal-status"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-slate-900/10 transition">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Mobile</label>
                            <input type="text" name="mobile" id="modal-mobile"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Email</label>
                            <input type="email" name="email" id="modal-email"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Father's
                                Name</label>
                            <input type="text" name="father_name" id="modal-father"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Mother's
                                Name</label>
                            <input type="text" name="mother_name" id="modal-mother"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Blood
                                Group</label>
                            <input type="text" name="blood_group" id="modal-blood"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Gender</label>
                            <input type="text" name="gender" id="modal-gender"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Job /
                                Business</label>
                            <input type="text" name="job_business" id="modal-job"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Institute</label>
                            <input type="text" name="institute_working_station" id="modal-institute"
                                class="w-full h-11 bg-slate-50 border border-slate-100 rounded-xl px-4 text-sm font-semibold focus:outline-none transition">
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3">
                <button onclick="closeModal()"
                    class="h-10 px-6 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-white transition uppercase tracking-widest">Close</button>
                <?php if ($adminRole === 'super_admin'): ?>
                    <button onclick="updateUser()"
                        class="h-10 px-6 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-black transition shadow-sm uppercase tracking-widest">Save
                        Changes</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'layout/settings_modal.php'; ?>
    <?php include 'layout/scripts.php'; ?>

    <script>
        let allUsers = []; let filteredUsers = [];
        const isAdmin = <?php echo ($adminRole === 'super_admin' ? 'true' : 'false'); ?>;

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
            const tbody = document.getElementById('users-table-body');
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
                                 class="w-9 h-9 rounded-full object-cover border border-slate-200 shrink-0 shadow-sm" alt="${user.full_name}">
                            <div class="min-w-0">
                                <div class="font-extrabold text-slate-900 text-[11px] truncate uppercase tracking-tight">${user.full_name}</div>
                                <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">DB-UID: ${user.user_id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-[11px] font-bold text-slate-900">${user.mobile}</div>
                        <div class="text-[10px] text-slate-500 truncate max-w-[120px] mt-0.5">${user.email}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <code class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-lg font-mono text-[10px] font-extrabold border border-slate-200/50">${user.user_id}</code>
                            <button onclick="copyReferralCode('${user.user_id}')" class="text-slate-300 hover:text-slate-600 transition" title="Copy ID">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        ${user.referred_by_name ? `
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-700">
                                <i data-lucide="user-check" class="w-3 h-3 text-indigo-500"></i>
                                <span class="font-bold truncate max-w-[100px] uppercase">${user.referred_by_name}</span>
                            </div>` :
                    '<span class="text-slate-400 text-[9px] font-bold uppercase tracking-tighter italic opacity-50">Direct Signup</span>'}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-widest ${user.status === 'active' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-amber-50 text-amber-700 border border-amber-100'}">
                            ${user.status}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        ${user.payment_status ? `
                            <div class="text-[10px]">
                                <div class="font-bold text-slate-900 uppercase tracking-widest">${user.payment_status}</div>
                                ${user.payment_amount ? `<div class="text-[10px] text-slate-500 font-extrabold mt-0.5">৳${user.payment_amount}</div>` : ''}
                            </div>` :
                    '<span class="text-slate-300 text-[10px] font-bold">&mdash;</span>'}
                    </td>
                    <td class="px-6 py-4 text-[10px] text-slate-500 font-bold uppercase tracking-tighter">${formatDate(user.created_at)}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            ${user.status === 'pending' && <?= hasPermission('mark_as_paid') ? 'true' : 'false' ?> ? `
                                <button onclick="markAsPaid(${user.user_id}, '${user.full_name}')" 
                                        class="h-7 px-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-extrabold text-[9px] uppercase tracking-widest flex items-center gap-1 transition shadow-sm shadow-green-100">
                                    <i data-lucide="check" class="w-3 h-3"></i>Paid
                                </button>` : ''}
                            <button onclick="viewUser(${user.user_id})" 
                                    class="h-7 px-3 bg-slate-900 hover:bg-black text-white rounded-lg font-extrabold text-[9px] uppercase tracking-widest transition shadow-sm shadow-slate-200">
                                View File
                            </button>
                        </div>
                    </td>
                </tr>`).join('');
            lucide.createIcons();
        }

        function filterUsers() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;
            const referralFilter = document.getElementById('referral-filter').value;

            filteredUsers = allUsers.filter(user => {
                const matchesSearch = !searchTerm ||
                    user.full_name.toLowerCase().includes(searchTerm) ||
                    user.mobile.includes(searchTerm) ||
                    user.email.toLowerCase().includes(searchTerm) ||
                    String(user.user_id).includes(searchTerm);

                const matchesStatus = !statusFilter || user.status === statusFilter;
                let matchesReferral = true;
                if (referralFilter === 'with') matchesReferral = user.referred_by_name !== null;
                else if (referralFilter === 'without') matchesReferral = user.referred_by_name === null;

                return matchesSearch && matchesStatus && matchesReferral;
            });
            displayUsers(filteredUsers);
        }

        function copyReferralCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-5 py-2.5 rounded-2xl text-[10px] font-extrabold uppercase tracking-widest shadow-2xl z-50 animate-bounce';
                toast.textContent = '✓ Copied ID: ' + code;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2500);
            });
        }

        async function markAsPaid(userId, userName) {
            if (!confirm(`Mark payment as complete for ${userName}?\n\nThis will activate the user account.`)) return;
            try {
                const fd = new FormData(); fd.append('action', 'mark_paid'); fd.append('user_id', userId);
                const res = await fetch('../../api/admin/mark_payment.php', { method: 'POST', body: fd });
                const result = await res.json();
                if (result.success) {
                    showToast('Payment verified successfully');
                    loadUsers();
                } else {
                    showToast('Error: ' + (result.message || 'Failed'), 'error');
                }
            } catch (error) { console.error('Error marking payment:', error); showToast('An error occurred.', 'error'); }
        }

        async function viewUser(userId) {
            try {
                const res = await fetch(`../../api/admin/get_user_details.php?id=${userId}`);
                const data = await res.json();
                if (data.success) {
                    const u = data.data;
                    document.getElementById('modal-user-id').value = u.user_id;
                    document.getElementById('modal-full-name').value = u.full_name;
                    document.getElementById('modal-status').value = u.status;
                    document.getElementById('modal-mobile').value = u.mobile;
                    document.getElementById('modal-email').value = u.email;
                    document.getElementById('modal-father').value = u.father_name || '';
                    document.getElementById('modal-mother').value = u.mother_name || '';
                    document.getElementById('modal-blood').value = u.blood_group || '';
                    document.getElementById('modal-gender').value = u.gender || '';
                    document.getElementById('modal-job').value = u.job_business || '';
                    document.getElementById('modal-institute').value = u.institute_working_station || '';

                    // Toggle readonly based on role
                    const inputs = document.querySelectorAll('#editUserForm input, #editUserForm select');
                    inputs.forEach(input => {
                        if (isAdmin) input.removeAttribute('readonly');
                        else input.setAttribute('readonly', true);
                    });

                    document.getElementById('userDetailsModal').classList.remove('hidden');
                    document.getElementById('userDetailsModal').classList.add('flex');
                } else {
                    showToast('Failed to load details', 'error');
                }
            } catch (e) { console.error(e); showToast('Error fetching user', 'error'); }
        }

        function closeModal() {
            document.getElementById('userDetailsModal').classList.add('hidden');
            document.getElementById('userDetailsModal').classList.remove('flex');
        }

        async function updateUser() {
            if (!isAdmin) return;
            const form = document.getElementById('editUserForm');
            const fd = new FormData(form);

            try {
                const res = await fetch('../../api/admin/update_user.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    showToast('User updated successfully');
                    closeModal();
                    loadUsers();
                } else {
                    showToast(data.message || 'Update failed', 'error');
                }
            } catch (e) { showToast('Server error', 'error'); }
        }

        function formatDate(dateString) { const date = new Date(dateString); const now = new Date(); const diffTime = Math.abs(now - date); const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)); if (diffDays === 0) return 'Today'; else if (diffDays === 1) return 'Yesterday'; else if (diffDays < 7) return diffDays + ' d ago'; else return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }); }
        function showError(message) { const tbody = document.getElementById('users-table-body'); tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-12 text-center text-red-500 font-medium italic text-xs">${message}</td></tr>`; }
    </script>
</body>


</html>