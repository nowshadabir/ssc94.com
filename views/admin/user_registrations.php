<?php
/**
 * User Registrations Management
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
    <title>User Registrations | Admin Portal — SSC Batch '94</title>
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
                class="flex items-center gap-3 px-4 py-2.5 text-white bg-slate-800 rounded-xl text-sm font-semibold">
                <i data-lucide="user-plus" class="w-4 h-4 text-yellow-400"></i> User Registrations
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
                <h1 class="text-sm font-bold text-slate-900">User Registrations</h1>
                <p class="text-[11px] text-slate-400">Monitor new member signups and referrals</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="refreshData()"
                    class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-900 text-xs font-bold px-4 py-2 rounded-xl transition">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i> Refresh
                </button>
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-slate-900">
                        <?php echo htmlspecialchars($adminName); ?>
                    </p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        <?php echo htmlspecialchars($adminRole); ?>
                    </p>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($adminName); ?>&background=0f172a&color=fff&size=80"
                    class="w-9 h-9 rounded-full border-2 border-slate-200" alt="Admin">
            </div>
        </header>

        <div class="p-6 lg:p-8 space-y-8 overflow-y-auto">

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900" id="stat-total">0</p>
                    <p class="text-xs text-slate-500 mt-1">Total Users</p>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-green-600" id="stat-active">0</p>
                    <p class="text-xs text-slate-500 mt-1">Active</p>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-yellow-600" id="stat-pending">0</p>
                    <p class="text-xs text-slate-500 mt-1">Pending Payment</p>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
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
                    <table class="w-full text-left">
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
                            <!-- Loading state -->
                            <tr id="loading-row">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <i data-lucide="loader-2" class="w-6 h-6 text-slate-300 animate-spin"></i>
                                        <span class="text-slate-400 italic">Loading users...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- No Results -->
            <div id="no-results" class="hidden bg-white rounded-2xl border border-slate-100 p-16 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="inbox" class="w-10 h-10 text-slate-200"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-600 mb-1">No users found</h3>
                <p class="text-sm text-slate-400">Try adjusting your filters or search terms</p>
            </div>
        </div>

    </main>

    <script>
        lucide.createIcons();

        let allUsers = [];
        let filteredUsers = [];

        // Load users on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadUsers();
        });

        // Load users from API
        async function loadUsers() {
            try {
                const response = await fetch('../../api/admin/get_registrations.php');
                const data = await response.json();

                if (data.success) {
                    allUsers = data.users;
                    filteredUsers = allUsers;
                    updateStats(data.stats);
                    displayUsers(filteredUsers);
                } else {
                    showError('Failed to load users');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showError('Error loading users');
            }
        }

        // Update statistics
        function updateStats(stats) {
            document.getElementById('stat-total').textContent = (stats.total || 0).toLocaleString();
            document.getElementById('stat-active').textContent = (stats.active || 0).toLocaleString();
            document.getElementById('stat-pending').textContent = (stats.pending || 0).toLocaleString();
            document.getElementById('stat-referrals').textContent = (stats.with_referrals || 0).toLocaleString();
        }

        // Display users in table
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
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="${user.profile_photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.full_name) + '&background=e2e8f0&color=475569'}" 
                                class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0"
                                alt="${user.full_name}">
                            <div>
                                <div class="font-bold text-slate-900 text-xs">${user.full_name}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">ID: ${user.user_id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs font-bold text-slate-900">${user.mobile}</div>
                        <div class="text-[11px] text-slate-500">${user.email}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <code class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-lg font-mono text-[11px] font-bold">
                                ${user.referral_code || 'N/A'}
                            </code>
                            ${user.referral_code ? `
                                <button onclick="copyReferralCode('${user.referral_code}')" 
                                    class="text-slate-300 hover:text-indigo-600 transition" title="Copy code">
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        ${user.referred_by_name ? `
                            <div class="flex items-center gap-2 text-xs text-slate-700">
                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-purple-600"></i>
                                <span class="font-medium">${user.referred_by_name}</span>
                            </div>
                        ` : '<span class="text-slate-400 text-[11px] italic">Direct signup</span>'}
                    </td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-${user.status} text-[10px] uppercase tracking-widest px-2.5 py-1">
                            ${user.status === 'active' ? '🟢 Active' : '🟡 Pending'}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        ${user.payment_status ? `
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">${user.payment_status}</div>
                                ${user.payment_amount ? `<div class="text-[11px] text-slate-500 font-medium">৳${user.payment_amount}</div>` : ''}
                            </div>
                        ` : '<span class="text-slate-400 text-xs">—</span>'}
                    </td>
                    <td class="px-6 py-4 text-[11px] text-slate-500 font-medium">
                        ${formatDate(user.created_at)}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            ${user.status === 'pending' ? `
                                <button onclick="markAsPaid(${user.user_id}, '${user.full_name}')" 
                                    class="h-8 px-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold text-[11px] flex items-center gap-1 transition shadow-sm">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    Paid
                                </button>
                            ` : ''}
                            <button onclick="viewUser(${user.user_id})" 
                                class="h-8 px-3 bg-slate-900 hover:bg-black text-white rounded-lg font-bold text-[11px] transition shadow-sm">
                                View
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            lucide.createIcons();
        }

        // Filter users
        function filterUsers() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;
            const referralFilter = document.getElementById('referral-filter').value;

            filteredUsers = allUsers.filter(user => {
                // Search filter
                const matchesSearch = !searchTerm ||
                    user.full_name.toLowerCase().includes(searchTerm) ||
                    user.mobile.includes(searchTerm) ||
                    user.email.toLowerCase().includes(searchTerm) ||
                    (user.referral_code && user.referral_code.toLowerCase().includes(searchTerm));

                // Status filter
                const matchesStatus = !statusFilter || user.status === statusFilter;

                // Referral filter
                let matchesReferral = true;
                if (referralFilter === 'with') {
                    matchesReferral = user.referred_by_name !== null;
                } else if (referralFilter === 'without') {
                    matchesReferral = user.referred_by_name === null;
                }

                return matchesSearch && matchesStatus && matchesReferral;
            });

            displayUsers(filteredUsers);
        }

        // Refresh data
        function refreshData() {
            loadUsers();
        }

        // Copy referral code
        function copyReferralCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                // Simple toast-like alert
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-2xl z-50';
                toast.textContent = '✓ Referral code copied: ' + code;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            });
        }


        // Mark user payment as complete
        async function markAsPaid(userId, userName) {
            if (!confirm(`Mark payment as complete for ${userName}?\n\nThis will activate the user account.`)) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'mark_paid');
                formData.append('user_id', userId);

                const response = await fetch('../../api/admin/mark_payment.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    loadUsers();
                } else {
                    alert('Error: ' + (result.message || 'Failed to mark payment'));
                }
            } catch (error) {
                console.error('Error marking payment:', error);
                alert('An error occurred.');
            }
        }

        // View user details
        function viewUser(userId) {
            window.location.href = `../profile.php?id=${userId}`;
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays === 0) {
                return 'Today';
            } else if (diffDays === 1) {
                return 'Yesterday';
            } else if (diffDays < 7) {
                return diffDays + ' days ago';
            } else {
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit', month: 'short', year: 'numeric'
                });
            }
        }

        // Show error
        function showError(message) {
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-red-500 font-medium italic">
                        ${message}
                    </td>
                </tr>
            `;
        }
    </script>
</body>

</html>