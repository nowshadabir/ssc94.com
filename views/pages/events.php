<?php
/**
 * Events & Adda Page
 * SSC Batch '94
 */

define('PROJECT_ROOT', dirname(dirname(dirname(__FILE__))));
$configPath = PROJECT_ROOT . '/config/config.php';

if (file_exists($configPath)) {
    require_once $configPath;

    // Check login status
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.html');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Adda - SSC Batch '94</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Icons (Lucide) -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Righteous&family=Inter:wght@300;400;500;600;700&family=Handlee&display=swap"
        rel="stylesheet">

    <style>
        /* Custom Theme Configuration */
        :root {
            --primary-navy: #0f172a;
            --accent-gold: #fbbf24;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-image: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), url('https://images.unsplash.com/photo-1529156069898-49953e39b3ac?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        h1,
        h2,
        h3,
        .brand-font {
            font-family: 'Righteous', cursive;
        }

        .handwriting {
            font-family: 'Handlee', cursive;
        }

        /* Card Hover */
        .event-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Avatar Stack Hover */
        .avatar-stack:hover .avatar {
            margin-right: 5px;
        }

        .avatar {
            transition: margin 0.3s ease;
        }

        /* Tooltip Animation */
        .tooltip-content {
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px) translateX(-50%);
            }

            to {
                opacity: 1;
                transform: translateY(0) translateX(-50%);
            }
        }

        /* Mobile Menu Animation */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.3s ease-out forwards;
        }

        #mapPicker {
            height: 200px;
            width: 100%;
            border-radius: 1rem;
            border: 2px border-slate-100;
            z-index: 10;
        }
    </style>
    <script>
        function safeNavigate(url) {
            try { window.location.href = url; }
            catch (e) { alert("Navigating to " + url); }
        }
    </script>
</head>

