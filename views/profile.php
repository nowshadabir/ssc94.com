<?php
/**
 * User Profile Page
 * SSC Batch '94
 */

ini_set('display_errors', 0);

define('PROJECT_ROOT', dirname(dirname(__FILE__)));

$configPath = PROJECT_ROOT . '/config/config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    exit('Server configuration error.');
}

require_once $configPath;

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.html');
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT status FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $userStatus = $stmt->fetchColumn();

    if ($userStatus !== 'active') {
        header('Location: ../payment_failed.php?error=pending_activation');
        exit;
    }
} catch (Exception $e) {
    logError("Profile status check error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SSC Batch '94</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent: #2563eb;
            --accent-light: #eff6ff;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --success: #10b981;
            --success-light: #ecfdf5;
            --gold: #f59e0b;
            --gold-light: #fffbeb;
            --navy: #1e293b;
            --radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.6;
        }

        /* ── NAVBAR ── */
        nav {
            background: var(--navy);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(8px);
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            cursor: pointer;
        }

        .nav-logo {
            background: #facc15;
            color: var(--navy);
            font-weight: 800;
            font-size: 16px;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-title {
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: -0.01em;
        }

        .nav-title span {
            color: #facc15;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px 12px 6px 6px;
            border-radius: 300px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.2s;
        }

        .nav-user:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .nav-user img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
        }

        .nav-user span {
            color: #fff;
            font-size: 13.5px;
            font-weight: 600;
        }

        .nav-logout {
            color: #94a3b8;
            background: transparent;
            border: none;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .nav-logout:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .nav-mobile-btn {
            display: none;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 850px) {
            .nav-links {
                display: none;
            }

            .nav-user span {
                display: none;
            }

            .nav-user {
                padding: 6px;
            }

            .nav-mobile-btn {
                display: flex;
            }
        }

        /* ── PROFILE HERO ── */
        .profile-hero {
            background: #fff;
            border-bottom: 1px solid var(--border);
            margin-bottom: 32px;
            position: relative;
        }

        .hero-banner {
            height: 180px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            display: flex;
            align-items: flex-end;
            gap: 24px;
            padding-bottom: 32px;
        }

        .hero-avatar-wrap {
            position: relative;
            flex-shrink: 0;
            margin-top: -80px;
        }

        .hero-avatar {
            width: 160px;
            height: 160px;
            border-radius: 24px;
            border: 6px solid #fff;
            background: #fff;
            box-shadow: var(--shadow-md);
            object-fit: cover;
            display: block;
        }

        .hero-avatar-edit {
            position: absolute;
            bottom: 12px;
            right: 12px;
            width: 40px;
            height: 40px;
            background: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow);
            color: var(--text-secondary);
            transition: all 0.2s;
            border: 1px solid var(--border);
        }

        .hero-avatar-edit:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            transform: scale(1.1);
        }

        .hero-info {
            flex: 1;
            padding-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
        }

        .hero-details h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .hero-details p {
            font-size: 15px;
            color: var(--text-secondary);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
        }

        @media (max-width: 768px) {
            .hero-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
                margin-top: -80px;
            }

            .hero-info {
                flex-direction: column;
                align-items: center;
                padding-bottom: 0;
            }

            .hero-avatar {
                width: 140px;
                height: 140px;
            }

            .hero-details h1 {
                font-size: 24px;
            }
        }

        /* Mobile menu */
        #mobile-menu {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 200;
        }

        #mobile-menu.open {
            display: block;
        }

        .mobile-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .mobile-panel {
            position: absolute;
            top: 60px;
            left: 0;
            right: 0;
            background: var(--navy);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px;
        }

        .mobile-panel a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 14px;
            border-radius: 6px;
            transition: background 0.15s;
        }

        .mobile-panel a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .mobile-panel a.active {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        /* ── PAGE LAYOUT ── */
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .page-title small {
            display: block;
            font-size: 13px;
            font-weight: 400;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ── CARD ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.2s;
        }

        .card:hover {
            box-shadow: var(--shadow);
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .card-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        .card-body {
            padding: 24px;
        }

        /* ── INFO LIST ── */
        .info-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .info-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-list li:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-list .label {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
        }

        .info-list .val {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
            text-align: right;
        }

        .info-section-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            background: #f8fafc;
            padding: 6px 12px;
            border-radius: 6px;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        /* ── BADGE ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 300px;
        }

        .badge-blue {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }

        .badge-gold {
            background: #fffbeb;
            color: #d48e06;
            border: 1px solid #fef3c7;
        }

        .badge-red {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fee2e2;
        }

        .badge-green {
            background: #ecfdf5;
            color: #10b981;
            border: 1px solid #d1fae5;
        }

        .badge-gray {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
        }

        .btn-outline {
            background: #fff;
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 13px;
        }

        .btn-block {
            width: 100%;
            justify-content: center;
        }

        /* ── FORM CONTROLS ── */
        .form-group {
            margin-bottom: 14px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 13px;
            font-family: inherit;
            color: var(--text-primary);
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.12);
        }

        /* ── DIVIDER ── */
        .section-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 0;
        }

        /* ── TICKET ── */
        .ticket-wrap {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 600px) {
            .ticket-wrap {
                flex-direction: row;
            }
        }

        .ticket-main-section {
            flex: 1;
            padding: 20px;
            border-right: none;
            border-bottom: 2px dashed var(--border);
        }

        @media (min-width: 600px) {
            .ticket-main-section {
                border-right: 2px dashed var(--border);
                border-bottom: none;
            }
        }

        .ticket-qr-section {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 150px;
            background: var(--bg);
        }

        .ticket-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .ticket-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .ticket-event-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 4px 0 16px;
        }

        .ticket-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* ── TIMELINE ── */
        .timeline {
            position: relative;
            padding-left: 28px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 16px;
        }

        .timeline-dot {
            position: absolute;
            left: -28px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 1px var(--border);
            background: #cbd5e0;
        }

        .timeline-dot.attended {
            background: var(--accent);
            box-shadow: 0 0 0 1px var(--accent);
        }

        .timeline-card {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 12px 14px;
        }

        .timeline-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .timeline-card-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .timeline-card-sub {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── BLOOD DONATION ── */
        .donation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .donation-select {
            font-size: 12px;
            font-weight: 600;
            padding: 5px 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text-primary);
            background: #fff;
            cursor: pointer;
            outline: none;
        }

        .donation-select:focus {
            border-color: var(--accent);
        }

        /* ── ICON WRAPPER ── */
        .icon-box {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-box-blue {
            background: var(--accent-light);
            color: var(--accent);
        }

        .icon-box-red {
            background: var(--danger-light);
            color: var(--danger);
        }

        .icon-box-green {
            background: var(--success-light);
            color: var(--success);
        }

        /* ── ACTION BUTTONS ROW ── */
        .action-row {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }

        .action-row .btn {
            flex: 1;
            justify-content: center;
        }

        /* ── WELCOME EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 28px 20px;
            color: var(--text-muted);
        }

        .empty-state .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }

        /* ── MODAL ── */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.open {
            display: flex;
        }

        .modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-blur: 8px;
            animation: fadeIn 0.3s ease;
        }

        .modal-container {
            position: relative;
            background: #fff;
            width: 100%;
            max-width: 650px;
            max-height: 90vh;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: slideUp 0.3s ease;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--navy);
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            background: #f8fafc;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 600px) {
            .modal-grid {
                grid-template-columns: 1fr;
            }
        }

        .image-edit-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px dashed var(--border);
        }

        .image-edit-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--bg);
            box-shadow: var(--shadow);
            margin-bottom: 12px;
        }
    </style>
    <script>
        function safeNavigate(url) {
            try { window.location.href = url; } catch (e) { alert('Navigating to ' + url); }
        }
    </script>
