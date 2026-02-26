<?php
/**
 * Common Admin Header (Dropdown & Hamburger)
 */
?>
<header
    class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 shrink-0 sticky top-0 z-20">
    <div class="flex items-center gap-4">
        <!-- Desktop hidden hamburger -->
        <button onclick="toggleMobileSidebar(true)"
            class="lg:hidden w-10 h-10 flex items-center justify-center text-slate-500 hover:bg-slate-50 rounded-xl transition">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <div>
            <h1 class="text-sm font-bold text-slate-900 leading-tight">
                <?php echo $pageTitle ?? 'Dashboard'; ?>
            </h1>
            <p class="text-[11px] text-slate-400 hidden sm:block">
                <?php echo $pageSubtitle ?? "SSC Batch '94 — Manager Portal"; ?>
            </p>
            <p class="text-[10px] text-yellow-600 font-bold sm:hidden">SSC '94 Portal</p>
        </div>
    </div>

    <!-- Dropdown Menu -->
    <div class="relative">
        <button id="profileDropdownBtn"
            class="flex items-center gap-3 hover:bg-slate-50 p-1.5 rounded-2xl transition group active:scale-95">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-bold text-slate-900 leading-tight">
                    <?php echo htmlspecialchars($adminName); ?>
                </p>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                    <?php echo htmlspecialchars($adminRole); ?>
                </p>
            </div>
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($adminName); ?>&background=0f172a&color=fff&size=80"
                class="w-9 h-9 rounded-full border-2 border-slate-200" alt="Admin">
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
        </button>

        <div id="profileDropdown"
            class="hidden absolute right-0 mt-3 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl shadow-slate-200/50 py-2.5 z-[60]">
            <div class="px-5 py-3 border-b border-slate-50 mb-1.5 sm:hidden">
                <p class="text-xs font-bold text-slate-900">
                    <?php echo htmlspecialchars($adminName); ?>
                </p>
                <p class="text-[10px] text-slate-400 font-bold uppercase">
                    <?php echo htmlspecialchars($adminRole); ?>
                </p>
            </div>
            <button onclick="openSettingsModal()"
                class="w-full flex items-center gap-3 px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition font-medium">
                <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i> Settings
            </button>
            <a href="../../api/auth/logout.php"
                class="flex items-center gap-3 px-5 py-2.5 text-sm text-red-500 hover:bg-red-50 transition font-medium">
                <i data-lucide="log-out" class="w-4 h-4 text-red-400"></i> Sign Out
            </a>
        </div>
    </div>
</header>