<body class="antialiased text-slate-800 flex flex-col min-h-screen">

    <!-- PREMIUM MINIMAL NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div
                class="bg-slate-900/80 backdrop-blur-xl border border-white/10 rounded-2xl px-6 h-16 flex items-center justify-between shadow-2xl shadow-slate-900/50">

                <!-- Brand -->
                <div class="flex items-center gap-3 cursor-pointer group" onclick="safeNavigate('../../index.html')">
                    <div
                        class="w-10 h-10 flex items-center justify-center bg-yellow-400 text-slate-900 rounded-xl font-black text-xl tracking-tighter transform group-hover:-rotate-12 transition-all duration-500 shadow-lg shadow-yellow-400/20">
                        94</div>
                    <div class="hidden sm:flex flex-col leading-none">
                        <span class="text-white font-extrabold tracking-tight text-lg">SSC BATCH <span
                                class="text-yellow-400">'94</span></span>
                        <span class="text-[9px] text-slate-400 uppercase tracking-[0.2em] font-bold">Friends For
                            Friends</span>
                    </div>
                </div>

                <!-- Nav Piles (Desktop) -->
                <div class="hidden md:flex items-center gap-1 bg-white/5 p-1 rounded-full border border-white/5">
                    <a href="find_friend.php"
                        class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white hover:bg-white/5 transition-all">Find
                        Friends</a>
                    <a href="events.php"
                        class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider text-white bg-white/10 transition-all">Events</a>
                    <a href="soon.html"
                        class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white hover:bg-white/5 transition-all">Donations</a>
                </div>

                <!-- Profile & Actions -->
                <div class="flex items-center gap-3">
                    <div id="auth-desktop" class="flex items-center gap-3">
                        <a href="../auth/login.html" id="login-link"
                            class="bg-white text-slate-900 px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider hover:bg-yellow-400 hover:shadow-lg hover:shadow-yellow-400/30 transition-all duration-300">
                            Login
                        </a>
                        <div id="profile-dropdown"
                            class="hidden flex items-center gap-2 bg-white/5 pl-1 pr-4 py-1 rounded-full border border-white/10 hover:bg-white/10 transition-colors cursor-pointer"
                            onclick="safeNavigate('../profile.php')">
                            <img id="profile-photo" src="https://i.pravatar.cc/300?u=guest"
                                class="w-8 h-8 rounded-full border-2 border-yellow-400/50">
                            <span id="profile-name" class="text-xs font-bold text-white tracking-wide">Member</span>
                        </div>
                    </div>

                    <div class="w-px h-6 bg-white/10 mx-1 hidden sm:block"></div>

                    <button id="logout-btn-nav"
                        class="hidden w-10 h-10 flex items-center justify-center rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all duration-300 group"
                        title="Logout" onclick="safeNavigate('../../api/auth/logout.php')">
                        <i data-lucide="power" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    </button>

                    <!-- Mobile Menu Trigger -->
                    <button onclick="toggleMenu()"
                        class="md:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 text-slate-400 hover:text-white transition-all">
                        <i data-lucide="menu" id="icon-menu" class="w-6 h-6"></i>
                        <i data-lucide="x" id="icon-close" class="hidden w-6 h-6 text-red-400"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- NEW MOBILE OVERLAY MENU -->
        <div id="mobile-menu" class="hidden fixed inset-0 z-40">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" onclick="toggleMenu()"></div>
            <div
                class="absolute top-24 left-4 right-4 bg-slate-900 border border-white/10 rounded-3xl p-6 shadow-2xl animate-fade-in-down">
                <div class="grid grid-cols-1 gap-3">
                    <a href="find_friend.php"
                        class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 text-slate-300 font-bold hover:bg-yellow-400 hover:text-slate-900 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-yellow-400/20 flex items-center justify-center"><i
                                data-lucide="users" class="w-5 h-5 text-yellow-400"></i></div>
                        Find Friends
                    </a>
                    <a href="events.php"
                        class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 text-slate-300 font-bold hover:bg-blue-400 hover:text-slate-900 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-blue-400/20 flex items-center justify-center"><i
                                data-lucide="calendar" class="w-5 h-5 text-blue-400"></i></div>
                        Events
                    </a>
                    <a href="soon.html"
                        class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 text-slate-300 font-bold hover:bg-green-400 hover:text-slate-900 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-green-400/20 flex items-center justify-center"><i
                                data-lucide="heart" class="w-5 h-5 text-green-400"></i></div>
                        Donations
                    </a>
                    <div class="h-px bg-white/10 my-2"></div>
                    <div id="mobile-auth-section">
                        <a href="../auth/login.html" id="login-link-mobile"
                            class="flex items-center gap-4 p-4 rounded-2xl bg-white text-slate-900 font-bold transition-all">
                            <i data-lucide="log-in" class="w-5 h-5"></i> Member Login
                        </a>
                        <a href="../profile.php" id="profile-link-mobile"
                            class="hidden flex items-center gap-4 p-4 rounded-2xl bg-slate-800 text-white font-bold transition-all">
                            <i data-lucide="user" class="w-5 h-5 text-slate-400"></i> My Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="h-24"></div>

    <!-- HEADER -->
    <div class="relative overflow-hidden py-16 border-b border-slate-800">
        <!-- <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-blue-900/40 to-slate-900 opacity-95"></div>
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
            </div>
        </div> -->

        <div class="relative z-10 max-w-5xl mx-auto px-4 text-center">
            <span class="text-yellow-400 handwriting text-2xl block mb-2 transform -rotate-2">Life is better with
                friends</span>
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 brand-font">
                Upcoming <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400">Adda</span>
            </h1>

            <!-- City Filter & Create Trigger -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-6 mt-8">
                <div class="flex justify-center gap-2 flex-wrap">
                    <button onclick="filterEvents('All')"
                        class="filter-btn active bg-yellow-500 text-slate-900 px-5 py-2 rounded-full font-bold text-sm hover:bg-yellow-400 transition">All</button>
                    <button onclick="filterEvents('Dhaka')"
                        class="filter-btn bg-slate-800 text-slate-300 border border-slate-700 px-5 py-2 rounded-full font-bold text-sm hover:text-white hover:border-yellow-500 transition">Dhaka</button>
                    <button onclick="filterEvents('Chittagong')"
                        class="filter-btn bg-slate-800 text-slate-300 border border-slate-700 px-5 py-2 rounded-full font-bold text-sm hover:text-white hover:border-yellow-500 transition">Chittagong</button>
                    <button onclick="filterEvents('Online')"
                        class="filter-btn bg-slate-800 text-slate-300 border border-slate-700 px-5 py-2 rounded-full font-bold text-sm hover:text-white hover:border-yellow-500 transition">Online</button>
                </div>

                <div id="host-action-container" class="hidden">
                    <button onclick="openCreateModal()"
                        class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-slate-900 px-6 py-2.5 rounded-xl font-bold text-sm hover:from-yellow-300 hover:to-yellow-500 transition shadow-lg shadow-yellow-500/20 flex items-center">
                        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Host an Adda
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- EVENTS GRID -->
    <div class="flex-grow max-w-7xl mx-auto px-4 py-12 w-full">
        <div id="eventsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Event cards will be injected here -->
        </div>
    </div>

    <!-- CREATE EVENT MODAL -->
    <div id="createModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl relative overflow-hidden transform transition-all scale-95 opacity-0"
            id="createModalContent">
            <div class="bg-slate-900 p-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg flex items-center" id="modalTitle">
                    <i data-lucide="coffee" class="w-5 h-5 mr-2 text-yellow-400"></i> Host an Adda
                </h3>
                <button onclick="closeCreateModal()" class="hover:text-yellow-400 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form id="eventForm" onsubmit="handleFormSubmit(event)" class="p-6 space-y-4">
                <input type="hidden" name="event_id" id="editEventId">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Event Title</label>
                    <input name="title" type="text" class="w-full border p-2 rounded-lg bg-slate-50 font-medium"
                        placeholder="e.g. Friday Evening Tea" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Date</label>
                        <input name="date" type="date" class="w-full border p-2 rounded-lg bg-slate-50" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Time</label>
                        <input name="time" type="time" class="w-full border p-2 rounded-lg bg-slate-50" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">City</label>
                        <input name="city" type="text" class="w-full border p-2 rounded-lg bg-slate-50 font-medium"
                            placeholder="e.g. Dhaka" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Location</label>
                        <input name="location" type="text" class="w-full border p-2 rounded-lg bg-slate-50"
                            placeholder="e.g. Dhanmondi Lake" required>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase">Pin Location (Map
                            Picker)</label>
                        <button type="button" onclick="findMyLocation()"
                            class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-1 rounded-lg font-bold hover:bg-indigo-100 transition flex items-center">
                            <i data-lucide="crosshair" class="w-3 h-3 mr-1"></i> Use My Location
                        </button>
                    </div>
                    <div id="mapPicker" class="mb-2"></div>
                    <input name="map_link" id="mapLinkInput" type="hidden">
                    <p class="text-[10px] text-slate-400 italic">Click on the map to set the exact point for directions.
                    </p>
                </div>
                <button type="submit" id="submitBtn"
                    class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition">Create
                    Event</button>
            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-8 mt-auto border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm">&copy; 2026 SSC Batch '94 Association</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        // Mobile Menu Toggle
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            const iconMenu = document.getElementById('icon-menu');
            const iconClose = document.getElementById('icon-close');

            if (menu.classList.contains('hidden')) {
                // Open Menu
                menu.classList.remove('hidden');
                menu.classList.add('animate-fade-in-down');
                iconMenu.classList.add('hidden');
                iconClose.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                // Close Menu
                menu.classList.add('hidden');
                menu.classList.remove('animate-fade-in-down');
                iconMenu.classList.remove('hidden');
                iconClose.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // 1. Live Data (Loaded from API)
        let eventsDB = [];
        let currentUserId = null;

        // 2. Render Function
        function renderEvents(data) {
            const container = document.getElementById('eventsContainer');
            container.innerHTML = '';

            data.forEach(event => {
                // 1. Build Tooltip List
                let tooltipRows = '';
                event.attendees.forEach(att => {
                    tooltipRows += `
                        <div class="flex items-center gap-3 mb-2 last:mb-0">
                            <img src="${att.img}" class="w-6 h-6 rounded-full border border-slate-200">
                            <span class="text-xs font-bold text-slate-700 whitespace-nowrap">${att.name}</span>
                        </div>
                    `;
                });
                // Add "others" count if needed
                if (event.totalGoing > event.attendees.length) {
                    tooltipRows += `<div class="text-[10px] text-slate-400 pl-9 italic">+ ${event.totalGoing - event.attendees.length} others</div>`;
                }

                // 2. Build Avatar Stack Visuals
                let avatarImgs = '';
                event.attendees.slice(0, 4).forEach(att => {
                    avatarImgs += `
                        <img class="w-8 h-8 rounded-full border-2 border-white avatar relative z-0 hover:z-10 bg-slate-200 object-cover" src="${att.img}" alt="${att.name}">
                    `;
                });
                if (event.totalGoing > 4) {
                    avatarImgs += `
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 z-10 relative">
                            +${event.totalGoing - 4}
                        </div>
                    `;
                }

                // 3. Construct Card
                const card = document.createElement('div');
                card.className = "bg-white rounded-[2rem] p-7 shadow-sm border border-slate-100 event-card flex flex-col h-full";

                const btnClass = event.isAttending
                    ? "bg-green-100 text-green-700 px-6 py-2.5 rounded-xl font-bold text-sm border border-green-200 transition"
                    : "bg-[#0f172a] text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-slate-800 transition shadow-lg shadow-slate-200";

                const btnText = event.isAttending ? "Going ✓" : "I'm Coming";

                card.innerHTML = `
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center">
                            <div class="relative">
                                <img src="${event.hostImg}" class="w-14 h-14 rounded-2xl border-2 border-white shadow-md bg-slate-200 object-cover transform rotate-3">
                                <div class="absolute -bottom-1 -right-1 bg-yellow-400 w-4 h-4 rounded-full border-2 border-white"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mb-0.5">Host</p>
                                <p class="text-base font-bold text-slate-900 leading-none">${event.host}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                             ${event.organizerId === currentUserId ? `
                                <button onclick="openEditModal(${event.id})" class="p-2 bg-slate-50 text-blue-500 rounded-lg hover:bg-blue-50 transition" title="Edit">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteEvent(${event.id})" class="p-2 bg-slate-50 text-red-500 rounded-lg hover:bg-red-50 transition" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-slate-900 mb-6 leading-tight group-hover:text-indigo-600 transition tracking-tight">${event.title}</h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-slate-50/80 p-3 rounded-2xl flex items-center group/item transition hover:bg-slate-100">
                             <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center mr-3 shrink-0">
                                <i data-lucide="calendar" class="w-4 h-4 text-orange-600"></i>
                             </div>
                             <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Date</p>
                                <p class="text-xs font-bold text-slate-700">${event.date}</p>
                             </div>
                        </div>
                        <div class="bg-slate-50/80 p-3 rounded-2xl flex items-center group/item transition hover:bg-slate-100">
                             <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center mr-3 shrink-0">
                                <i data-lucide="clock" class="w-4 h-4 text-blue-600"></i>
                             </div>
                             <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Time</p>
                                <p class="text-xs font-bold text-slate-700">${event.time}</p>
                             </div>
                        </div>
                        <div class="bg-slate-50/80 p-3 rounded-2xl col-span-2 flex items-center group/item transition hover:bg-slate-100">
                             <div class="w-8 h-8 rounded-xl bg-green-100 flex items-center justify-center mr-3 shrink-0">
                                <i data-lucide="map-pin" class="w-4 h-4 text-green-600"></i>
                             </div>
                             <div class="flex-grow">
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Venue</p>
                                <p class="text-xs font-bold text-slate-700 line-clamp-1">${event.location}</p>
                             </div>
                             ${event.mapLink ? `
                                <a href="${event.mapLink}" target="_blank" class="text-[10px] bg-green-600 text-white px-2 py-1 rounded-lg font-bold hover:bg-green-700 transition flex items-center">
                                    <i data-lucide="navigation" class="w-3 h-3 mr-1"></i> Directions
                                </a>
                             ` : ''}
                        </div>
                        <div class="bg-slate-50/80 p-3 rounded-2xl col-span-2 flex items-center group/item transition hover:bg-slate-100">
                             <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center mr-3 shrink-0">
                                <i data-lucide="phone" class="w-4 h-4 text-indigo-600"></i>
                             </div>
                             <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Contact Host</p>
                                <p class="text-xs font-bold text-slate-700">${event.hostMobile}</p>
                             </div>
                        </div>
                    </div>

                    <div class="mt-auto pt-6 border-t border-slate-50 flex justify-between items-center">
                        <div class="flex flex-col relative group/tooltip">
                            <div class="flex -space-x-2.5 avatar-stack cursor-help">
                                ${avatarImgs}
                            </div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-2">${event.totalGoing} joined</span>

                            <!-- HOVER TOOLTIP -->
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-48 bg-white p-3 rounded-xl shadow-xl border border-slate-100 hidden group-hover/tooltip:block z-50 tooltip-content">
                                <div class="text-[10px] uppercase font-bold text-slate-400 mb-2 border-b border-slate-50 pb-1 flex justify-between">
                                    <span>Attendees</span>
                                    <span class="text-slate-900 font-black">${event.totalGoing}</span>
                                </div>
                                ${tooltipRows}
                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-white"></div>
                            </div>
                        </div>
                        <button onclick="toggleJoin(this, ${event.id})" class="${btnClass}">
                            ${btnText}
                        </button>
                    </div>
                `;
                container.appendChild(card);
            });
            lucide.createIcons();
        }

        // 3. Filter Function
        function filterEvents(city) {
            // Update Buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                if (btn.innerText === city) {
                    btn.className = "filter-btn active bg-yellow-500 text-slate-900 px-5 py-2 rounded-full font-bold text-sm hover:bg-yellow-400 transition shadow-lg";
                } else {
                    btn.className = "filter-btn bg-slate-800 text-slate-300 border border-slate-700 px-5 py-2 rounded-full font-bold text-sm hover:text-white hover:border-yellow-500 transition";
                }
            });

            if (city === 'All') {
                renderEvents(eventsDB);
            } else {
                const filtered = eventsDB.filter(e => e.city === city);
                renderEvents(filtered);
            }
        }

        // 4. Handle Form Submit (Create or Update)
        async function handleFormSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = document.getElementById('submitBtn');
            const originalBtnText = submitBtn.innerText;
            const eventId = document.getElementById('editEventId').value;
            const isEdit = !!eventId;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerText = isEdit ? "Updating..." : "Creating...";

            try {
                const formData = new FormData(form);
                const endpoint = isEdit ? '../../api/events/update.php' : '../../api/events/create.php';

                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Failed to process request');
                }

                // Success
                alert(result.message || (isEdit ? "Event updated!" : "Event created!"));
                closeCreateModal();
                form.reset();

                // Refresh list
                loadEvents();

            } catch (error) {
                console.error('Error processing event:', error);
                alert(error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        }

        async function loadEvents() {
            try {
                const response = await fetch('../../api/events/list.php');
                if (!response.ok) throw new Error('Failed to load events');

                const result = await response.json();
                if (result.success && Array.isArray(result.data)) {
                    eventsDB = result.data; // Update local cache
                    renderEvents(eventsDB);
                }
            } catch (error) {
                console.error('Error loading events:', error);
                // Fallback to empty or error state
                document.getElementById('eventsContainer').innerHTML =
                    '<p class="text-center text-slate-500 col-span-3">No events found or failed to load.</p>';
            }
        }

        // 5. Join Interaction
        async function toggleJoin(btn, eventId) {
            const isJoining = btn.innerText === "I'm Coming";
            const action = isJoining ? 'join' : 'leave';

            // Temporary UI feedback
            const originalText = btn.innerText;
            btn.innerText = isJoining ? "Joining..." : "Leaving...";
            btn.disabled = true;

            try {
                const formData = new URLSearchParams();
                formData.append('event_id', eventId);
                formData.append('action', action);

                const response = await fetch('../../api/events/join.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Update UI locally or reload
                    loadEvents();
                } else {
                    alert(result.message || "Failed to update RSVP");
                    btn.innerText = originalText;
                }
            } catch (error) {
                console.error("Error toggling join:", error);
                alert("Something went wrong. Please try again.");
                btn.innerText = originalText;
            } finally {
                btn.disabled = false;
            }
        }

        async function deleteEvent(eventId) {
            if (!confirm("Are you sure you want to delete this event?")) return;

            try {
                const formData = new URLSearchParams();
                formData.append('event_id', eventId);

                const response = await fetch('../../api/events/delete.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    alert("Event deleted successfully!");
                    loadEvents();
                } else {
                    alert(result.message || "Failed to delete event");
                }
            } catch (error) {
                console.error("Error deleting event:", error);
                alert("Something went wrong.");
            }
        }

        // 5. Modal Logic
        let map = null;
        let mapMarker = null;

        function initMap() {
            if (map) return;

            // Centralize on Dhaka by default
            map = L.map('mapPicker').setView([23.8103, 90.4125], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            map.on('click', function (e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                updateMarker(lat, lng);
            });
        }

        function updateMarker(lat, lng) {
            if (mapMarker) {
                mapMarker.setLatLng([lat, lng]);
            } else {
                mapMarker = L.marker([lat, lng]).addTo(map);
            }
            // Use Google Maps format for the link
            document.getElementById('mapLinkInput').value = `https://www.google.com/maps?q=${lat},${lng}`;
            map.setView([lat, lng], 15);
        }

        function findMyLocation() {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser.");
                return;
            }

            const btn = event.currentTarget;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-3 h-3 mr-1 animate-spin"></i> Locating...';
            lucide.createIcons();

            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                updateMarker(lat, lng);
                btn.innerHTML = originalHtml;
                lucide.createIcons();
            }, (error) => {
                alert("Unable to retrieve your location.");
                btn.innerHTML = originalHtml;
                lucide.createIcons();
            });
        }

        function openCreateModal() {
            document.getElementById('modalTitle').innerHTML = '<i data-lucide="coffee" class="w-5 h-5 mr-2 text-yellow-400"></i> Host an Adda';
            document.getElementById('submitBtn').innerText = 'Create Event';
            document.getElementById('editEventId').value = '';
            document.getElementById('mapLinkInput').value = '';
            document.getElementById('eventForm').reset();

            if (mapMarker) {
                map.removeLayer(mapMarker);
                mapMarker = null;
            }

            lucide.createIcons();
            const modal = document.getElementById('createModal');
            const content = document.getElementById('createModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
                initMap();
                map.invalidateSize(); // Fix gray tiles issue
            }, 10);
        }

        function openEditModal(eventId) {
            const event = eventsDB.find(e => e.id == eventId);
            if (!event) return;

            document.getElementById('modalTitle').innerHTML = '<i data-lucide="edit-2" class="w-5 h-5 mr-2 text-yellow-400"></i> Edit Adda';
            document.getElementById('submitBtn').innerText = 'Update Event';
            document.getElementById('editEventId').value = event.id;

            const form = document.getElementById('eventForm');
            form.elements['title'].value = event.title;
            form.elements['date'].value = event.rawDate;
            form.elements['time'].value = event.rawTime;
            form.elements['city'].value = event.city;
            form.elements['location'].value = event.location;
            document.getElementById('mapLinkInput').value = event.mapLink;

            // Handle map marker for existing link
            if (event.mapLink && event.mapLink.includes('q=')) {
                const coords = event.mapLink.split('q=')[1].split(',');
                if (coords.length === 2) {
                    const lat = parseFloat(coords[0]);
                    const lng = parseFloat(coords[1]);
                    setTimeout(() => {
                        initMap();
                        updateMarker(lat, lng);
                    }, 50);
                }
            } else if (mapMarker) {
                map.removeLayer(mapMarker);
                mapMarker = null;
            }

            lucide.createIcons();
            const modal = document.getElementById('createModal');
            const content = document.getElementById('createModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
                initMap();
                map.invalidateSize();
            }, 10);
        }

        function closeCreateModal() {
            const modal = document.getElementById('createModal');
            const content = document.getElementById('createModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Initial Render
        document.addEventListener('DOMContentLoaded', () => {
            loadEvents(); // Load from API
            loadSession();
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function () {
            const navbar = document.getElementById('navbar');
            const navContainer = navbar.querySelector('.max-w-7xl');
            if (window.scrollY > 50) {
                navContainer.classList.remove('mt-4');
                navbar.classList.add('bg-slate-900/95');
            } else {
                navContainer.classList.add('mt-4');
                navbar.classList.remove('bg-slate-900/95');
            }
        });

        // 6. Session Management
        // 6. Session Management
        function setAuthUi(isLoggedIn, user) {
            const loginLink = document.getElementById('login-link');
            const profileDropdown = document.getElementById('profile-dropdown');
            const logoutBtn = document.getElementById('logout-btn-nav');
            const loginMobile = document.getElementById('login-link-mobile');
            const profileMobile = document.getElementById('profile-link-mobile');
            const hostAction = document.getElementById('host-action-container');

            if (isLoggedIn) {
                currentUserId = user.user_id; // Set global user ID

                if (loginLink) loginLink.classList.add('hidden');
                if (profileDropdown) profileDropdown.classList.remove('hidden');
                if (logoutBtn) logoutBtn.classList.remove('hidden');
                if (loginMobile) loginMobile.classList.add('hidden');
                if (profileMobile) profileMobile.classList.remove('hidden');
                if (hostAction) hostAction.classList.remove('hidden');

                const profilePhoto = document.getElementById('profile-photo');
                const profileName = document.getElementById('profile-name');

                if (user?.profile_photo && profilePhoto) {
                    let photoPath = user.profile_photo;
                    if (!photoPath.startsWith('http')) {
                        photoPath = photoPath.replace(/^(\.\.\/|\.\/|\/)/, '');
                        photoPath = '../../' + photoPath;
                    }
                    profilePhoto.src = photoPath;
                }
                if (user?.name && profileName) {
                    profileName.textContent = user.name.split(' ')[0];
                }

                // Load events only for valid session
                loadEvents();
            } else {
                // Should not happen due to PHP check, but as a fallback/clean redirect
                window.location.href = '../auth/login.html';
            }
        }

        async function loadSession() {
            try {
                const response = await fetch('../../api/auth/session.php', { credentials: 'same-origin' });
                const payload = await response.json();
                const data = payload?.data;
                setAuthUi(!!data?.logged_in, data);
            } catch (error) {
                setAuthUi(false, null);
            }
        }

        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            const iconMenu = document.getElementById('icon-menu');
            const iconClose = document.getElementById('icon-close');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                iconMenu.classList.add('hidden');
                iconClose.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                menu.classList.add('hidden');
                iconMenu.classList.remove('hidden');
                iconClose.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        loadSession();
    </script>
</body>

</html>