</head>

<body>

    <!-- NAVBAR -->
    <nav>
        <div class="nav-inner">
            <div class="nav-brand" onclick="safeNavigate('../index.html')">
                <div class="nav-logo">94</div>
                <span class="nav-title">SSC BATCH <span>'94</span></span>
            </div>
            <div class="nav-links">
                <a href="pages/find_friend.php">Find Friends</a>
                <a href="pages/events.php">Events</a>
                <a href="pages/soon.html">Donations</a>
            </div>
            <div class="nav-right">
                <div class="nav-user" onclick="safeNavigate('profile.php')">
                    <img id="navProfileImage" src="https://i.pravatar.cc/100" alt="Profile">
                    <span id="navProfileName">Member</span>
                </div>
                <a href="../api/auth/logout.php" class="nav-logout" title="Logout">
                    <i data-lucide="power" style="width:16px;height:16px;"></i>
                </a>
                <button class="nav-mobile-btn" onclick="toggleMenu()">
                    <i data-lucide="menu" id="icon-menu" style="width:18px;height:18px;"></i>
                    <i data-lucide="x" id="icon-close" style="width:18px;height:18px;display:none;"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu">
        <div class="mobile-overlay" onclick="toggleMenu()"></div>
        <div class="mobile-panel">
            <a href="pages/find_friend.php"><i data-lucide="users" style="width:16px;height:16px;"></i> Find Friends</a>
            <a href="pages/events.php"><i data-lucide="calendar" style="width:16px;height:16px;"></i> Events</a>
            <a href="pages/soon.html"><i data-lucide="heart" style="width:16px;height:16px;"></i>
                Donations</a>
            <a href="profile.php" class="active"><i data-lucide="user" style="width:16px;height:16px;"></i> My
                Profile</a>
        </div>
    </div>

    <!-- PROFILE HERO -->
    <div class="profile-hero">
        <div class="hero-banner"></div>
        <div class="hero-content">
            <div class="hero-avatar-wrap">
                <img src="https://i.pravatar.cc/300?u=<?php echo $userId; ?>" alt="Profile" id="profileImage"
                    class="hero-avatar">
                <label for="profileImageInput" class="hero-avatar-edit" title="Change Photo">
                    <i data-lucide="camera" style="width:20px;height:20px;"></i>
                </label>
                <input type="file" id="profileImageInput" accept="image/*" onchange="previewProfileImage(event)"
                    style="display:none;">
            </div>
            <div class="hero-info">
                <div class="hero-details">
                    <h1 id="displayName">Loading...</h1>
                    <p>
                        <i data-lucide="briefcase" style="width:16px;height:16px;color:var(--accent);"></i>
                        <span id="displayJob">Profession</span> @ <span id="displayInstitute">Institution</span>
                    </p>
                </div>
                <div class="hero-actions">
                    <button onclick="toggleEditMode()" class="btn btn-primary">
                        <i data-lucide="edit-3" style="width:16px;height:16px;"></i>
                        Edit Profile
                    </button>
                    <a href="../api/auth/logout.php" class="btn btn-outline" style="color:var(--danger);">
                        <i data-lucide="log-out" style="width:16px;height:16px;"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- PAGE -->
    <div class="page-container">
        <div class="profile-grid">

            <!-- ═══════════ LEFT COLUMN ═══════════ -->
            <div style="display:flex;flex-direction:column;gap:24px;">

                <!-- Wallet & ID Card -->
                <div class="card" style="background: var(--navy); color: #fff; border: none;">
                    <div class="card-body" style="display:flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div
                                style="font-size:11px; font-weight:700; text-transform:uppercase; color:rgba(255,255,255,0.5); margin-bottom:4px;">
                                Available Balance</div>
                            <div style="font-size:24px; font-weight:800; color:#facc15;">৳ <span
                                    id="displayBalance">0.00</span></div>
                        </div>
                        <div style="text-align:right;">
                            <div
                                style="font-size:11px; font-weight:700; text-transform:uppercase; color:rgba(255,255,255,0.5); margin-bottom:4px;">
                                Member ID</div>
                            <div style="font-size:16px; font-weight:700; color:#fff;" id="displayMemberId">#000000</div>
                        </div>
                    </div>
                </div>

                <!-- Personal Info Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-left">
                            <i data-lucide="user" style="width:18px;height:18px;color:var(--accent);"></i>
                            <span class="card-title">Profile Details</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="info-section-title">Professional</div>
                        <ul class="info-list">
                            <li><span class="label">Profession</span><span class="val"
                                    id="displayProfessionInfo">---</span></li>
                            <li><span class="label">Company</span><span class="val" id="displayCompanyInfo">---</span>
                            </li>
                        </ul>

                        <div class="info-section-title" style="margin-top:20px;">Identity & Family</div>
                        <ul class="info-list">
                            <li><span class="label">Father's Name</span><span class="val"
                                    id="displayFatherName">---</span></li>
                            <li><span class="label">Mother's Name</span><span class="val"
                                    id="displayMotherName">---</span></li>
                            <li><span class="label">Blood Group</span><span class="val"
                                    id="displayBloodGroupInfo">---</span></li>
                        </ul>

                        <div class="info-section-title" style="margin-top:20px;">Location Details</div>
                        <ul class="info-list">
                            <li><span class="label">Home District</span><span class="val" id="displayZilla">---</span>
                            </li>
                            <li><span class="label">Current Area</span><span class="val" id="displayLocation">---</span>
                            </li>
                            <li><span class="label">Permanent Address</span><span class="val"
                                    id="displayPermanentAddress">---</span></li>
                        </ul>

                        <div class="info-section-title" style="margin-top:20px;">Education & Contact</div>
                        <ul class="info-list">
                            <li><span class="label">School (SSC)</span><span class="val" id="displaySchool">---</span>
                            </li>
                            <li><span class="label">Email</span><span class="val" id="displayEmail">---</span></li>
                            <li><span class="label">Mobile</span><span class="val" id="displayMobile">---</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Blood Donation Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-left">
                            <i data-lucide="droplet" style="width:18px;height:18px;color:var(--danger);"></i>
                            <span class="card-title">Blood Donation</span>
                        </div>
                        <select id="bloodAvailability" class="donation-select" onchange="toggleDonationStatus()">
                            <option value="1">Available</option>
                            <option value="0">Unavailable</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <p id="donationStatusText"
                            style="font-size:14px; color:var(--text-secondary); margin-bottom:12px;">
                            Checking status...
                        </p>
                        <div
                            style="padding:12px; background:var(--bg); border-radius:8px; display:flex; align-items:center; gap:10px;">
                            <i data-lucide="calendar" style="width:14px;height:14px;color:var(--text-muted);"></i>
                            <span style="font-size:12px; color:var(--text-muted);">Last donated: <strong
                                    id="displayLastDonation" style="color:var(--text-secondary);">---</strong></span>
                        </div>
                    </div>
                </div>

            </div><!-- /left column -->

            <!-- ═══════════ RIGHT COLUMN ═══════════ -->
            <div style="display:flex;flex-direction:column;gap:24px;">

                <!-- Reunion Ticket -->
                <div id="ticketSection" class="card" style="display:none; overflow:hidden;">
                    <div class="card-header" style="background: var(--accent-light);">
                        <div class="card-header-left">
                            <i data-lucide="ticket" style="width:18px;height:18px;color:var(--accent);"></i>
                            <span class="card-title">Reunion Ticket &mdash; <span
                                    id="ticketHeaderYear">2024</span></span>
                        </div>
                        <span class="badge badge-green">Paid & Confirmed</span>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="ticket-wrap" style="border:none;">
                            <!-- Left Section -->
                            <div class="ticket-main-section" style="padding:24px;">
                                <div class="ticket-label">Event</div>
                                <div class="ticket-event-name" id="ticketEventTitle"
                                    style="font-size:22px; margin-bottom:20px;">Grand Reunion 2024</div>
                                <div class="ticket-grid">
                                    <div>
                                        <div class="ticket-label">Date</div>
                                        <div class="ticket-value" id="ticketDate">15 Dec, 2024</div>
                                    </div>
                                    <div>
                                        <div class="ticket-label">Venue</div>
                                        <div class="ticket-value" id="ticketVenue">Green View Resort</div>
                                    </div>
                                    <div>
                                        <div class="ticket-label">T-Shirt Size</div>
                                        <div class="ticket-value" id="ticketTshirt">XL (Men)</div>
                                    </div>
                                    <div>
                                        <div class="ticket-label">Guests</div>
                                        <div class="ticket-value" id="ticketGuests">Spouse + 1 Child</div>
                                    </div>
                                </div>
                            </div>
                            <!-- QR Section -->
                            <div class="ticket-qr-section" style="background:#f1f5f9; padding:24px; min-width:180px;">
                                <div id="ticketQr"
                                    style="background:#fff;padding:8px;border:1px solid var(--border);border-radius:12px;width:120px;height:120px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:var(--shadow-sm);">
                                </div>
                                <div class="ticket-label">Ticket ID</div>
                                <div style="font-size:15px;font-weight:700;font-family:monospace;color:var(--text-primary);letter-spacing:1px;"
                                    id="ticketId">#00-0000</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reunion History -->
                <div id="historyBlock" class="card" style="display:none;">
                    <div class="card-header">
                        <div class="card-header-left">
                            <i data-lucide="history" style="width:18px;height:18px;color:var(--accent);"></i>
                            <span class="card-title">Reunion History</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="reunionHistoryList" class="timeline">
                            <!-- Populated via JS -->
                        </div>
                    </div>
                </div>

                <!-- Additional Info / Help Card -->
                <div class="card"
                    style="background: var(--accent-light); border-color: var(--accent); border-style: dashed;">
                    <div class="card-body" style="display:flex; gap:16px; align-items: center;">
                        <div
                            style="width:48px; height:48px; border-radius:12px; background:var(--surface); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i data-lucide="help-circle" style="width:24px;height:24px;color:var(--accent);"></i>
                        </div>
                        <div>
                            <h4 style="font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">
                                Need Help?</h4>
                            <p style="font-size:13px; color:var(--text-secondary);">If you find any incorrect
                                information or need to update your batch details, please contact the admin team.</p>
                        </div>
                    </div>
                </div>

            </div><!-- /right column -->
        </div><!-- /profile-grid -->
    </div><!-- /page-container -->



    <!-- ── EDIT PROFILE MODAL ── -->
    <div id="editProfileModal" class="modal">
        <div class="modal-overlay" onclick="closeEditModal()"></div>
        <div class="modal-container">
            <div class="modal-header">
                <span class="modal-title">Update Profile Details</span>
                <button onclick="closeEditModal()"
                    style="background:none; border:none; color:var(--text-muted); cursor:pointer;">
                    <i data-lucide="x" style="width:20px;height:20px;"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Image Edit -->
                <div class="image-edit-container">
                    <img id="modalPreviewImg" src="https://i.pravatar.cc/300" class="image-edit-preview">
                    <label for="modalProfileImageInput" class="btn btn-outline btn-sm">
                        <i data-lucide="camera" style="width:14px;height:14px;"></i>
                        Change Photo
                    </label>
                    <input type="file" id="modalProfileImageInput" accept="image/*"
                        onchange="previewProfileImage(event)" style="display:none;">
                </div>

                <div class="modal-grid">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" id="inputName" class="form-control" placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" id="inputEmail" class="form-control" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mobile *</label>
                        <input type="tel" id="inputMobile" class="form-control" placeholder="01711XXXXXX"
                            maxlength="11">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Blood Group *</label>
                        <select id="inputBloodGroup" class="form-control">
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profession / Job *</label>
                        <input type="text" id="inputJob" class="form-control" placeholder="e.g. Doctor">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Company / Institute *</label>
                        <input type="text" id="inputInstitute" class="form-control" placeholder="e.g. DMCH">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Father's Name</label>
                        <input type="text" id="inputFatherName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mother's Name</label>
                        <input type="text" id="inputMotherName" class="form-control">
                    </div>
                    <div class="form-group col-span-2">
                        <label class="form-label">Present Address *</label>
                        <input type="text" id="inputCurrentLocation" class="form-control"
                            placeholder="Current living area">
                    </div>
                    <div class="form-group col-span-2">
                        <label class="form-label">Permanent Address *</label>
                        <input type="text" id="inputPermanentAddress" class="form-control"
                            placeholder="Village, Upozilla, Zilla">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Home District *</label>
                        <input type="text" id="inputZilla" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Upozilla *</label>
                        <input type="text" id="inputUpozilla" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">School Name *</label>
                        <input type="text" id="inputSchool" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Donation Date</label>
                        <input type="date" id="inputLastDonation" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeEditModal()" class="btn btn-outline">Cancel</button>
                <button id="modalSaveBtn" onclick="saveProfile()" class="btn btn-primary">
                    <i data-lucide="check" style="width:15px;height:15px;"></i>
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        let userData = {
            id: null, memberId: '000000', balance: '0.00',
            name: 'User', email: '', mobile: '',
            job: 'Not specified', institute: 'Not specified',
            currentLocation: 'Bangladesh', bloodGroup: 'Not specified',
            schoolName: 'Not specified', zilla: 'Not specified',
            upozilla: 'Not specified', fatherName: '', motherName: '',
            permanentAddress: '',
            lastDonation: new Date().toISOString().slice(0, 10),
            willingToDonate: true,
            profileImage: 'https://i.pravatar.cc/300?u=<?php echo $userId; ?>'
        };

        function setElText(id, text) {
            const el = document.getElementById(id);
            if (el) el.textContent = text;
        }

        function loadUserData() {
            setElText('displayName', userData.name);
            setElText('displayMemberId', '#' + (userData.memberId || '000000'));
            setElText('displayBalance', parseFloat(userData.balance).toFixed(2));
            setElText('navProfileName', userData.name.split(' ')[0]);
            setElText('displayJob', userData.job);
            setElText('displayProfessionInfo', userData.job);
            setElText('displayInstitute', userData.institute);
            setElText('displayCompanyInfo', userData.institute);
            setElText('displayLocation', userData.currentLocation.split(',')[0]);
            setElText('displayBloodGroupInfo', userData.bloodGroup);
            setElText('displaySchool', userData.schoolName);
            setElText('displayZilla', userData.zilla);
            setElText('displayUpozilla', userData.upozilla);
            setElText('displayFatherName', userData.fatherName || 'Not specified');
            setElText('displayMotherName', userData.motherName || 'Not specified');
            setElText('displayPermanentAddress', userData.permanentAddress || 'Not specified');
            setElText('displayEmail', userData.email);
            setElText('displayMobile', userData.mobile);

            const img = document.getElementById('profileImage');
            if (img) img.src = userData.profileImage;
            const navImg = document.getElementById('navProfileImage');
            if (navImg) navImg.src = userData.profileImage;

            const d = new Date(userData.lastDonation);
            setElText('displayLastDonation', d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }));

            const sel = document.getElementById('bloodAvailability');
            if (sel) { sel.value = userData.willingToDonate ? '1' : '0'; toggleDonationStatus(); }
            initReunionTicket();
            loadReunionHistory();
        }

        async function loadReunionHistory() {
            try {
                const response = await fetch('../api/reunion/get_history.php');
                const result = await response.json();
                if (result.success && result.data) {
                    const historyList = document.getElementById('reunionHistoryList');
                    const historyBlock = document.getElementById('historyBlock');
                    historyList.innerHTML = '';
                    let attendedCount = 0;

                    if (result.data && result.data.length > 0) {
                        historyList.classList.add('timeline');
                        result.data.forEach(item => {
                            const isAttended = item.payment_status === 'completed';
                            if (isAttended) attendedCount++;
                            const date = new Date(item.reunion_date);
                            const year = date.getFullYear();
                            const html = `
                            <div class="timeline-item">
                                <div class="timeline-dot ${isAttended ? 'attended' : ''}"></div>
                                <div class="timeline-card">
                                    <div class="timeline-card-top">
                                        <span class="timeline-card-title">${item.title}</span>
                                        <span class="badge ${isAttended ? 'badge-green' : 'badge-gray'}">${isAttended ? 'Attended' : 'Missed'}</span>
                                    </div>
                                    <div class="timeline-card-sub">${item.venue} &bull; ${year}</div>
                                </div>
                            </div>`;
                            historyList.insertAdjacentHTML('beforeend', html);
                        });
                    }

                    if (attendedCount === 0 && result.data.length === 0) {
                        historyList.classList.remove('timeline');
                        historyList.innerHTML = `
                        <div class="empty-state">
                            <div class="icon-circle"><i data-lucide="star" style="width:20px;height:20px;color:var(--accent);"></i></div>
                            <strong style="display:block;margin-bottom:6px;color:var(--text-primary);font-size:15px;">Welcome to the Community!</strong>
                            <p style="max-width:300px;margin:0 auto;font-size:13px;">You haven't attended any reunions yet. Join an upcoming event to build your history with Batch '94.</p>
                        </div>`;
                    } else if (attendedCount === 0 && result.data.length > 0) {
                        // If they have registration records but none attended, we keep the timeline view
                        historyList.classList.add('timeline');
                    }
                    historyBlock.style.display = '';
                    lucide.createIcons();
                }
            } catch (e) { console.error('Failed to load history:', e); }
        }

        async function initReunionTicket() {
            try {
                const response = await fetch('../api/reunion/get_active.php');
                const result = await response.json();
                if (result.success && result.data && result.data.reunion) {
                    const r = result.data.reunion;
                    const date = new Date(r.reunion_date);
                    if (result.data.user_registration) {
                        const reg = result.data.user_registration;
                        document.getElementById('ticketSection').style.display = '';
                        const year = date.getFullYear();
                        document.getElementById('ticketHeaderYear').innerText = year;

                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = r.title;
                        document.getElementById('ticketEventTitle').innerText = tempDiv.innerText;
                        document.getElementById('ticketDate').innerText = date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                        tempDiv.innerHTML = r.venue;
                        document.getElementById('ticketVenue').innerText = tempDiv.innerText;
                        document.getElementById('ticketTshirt').innerText = reg.tshirt_size ? reg.tshirt_size.toUpperCase() : 'Not set';

                        let guestsText = 'Member Only';
                        if (reg.guest_count > 0) guestsText = `Member + ${reg.guest_count} ${reg.guest_count == 1 ? 'Guest' : 'Guests'}`;
                        document.getElementById('ticketGuests').innerText = guestsText;
                        document.getElementById('ticketId').innerText = reg.ticket_number;

                        const verifyUrl = `${window.location.origin}${window.location.pathname.replace('views/profile.php', '')}verify_ticket.php?t=${encodeURIComponent(reg.ticket_number)}`;

                        const qrContainer = document.getElementById('ticketQr');
                        qrContainer.innerHTML = '';
                        new QRCode(qrContainer, { text: verifyUrl, width: 100, height: 100, colorDark: '#1e3a5f', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.H });

                        lucide.createIcons();
                    }
                }
            } catch (e) { console.error('Failed to load reunion ticket:', e); }
        }

        function toggleEditMode() {
            const modal = document.getElementById('editProfileModal');
            modal.classList.add('open');

            // Fill inputs
            document.getElementById('modalPreviewImg').src = userData.profileImage;
            document.getElementById('inputName').value = userData.name;
            document.getElementById('inputEmail').value = userData.email;
            document.getElementById('inputMobile').value = userData.mobile;
            document.getElementById('inputJob').value = userData.job;
            document.getElementById('inputInstitute').value = userData.institute;
            document.getElementById('inputCurrentLocation').value = userData.currentLocation;
            document.getElementById('inputBloodGroup').value = userData.bloodGroup;
            document.getElementById('inputSchool').value = userData.schoolName;
            document.getElementById('inputZilla').value = userData.zilla;
            document.getElementById('inputUpozilla').value = userData.upozilla;
            document.getElementById('inputFatherName').value = userData.fatherName;
            document.getElementById('inputMotherName').value = userData.motherName;
            document.getElementById('inputPermanentAddress').value = userData.permanentAddress;
            document.getElementById('inputLastDonation').value = userData.lastDonation;

            lucide.createIcons();
        }

        function closeEditModal() {
            document.getElementById('editProfileModal').classList.remove('open');
        }

        async function saveProfile() {
            const saveBtn = document.getElementById('modalSaveBtn');
            const originalHtml = saveBtn.innerHTML;

            const name = document.getElementById('inputName').value.trim();
            const email = document.getElementById('inputEmail').value.trim();
            const mobile = document.getElementById('inputMobile').value.trim();
            const job = document.getElementById('inputJob').value.trim();
            const institute = document.getElementById('inputInstitute').value.trim();
            const currentLocation = document.getElementById('inputCurrentLocation').value.trim();
            const bloodGroup = document.getElementById('inputBloodGroup').value;
            const schoolName = document.getElementById('inputSchool').value.trim();
            const zilla = document.getElementById('inputZilla').value.trim();
            const upozilla = document.getElementById('inputUpozilla').value.trim();
            const permanentAddress = document.getElementById('inputPermanentAddress').value.trim();

            if (!name || !email || !mobile) {
                alert('Please provide Name, Email and Mobile.'); return;
            }
            if (!/^[0-9]{11}$/.test(mobile)) {
                alert('Mobile number must be exactly 11 digits'); return;
            }

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i data-lucide="loader-2" class="animate-spin w-4 h-4 mr-2"></i> Saving...';
            lucide.createIcons();

            const formData = new FormData();
            formData.append('name', name); formData.append('email', email); formData.append('mobile', mobile);
            formData.append('job', job); formData.append('institute', institute);
            formData.append('current_location', currentLocation); formData.append('blood_group', bloodGroup);
            formData.append('school_name', schoolName); formData.append('zilla', zilla);
            formData.append('upozilla', upozilla);
            formData.append('father_name', document.getElementById('inputFatherName').value.trim());
            formData.append('mother_name', document.getElementById('inputMotherName').value.trim());
            formData.append('permanent_address', permanentAddress);
            formData.append('last_donation_date', document.getElementById('inputLastDonation').value);
            formData.append('willing_to_donate', document.getElementById('bloodAvailability').value);

            const imageInput = document.getElementById('modalProfileImageInput');
            if (imageInput && imageInput.files && imageInput.files[0]) formData.append('profile_image', imageInput.files[0]);

            try {
                const response = await fetch('../api/profile/update.php', { method: 'POST', body: formData, credentials: 'same-origin' });
                const payload = await response.json();
                if (!response.ok || !payload.success) { alert(payload.message || 'Failed to update profile'); return; }
                userData = payload.data;
                loadUserData();
                closeEditModal();
                alert('Profile updated successfully!');
            } catch (e) { alert('Failed to update profile.'); }
            finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalHtml;
                lucide.createIcons();
            }
        }

        function cancelEdit() { closeEditModal(); }

        function previewProfileImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('modalPreviewImg').src = e.target.result;
                    document.getElementById('profileImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            const iconMenu = document.getElementById('icon-menu');
            const iconClose = document.getElementById('icon-close');
            const isOpen = menu.classList.contains('open');
            if (isOpen) {
                menu.classList.remove('open');
                iconMenu.style.display = ''; iconClose.style.display = 'none';
                document.body.style.overflow = 'auto';
            } else {
                menu.classList.add('open');
                iconMenu.style.display = 'none'; iconClose.style.display = '';
                document.body.style.overflow = 'hidden';
            }
        }

        function toggleDonationStatus() {
            const toggle = document.getElementById('bloodAvailability');
            const text = document.getElementById('donationStatusText');
            if (!toggle || !text) return;
            userData.willingToDonate = toggle.value === '1';
            if (userData.willingToDonate) {
                text.innerHTML = 'You are currently <strong style="color:#276749;">Available</strong> to donate blood.';
            } else {
                text.innerHTML = 'You are set to <strong style="color:#c53030;">Unavailable</strong> for blood donation.';
            }
        }

        async function fetchProfile() {
            try {
                const response = await fetch('../api/profile/get.php', { credentials: 'same-origin' });
                const payload = await response.json();
                if (!response.ok || !payload.success) return;
                userData = payload.data;
                loadUserData();
            } catch (e) { /* keep placeholders */ }
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadUserData();
            fetchProfile();
        });
    </script>
</body>

</html>