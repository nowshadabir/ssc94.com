<?php
/**
 * Common Admin Sidebar (Desktop & Mobile)
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- ══ DESKTOP SIDEBAR ══════════════════════════════════════════════════════ -->
<aside class="sidebar bg-slate-900 text-slate-400 flex flex-col hidden lg:flex shrink-0">
    <div class="p-6 flex items-center gap-3 border-b border-slate-800">
        <div
            class="w-9 h-9 bg-yellow-400 text-slate-900 rounded-lg flex items-center justify-center font-black text-sm">
            94</div>
        <span class="text-white font-bold tracking-wider text-sm uppercase">Admin Portal</span>
    </div>
    <nav class="flex-1 px-3 py-5 space-y-0.5">
        <?php renderNavLink('dashboard.php', 'layout-dashboard', 'Overview'); ?>

        <?php if (hasPermission('view_members')): ?>
            <?php renderNavLink('user_registrations.php', 'user-plus', 'User Registrations'); ?>
        <?php endif; ?>

        <?php if (hasPermission('view_payments')): ?>
            <?php renderNavLink('payment_gateway_settings.php', 'credit-card', 'Payment Gateway'); ?>
        <?php endif; ?>

        <?php if (hasPermission('view_reunions')): ?>
            <?php renderNavLink('reunions.php', 'party-popper', 'Reunions'); ?>
        <?php endif; ?>

        <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
            <div class="pt-4 pb-2 px-4 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Administration</div>
            <?php renderNavLink('manage_admins.php', 'shield-check', 'Manage Admins'); ?>
        <?php endif; ?>

        <div class="pt-4 pb-2 px-4 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Community</div>
        <?php renderNavLink('#', 'users', 'Batchmates'); ?>
        <?php renderNavLink('#', 'calendar', 'Events'); ?>
        <?php renderNavLink('#', 'heart-pulse', 'Blood Donors'); ?>

        <div class="h-px bg-slate-800 my-4"></div>
    </nav>
    <div class="p-4 border-t border-slate-800">
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em] mb-3 px-4">System</p>
        <a href="../../index.html"
            class="flex items-center gap-3 px-4 py-2.5 hover:text-white hover:bg-slate-800 rounded-xl transition text-sm font-medium">
            <i data-lucide="home" class="w-4 h-4 text-emerald-400"></i> Visit Public Site
        </a>
    </div>
</aside>

<!-- ══ MOBILE SIDEBAR DRAWER ══════════════════════════════════════════════════════ -->
<div id="mobileOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] lg:hidden"></div>
<aside id="mobileSidebar"
    class="fixed inset-y-0 left-0 w-[280px] bg-slate-900 text-slate-400 flex flex-col z-[101] lg:hidden">
    <div class="p-6 flex items-center justify-between border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div
                class="w-8 h-8 bg-yellow-400 text-slate-900 rounded-lg flex items-center justify-center font-black text-xs">
                94</div>
            <span class="text-white font-bold tracking-wider text-sm">ADMIN PORTAL</span>
        </div>
        <button onclick="toggleMobileSidebar(false)" class="text-slate-500 hover:text-white">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
        <?php renderNavLink('dashboard.php', 'layout-dashboard', 'Overview', true); ?>

        <?php if (hasPermission('view_members')): ?>
            <?php renderNavLink('user_registrations.php', 'user-plus', 'User Registrations', true); ?>
        <?php endif; ?>

        <?php if (hasPermission('view_payments')): ?>
            <?php renderNavLink('payment_gateway_settings.php', 'credit-card', 'Payment Gateway', true); ?>
        <?php endif; ?>

        <?php if (hasPermission('view_reunions')): ?>
            <?php renderNavLink('reunions.php', 'party-popper', 'Reunions', true); ?>
        <?php endif; ?>

        <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
            <div class="h-px bg-slate-800 my-4"></div>
            <?php renderNavLink('manage_admins.php', 'shield-check', 'Manage Admins', true); ?>
        <?php endif; ?>

        <div class="h-px bg-slate-800 my-4"></div>
        <?php renderNavLink('#', 'users', 'Batchmates', true); ?>
        <?php renderNavLink('#', 'calendar', 'Events', true); ?>
        <?php renderNavLink('#', 'heart-pulse', 'Blood Donors', true); ?>
    </nav>
    <div class="p-4 border-t border-slate-800">
        <a href="../../index.html"
            class="flex items-center gap-3 px-4 py-3 hover:text-white hover:bg-slate-800 rounded-xl transition text-sm font-medium">
            <i data-lucide="home" class="w-4 h-4 text-emerald-400"></i> Visit Public Site
        </a>
    </div>
</aside>

<?php
function renderNavLink($url, $icon, $label, $isMobile = false)
{
    global $currentPage;
    $isActive = ($currentPage === $url);
    $baseClass = "flex items-center gap-3 px-4 rounded-xl transition text-sm font-medium";
    $paddingClass = $isMobile ? "py-3" : "py-2.5";
    $activeClass = $isActive ? "text-white bg-slate-800 font-semibold" : "hover:text-white hover:bg-slate-800";
    $iconColor = $isActive ? "text-yellow-400" : "";

    echo '<a href="' . $url . '" class="' . $baseClass . ' ' . $paddingClass . ' ' . $activeClass . '">';
    echo '<i data-lucide="' . $icon . '" class="w-4 h-4 ' . $iconColor . '"></i> ' . $label;
    echo '</a>';
}
?>