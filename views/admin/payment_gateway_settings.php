<?php
/**
 * Payment Gateway Settings
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
    <title>Payment Gateway | Admin Portal — SSC Batch '94</title>
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
                class="flex items-center gap-3 px-4 py-2.5 text-white bg-slate-800 rounded-xl text-sm font-semibold">
                <i data-lucide="credit-card" class="w-4 h-4 text-yellow-400"></i> Payment Gateway
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
                <h1 class="text-sm font-bold text-slate-900">Payment Gateway</h1>
                <p class="text-[11px] text-slate-400">Manage and configure active gateways</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="location.reload()"
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

            <!-- Active Gateway Status -->
            <div id="activeGatewayCard"
                class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl shadow-lg p-8 text-white border border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1.5">Currently
                            Active Gateway
                        </p>
                        <h2 id="activeGatewayName" class="text-3xl font-black">Loading...</h2>
                    </div>
                    <div
                        class="w-16 h-16 bg-indigo-500/20 text-indigo-400 rounded-full flex items-center justify-center border border-indigo-500/30">
                        <i data-lucide="shield-check" class="w-10 h-10"></i>
                    </div>
                </div>
            </div>

            <!-- Gateway Cards -->
            <div class="grid md:grid-cols-2 gap-6">

                <!-- Rupantorpay Gateway -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100 group hover:shadow-md transition"
                    id="rupantorpay-card">
                    <div class="bg-slate-900 p-6 text-white flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold">Rupantorpay</h3>
                            <p class="text-slate-400 text-[11px] font-medium tracking-wide">Digital Gateway for
                                Bangladesh</p>
                        </div>
                        <div id="rupantorpay-status"
                            class="px-2.5 py-1 bg-white/10 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/10">
                            Inactive
                        </div>
                    </div>

                    <div class="p-6">
                        <form id="rupantorpay-form" class="space-y-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">API
                                    Key</label>
                                <input type="text" name="api_key" id="rupantorpay-api-key"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition"
                                    placeholder="Enter your API key">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Success
                                    URL</label>
                                <input type="url" name="success_url" id="rupantorpay-success-url"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition"
                                    placeholder="https://yourdomain.com/success">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Cancel
                                        URL</label>
                                    <input type="url" name="cancel_url" id="rupantorpay-cancel-url"
                                        class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition"
                                        placeholder=".../cancel">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Webhook
                                        URL</label>
                                    <input type="url" name="webhook_url" id="rupantorpay-webhook-url"
                                        class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition"
                                        placeholder=".../webhook">
                                </div>
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="button" onclick="activateGateway('rupantorpay')"
                                    class="flex-1 bg-green-600 text-white h-11 rounded-xl font-bold text-sm hover:bg-green-700 transition shadow-sm flex items-center justify-center gap-2">
                                    <i data-lucide="zap" class="w-4 h-4"></i>
                                    Activate
                                </button>
                                <button type="submit"
                                    class="flex-1 bg-slate-900 text-white h-11 rounded-xl font-bold text-sm hover:bg-black transition shadow-sm flex items-center justify-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bkash Gateway -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100 group hover:shadow-md transition"
                    id="bkash-card">
                    <div class="bg-[#d23460] p-6 text-white flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold">bKash</h3>
                            <p class="text-white/70 text-[11px] font-medium tracking-wide">Mobile Financial Service</p>
                        </div>
                        <div id="bkash-status"
                            class="px-2.5 py-1 bg-white/10 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/10">
                            Inactive
                        </div>
                    </div>

                    <div class="p-6">
                        <form id="bkash-form" class="space-y-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">API
                                    Key</label>
                                <input type="text" name="api_key" id="bkash-api-key"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-[#d23460] focus:ring-2 focus:ring-[#d23460]/10 transition"
                                    placeholder="Enter your API key">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">API
                                    Secret</label>
                                <input type="password" name="api_secret" id="bkash-api-secret"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-[#d23460] focus:ring-2 focus:ring-[#d23460]/10 transition"
                                    placeholder="Enter your API secret">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Merchant
                                    Number</label>
                                <input type="text" name="merchant_number" id="bkash-merchant-number"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-[#d23460] focus:ring-2 focus:ring-[#d23460]/10 transition"
                                    placeholder="Enter merchant number">
                            </div>

                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 shrink-0"></i>
                                <p class="text-[11px] text-amber-800 leading-relaxed font-medium">
                                    <strong>Merchant Support:</strong> bKash production requires manual whitelisting.
                                    Contact bKash support to enable your domain.
                                </p>
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="button" onclick="activateGateway('bkash')"
                                    class="flex-1 bg-green-600 text-white h-11 rounded-xl font-bold text-sm hover:bg-green-700 transition shadow-sm flex items-center justify-center gap-2">
                                    <i data-lucide="zap" class="w-4 h-4"></i>
                                    Activate
                                </button>
                                <button type="submit"
                                    class="flex-1 bg-slate-900 text-white h-11 rounded-xl font-bold text-sm hover:bg-black transition shadow-sm flex items-center justify-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Setup Info -->
            <div class="bg-white rounded-2xl border border-slate-100 p-8">
                <h3 class="text-sm font-black text-slate-900 mb-6 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="info" class="w-4 h-4 text-indigo-500"></i>
                    Setup Instructions
                </h3>
                <div class="grid sm:grid-cols-3 gap-8 text-[11px]">
                    <div class="space-y-2">
                        <p class="font-bold text-slate-900 uppercase">1. Rupantorpay</p>
                        <p class="text-slate-500 leading-relaxed">Enter your API key from the Rupantorpay merchant
                            dashboard. Ensure callback URLs match your actual domain for successful verification.</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-bold text-slate-900 uppercase">2. bKash</p>
                        <p class="text-slate-500 leading-relaxed">Requires App Key, App Secret, Username and Password.
                            For live production, you must submit your domain for verification.</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-bold text-slate-900 uppercase">3. Activation</p>
                        <p class="text-slate-500 leading-relaxed">Switching gateways is instant. All registration links
                            on the member side will automatically use the active gateway selected here.</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        // Load gateway settings on page load
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
            loadGatewaySettings();
        });

        async function loadGatewaySettings() {
            try {
                const response = await fetch('../../api/admin/payment_gateway_settings.php?action=get_gateways');
                const result = await response.json();

                if (result.success && result.data.gateways) {
                    const gateways = result.data.gateways;

                    gateways.forEach(gateway => {
                        const name = gateway.gateway_name;
                        const isActive = gateway.is_active == 1;

                        // Update status badges
                        const statusEl = document.getElementById(`${name}-status`);
                        if (statusEl) {
                            statusEl.textContent = isActive ? 'Active' : 'Inactive';
                            statusEl.className = isActive
                                ? 'px-2.5 py-1 bg-green-500 rounded-full text-[10px] font-black uppercase tracking-widest text-white ring-2 ring-green-500/20'
                                : 'px-2.5 py-1 bg-white/10 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/10';
                        }

                        // Update card borders
                        const cardEl = document.getElementById(`${name}-card`);
                        if (cardEl && isActive) {
                            cardEl.classList.add('ring-2', 'ring-green-500', 'border-transparent');
                        }

                        // Populate form fields
                        if (name === 'rupantorpay') {
                            document.getElementById('rupantorpay-api-key').value = gateway.api_key || '';
                            document.getElementById('rupantorpay-success-url').value = gateway.success_url || '';
                            document.getElementById('rupantorpay-cancel-url').value = gateway.cancel_url || '';
                            document.getElementById('rupantorpay-webhook-url').value = gateway.webhook_url || '';
                        } else if (name === 'bkash') {
                            document.getElementById('bkash-api-key').value = gateway.api_key || '';
                            document.getElementById('bkash-api-secret').value = gateway.api_secret || '';
                            document.getElementById('bkash-merchant-number').value = gateway.merchant_number || '';
                        }

                        // Update active gateway display
                        if (isActive) {
                            document.getElementById('activeGatewayName').textContent = name.charAt(0).toUpperCase() + name.slice(1);
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading gateway settings:', error);
                showNotification('Failed to load gateway settings', 'error');
            }
        }

        async function activateGateway(gatewayName) {
            if (!confirm(`Switch to ${gatewayName.toUpperCase()}?`)) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'toggle_gateway');
                formData.append('gateway_name', gatewayName);

                const response = await fetch('../../api/admin/payment_gateway_settings.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error activating gateway:', error);
                showNotification('Failed to activate gateway', 'error');
            }
        }

        // Handle Rupantorpay form submission
        document.getElementById('rupantorpay-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            await saveGatewaySettings('rupantorpay', new FormData(this));
        });

        // Handle Bkash form submission
        document.getElementById('bkash-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            await saveGatewaySettings('bkash', new FormData(this));
        });

        async function saveGatewaySettings(gatewayName, formData) {
            try {
                formData.append('action', 'update_gateway');
                formData.append('gateway_name', gatewayName);

                const response = await fetch('../../api/admin/payment_gateway_settings.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Settings saved successfully!', 'success');
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                showNotification('Failed to save settings', 'error');
            }
        }

        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                info: 'bg-slate-900'
            };

            const notification = document.createElement('div');
            notification.className = `fixed bottom-8 right-8 ${colors[type]} text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3 animate-fade-in`;
            notification.innerHTML = `<i data-lucide="bell" class="w-4 h-4"></i><span class="text-xs font-bold uppercase tracking-widest">${message}</span>`;

            document.body.appendChild(notification);
            lucide.createIcons();

            setTimeout(() => {
                notification.classList.add('opacity-0', 'translate-y-4');
                setTimeout(() => notification.remove(), 300)
            }, 3000);
        }
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</body>

</html>