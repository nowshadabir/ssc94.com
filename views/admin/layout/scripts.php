<?php
/**
 * Common Admin Scripts
 */
?>
<script>
    lucide.createIcons();

    // ══ SIDEBAR MOBILE logic ══════════════════════════════════════════════════
    function toggleMobileSidebar(show) {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('mobileOverlay');
        if (show) {
            sidebar.classList.add('open');
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    }
    document.getElementById('mobileOverlay').addEventListener('click', () => toggleMobileSidebar(false));

    // ══ PROFILE DROPDOWN logic ══════════════════════════════════════════════════
    const profileDropdownBtn = document.getElementById('profileDropdownBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    profileDropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (profileDropdown && !profileDropdown.contains(e.target) && !profileDropdownBtn.contains(e.target)) {
            profileDropdown.classList.add('hidden');
        }
    });

    // ══ SETTINGS MODAL logic ══════════════════════════════════════════════════
    function openSettingsModal() {
        document.getElementById('settingsModal').classList.add('open');
        document.getElementById('profileDropdown').classList.add('hidden');
        document.body.style.overflow = 'hidden';
        lucide.createIcons();
    }

    function closeSettingsModal() {
        document.getElementById('settingsModal').classList.remove('open');
        document.body.style.overflow = '';
        document.getElementById('settingsForm').reset();
        document.getElementById('settingsStatus').classList.add('hidden');
    }

    document.getElementById('settingsModal').addEventListener('click', function (e) {
        if (e.target === this) closeSettingsModal();
    });

    document.getElementById('settingsForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = document.getElementById('settingsSaveBtn');
        const status = document.getElementById('settingsStatus');
        const formData = new FormData(this);

        if (formData.get('new_password') !== formData.get('confirm_password')) {
            showSettingsStatus('✗ New passwords do not match', 'red');
            return;
        }

        btn.disabled = true;
        const origBtnHtml = btn.innerHTML;
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline mr-1"></i> Updating…';
        lucide.createIcons();

        try {
            const res = await fetch('../../api/admin/change_password.php', { method: 'POST', body: formData });
            const json = await res.json();
            if (json.success) {
                showSettingsStatus('✓ Password updated successfully!', 'green');
                setTimeout(() => closeSettingsModal(), 2000);
            } else {
                showSettingsStatus('✗ ' + (json.message || 'Update failed'), 'red');
            }
        } catch (err) {
            showSettingsStatus('✗ Connection error', 'red');
        } finally {
            btn.disabled = false;
            btn.innerHTML = origBtnHtml;
            lucide.createIcons();
        }
    });

    function showSettingsStatus(msg, color) {
        const s = document.getElementById('settingsStatus');
        s.textContent = msg;
        s.className = `block mb-3 py-2 px-3 rounded-lg text-[11px] font-bold ${color === 'red' ? 'text-red-600 bg-red-50' : 'text-green-600 bg-green-50'}`;
        s.classList.remove('hidden');
    }
</script>