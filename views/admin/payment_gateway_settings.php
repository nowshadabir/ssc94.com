<?php
/**
 * Payment Gateway Settings
 * SSC Batch '94
 */
require_once '../../config/config.php';

requireAdmin('view_payments');

$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$adminRole = $_SESSION['admin_role'] ?? 'Admin';

// Page Info
$pageTitle = "Payment Gateway";
$pageSubtitle = "Manage and configure active gateways";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layout/head.php'; ?>
    <title>Payment Gateway | Admin Portal — SSC Batch '94</title>
</head>

<body class="flex min-h-screen">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-1 flex flex-col min-w-0">
        <?php include 'layout/header.php'; ?>

        <div class="p-6 lg:p-8 space-y-8 overflow-y-auto">
            <!-- Active Gateway Status -->
            <div id="activeGatewayCard"
                class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl shadow-lg p-8 text-white border border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1.5">Currently
                            Active Gateway</p>
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
                            Inactive</div>
                    </div>
                    <div class="p-6">
                        <form id="rupantorpay-form" class="space-y-4">
                            <div><label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">API
                                    Key</label><input type="text" name="api_key" id="rupantorpay-api-key"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition"
                                    placeholder="Enter your API key"></div>
                            <div><label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Success
                                    URL</label><input type="url" name="success_url" id="rupantorpay-success-url"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition"
                                    placeholder="https://yourdomain.com/success"></div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div><label
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Cancel
                                        URL</label><input type="url" name="cancel_url" id="rupantorpay-cancel-url"
                                        class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition"
                                        placeholder=".../cancel"></div>
                                <div><label
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Webhook
                                        URL</label><input type="url" name="webhook_url" id="rupantorpay-webhook-url"
                                        class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition"
                                        placeholder=".../webhook"></div>
                            </div>
                            <?php if (hasPermission('manage_payments')): ?>
                                <div class="flex gap-3 pt-4">
                                    <button type="button" onclick="activateGateway('rupantorpay')"
                                        class="flex-1 bg-green-600 text-white h-11 rounded-xl font-bold text-sm hover:bg-green-700 transition shadow-sm flex items-center justify-center gap-2"><i
                                            data-lucide="zap" class="w-4 h-4"></i> Activate</button>
                                    <button type="submit"
                                        class="flex-1 bg-slate-900 text-white h-11 rounded-xl font-bold text-sm hover:bg-black transition shadow-sm flex items-center justify-center gap-2"><i
                                            data-lucide="save" class="w-4 h-4"></i> Save Settings</button>
                                </div>
                            <?php else: ?>
                                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-[11px] text-slate-500 font-bold uppercase tracking-widest text-center">
                                    <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i> Read-only access
                                </div>
                                <script>document.querySelectorAll('#rupantorpay-form input').forEach(i => i.disabled = true);</script>
                            <?php endif; ?>
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
                            Inactive</div>
                    </div>
                    <div class="p-6">
                        <form id="bkash-form" class="space-y-4">
                            <div><label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">API
                                    Key</label><input type="text" name="api_key" id="bkash-api-key"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-[#d23460] focus:ring-2 focus:ring-[#d23460]/10 transition"
                                    placeholder="Enter your API key"></div>
                            <div><label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">API
                                    Secret</label><input type="password" name="api_secret" id="bkash-api-secret"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-[#d23460] focus:ring-2 focus:ring-[#d23460]/10 transition"
                                    placeholder="Enter your API secret"></div>
                            <div><label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Merchant
                                    Number</label><input type="text" name="merchant_number" id="bkash-merchant-number"
                                    class="w-full h-11 border border-slate-200 rounded-xl px-4 text-sm focus:outline-none focus:border-[#d23460] focus:ring-2 focus:ring-[#d23460]/10 transition"
                                    placeholder="Enter merchant number"></div>
                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 shrink-0"></i>
                                <p class="text-[11px] text-amber-800 leading-relaxed font-medium"><strong>Merchant
                                        Support:</strong> bKash production requires manual whitelisting. Contact bKash
                                    support to enable your domain.</p>
                            </div>
                            <?php if (hasPermission('manage_payments')): ?>
                                <div class="flex gap-3 pt-4">
                                    <button type="button" onclick="activateGateway('bkash')"
                                        class="flex-1 bg-green-600 text-white h-11 rounded-xl font-bold text-sm hover:bg-green-700 transition shadow-sm flex items-center justify-center gap-2"><i
                                            data-lucide="zap" class="w-4 h-4"></i> Activate</button>
                                    <button type="submit"
                                        class="flex-1 bg-slate-900 text-white h-11 rounded-xl font-bold text-sm hover:bg-black transition shadow-sm flex items-center justify-center gap-2"><i
                                            data-lucide="save" class="w-4 h-4"></i> Save Settings</button>
                                </div>
                            <?php else: ?>
                                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-[11px] text-slate-500 font-bold uppercase tracking-widest text-center mt-4">
                                    <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i> Read-only access
                                </div>
                                <script>document.querySelectorAll('#bkash-form input').forEach(i => i.disabled = true);</script>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Setup Info -->
            <div class="bg-white rounded-2xl border border-slate-100 p-8">
                <h3 class="text-sm font-black text-slate-900 mb-6 flex items-center gap-2 uppercase tracking-widest"><i
                        data-lucide="info" class="w-4 h-4 text-indigo-500"></i> Setup Instructions</h3>
                <div class="grid sm:grid-cols-3 gap-8 text-[11px]">
                    <div class="space-y-2">
                        <p class="font-bold text-slate-900 uppercase">1. Rupantorpay</p>
                        <p class="text-slate-500 leading-relaxed">Enter your API key from the Rupantorpay merchant
                            dashboard. Ensure callback URLs match your actual domain.</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-bold text-slate-900 uppercase">2. bKash</p>
                        <p class="text-slate-500 leading-relaxed">Requires App Key, App Secret, Username and Password.
                            For live production, you must submit your domain for verification.</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-bold text-slate-900 uppercase">3. Activation</p>
                        <p class="text-slate-500 leading-relaxed">Switching gateways is instant. All registration links
                            on the member side will automatically use the active gateway.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'layout/settings_modal.php'; ?>
    <?php include 'layout/scripts.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => loadGatewaySettings());
        async function loadGatewaySettings() {
            try {
                const res = await fetch('../../api/admin/payment_gateway_settings.php?action=get_gateways');
                const result = await res.json();
                if (result.success && result.data.gateways) {
                    result.data.gateways.forEach(g => {
                        const name = g.gateway_name; const isActive = g.is_active == 1;
                        const statusEl = document.getElementById(`${name}-status`);
                        if (statusEl) {
                            statusEl.textContent = isActive ? 'Active' : 'Inactive';
                            statusEl.className = isActive ? 'px-2.5 py-1 bg-green-500 rounded-full text-[10px] font-black uppercase tracking-widest text-white ring-2 ring-green-500/20' : 'px-2.5 py-1 bg-white/10 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/10';
                        }
                        const cardEl = document.getElementById(`${name}-card`);
                        if (cardEl && isActive) cardEl.classList.add('ring-2', 'ring-green-500', 'border-transparent');
                        if (name === 'rupantorpay') {
                            document.getElementById('rupantorpay-api-key').value = g.api_key || ''; document.getElementById('rupantorpay-success-url').value = g.success_url || ''; document.getElementById('rupantorpay-cancel-url').value = g.cancel_url || ''; document.getElementById('rupantorpay-webhook-url').value = g.webhook_url || '';
                        } else if (name === 'bkash') {
                            document.getElementById('bkash-api-key').value = g.api_key || ''; document.getElementById('bkash-api-secret').value = g.api_secret || ''; document.getElementById('bkash-merchant-number').value = g.merchant_number || '';
                        }
                        if (isActive) document.getElementById('activeGatewayName').textContent = name.charAt(0).toUpperCase() + name.slice(1);
                    });
                }
            } catch (error) { console.error('Error loading gateway settings:', error); showNotification('Failed to load gateway settings', 'error'); }
        }
        async function activateGateway(gatewayName) {
            if (!confirm(`Switch to ${gatewayName.toUpperCase()}?`)) return;
            try {
                const fd = new FormData(); fd.append('action', 'toggle_gateway'); fd.append('gateway_name', gatewayName);
                const res = await fetch('../../api/admin/payment_gateway_settings.php', { method: 'POST', body: fd });
                const result = await res.json();
                if (result.success) { showNotification(result.message, 'success'); setTimeout(() => location.reload(), 1000); }
                else showNotification(result.message, 'error');
            } catch (error) { showNotification('Failed to activate gateway', 'error'); }
        }
        document.getElementById('rupantorpay-form').addEventListener('submit', function (e) { e.preventDefault(); saveGatewaySettings('rupantorpay', new FormData(this)); });
        document.getElementById('bkash-form').addEventListener('submit', function (e) { e.preventDefault(); saveGatewaySettings('bkash', new FormData(this)); });
        async function saveGatewaySettings(gatewayName, formData) {
            try {
                formData.append('action', 'update_gateway'); formData.append('gateway_name', gatewayName);
                const res = await fetch('../../api/admin/payment_gateway_settings.php', { method: 'POST', body: formData });
                const result = await res.json();
                if (result.success) showNotification('Settings saved successfully!', 'success'); else showNotification(result.message, 'error');
            } catch (error) { showNotification('Failed to save settings', 'error'); }
        }
        function showNotification(message, type = 'info') {
            const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-slate-900' };
            const n = document.createElement('div');
            n.className = `fixed bottom-8 right-8 ${colors[type] || colors.info} text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3 animate-fade-in`;
            n.innerHTML = `<i data-lucide="bell" class="w-4 h-4"></i><span class="text-xs font-bold uppercase tracking-widest">${message}</span>`;
            document.body.appendChild(n); lucide.createIcons();
            setTimeout(() => { n.classList.add('opacity-0', 'translate-y-4'); setTimeout(() => n.remove(), 300); }, 3000);
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