<?php
/**
 * Admin Settings (Password Change) Modal
 */
?>
<!-- ══ SETTINGS MODAL ══════════════════════════════════════════════════════════ -->
<div id="settingsModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3"><i data-lucide="settings" class="w-5 h-5 text-white"></i><span
                    class="text-white font-bold text-sm uppercase tracking-wider">User Settings</span></div>
            <button onclick="closeSettingsModal()" class="text-indigo-200 hover:text-white transition"><i
                    data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <h3 class="text-slate-900 font-bold text-base">Change Password</h3>
                <p class="text-slate-400 text-[11px] mt-0.5">Maintain your account security by updating your password.
                </p>
            </div>
            <form id="settingsForm" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Current
                        Password</label>
                    <div class="relative">
                        <i data-lucide="lock"
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="password" name="current_password" required placeholder="••••••••"
                            class="w-full h-11 bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 text-sm focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">New
                        Password</label>
                    <div class="relative">
                        <i data-lucide="key-round"
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="password" name="new_password" required placeholder="••••••••"
                            class="w-full h-11 bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 text-sm focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Confirm
                        New Password</label>
                    <div class="relative">
                        <i data-lucide="shield-check"
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="password" name="confirm_password" required placeholder="••••••••"
                            class="w-full h-11 bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 text-sm focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 transition">
                    </div>
                </div>
                <div id="settingsStatus" class="hidden text-[11px] font-bold py-2 px-3 rounded-lg"></div>
                <button type="submit" id="settingsSaveBtn"
                    class="w-full bg-indigo-600 text-white h-11 rounded-xl font-bold text-sm hover:bg-indigo-700 transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20">
                    <i data-lucide="save" class="w-4 h-4"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div>