<?php
/**
 * Common Admin Head
 */
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="../../assets/js/main.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #f1f5f9;
    }

    .sidebar {
        width: 260px;
        min-height: 100vh;
    }

    .stat-card {
        transition: transform .15s, box-shadow .15s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
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

    /* Mobile Sidebar Tray */
    #mobileSidebar {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateX(-100%);
    }

    #mobileSidebar.open {
        transform: translateX(0);
    }

    #mobileOverlay {
        transition: opacity 0.3s ease;
        opacity: 0;
        pointer-events: none;
    }

    #mobileOverlay.open {
        opacity: 1;
        pointer-events: auto;
    }

    /* Modals */
    .modal-wrap {
        display: none;
    }

    .modal-wrap.open,
    #settingsModal.open {
        display: flex;
    }

    #settingsModal {
        display: none;
    }
</style>