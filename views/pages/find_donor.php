<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Blood Donor - Red Rescue | SSC Batch '94 Alumni</title>
    <meta name="description"
        content="Search for blood donors within the SSC Batch 1994 alumni network. Red Rescue is a dedicated wing for emergency blood support.">
    <meta name="keywords"
        content="SSC 94 Blood Donor, Red Rescue 94, Alumni Blood Bank, Emergency Blood Bangladesh, Batch 94 Social Welfare">
    <link rel="canonical" href="https://ssc94.com/views/pages/find_donor.php">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://ssc94.com/views/pages/find_donor.php">
    <meta property="og:title" content="Find Blood Donor - Red Rescue | SSC Batch '94 Alumni">
    <meta property="og:description"
        content="Need blood? Search our verified donor database of 1994 batchmates. Every drop counts.">
    <meta property="og:image" content="https://ssc94.com/assets/images/og-rescue.jpg">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Icons (Lucide) -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* Custom Theme Configuration */
        :root {
            --primary-navy: #0f172a;
            --accent-gold: #fbbf24;
            --rescue-red: #ef4444;
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

        /* Blood Drop Animation */
        .drop-shadow-glow {
            filter: drop-shadow(0 0 8px rgba(239, 68, 68, 0.4));
        }

        /* Card Hover */
        .donor-card {
            transition: all 0.3s ease;
        }

        .donor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(239, 68, 68, 0.15);
            border-color: #fca5a5;
        }

        /* Search Inputs */
        .search-input {
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--rescue-red);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
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
                    <a href="../../index.html"
                        class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white hover:bg-white/5 transition-all">Home</a>
                    <a href="find_friend.php"
                        class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white hover:bg-white/5 transition-all">Find
                        Friends</a>
                    <a href="events.php"
                        class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white hover:bg-white/5 transition-all">Events</a>
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
                    <a href="../../index.html"
                        class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 text-slate-300 font-bold hover:bg-slate-700 hover:text-white transition-all">
                        <div class="w-10 h-10 rounded-xl bg-slate-400/20 flex items-center justify-center"><i
                                data-lucide="home" class="w-5 h-5 text-slate-400"></i></div>
                        Home
                    </a>
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

    <!-- HEADER / SEARCH SECTION -->
    <div class="bg-slate-900 relative overflow-hidden pb-16 pt-12 border-b-4 border-red-600">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-red-900 opacity-95"></div>
            <!-- DNA Pattern -->
            <div
                class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/diagmonds-light.png')]">
            </div>
        </div>

        <div class="relative z-10 max-w-5xl mx-auto px-4 text-center">
            <span
                class="inline-flex items-center py-1 px-3 rounded-full bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-bold tracking-[0.2em] mb-4 animate-pulse">
                <i data-lucide="siren" class="w-4 h-4 mr-2"></i> EMERGENCY RESPONSE
            </span>
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4 brand-font">
                Find a <span class="text-red-500 drop-shadow-glow">Life Saver</span>
            </h1>
            <p class="text-slate-400 mb-10 max-w-2xl mx-auto text-lg">
                Search our database of 500+ verified batchmate donors. In emergencies, call directly.
            </p>

            <!-- SEARCH BAR COMPONENT -->
            <div
                class="bg-white p-6 rounded-3xl shadow-2xl flex flex-col lg:flex-row gap-4 items-end transform transition-all border-2 border-red-500/20 translate-y-8">

                <!-- 1. Blood Group (4/12) -->
                <div class="relative w-full lg:w-4/12">
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Blood Group</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="droplet" class="h-5 w-5 text-red-500"></i>
                        </div>
                        <select id="bloodInput"
                            class="search-input w-full pl-12 pr-10 py-3.5 rounded-2xl border border-slate-200 text-red-600 font-bold bg-red-50/50 appearance-none cursor-pointer focus:bg-white transition">
                            <option value="All">Any Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <i data-lucide="chevron-down" class="h-4 w-4 text-red-400"></i>
                        </div>
                    </div>
                </div>

                <!-- 2. Location (6/12) -->
                <div class="relative w-full lg:w-6/12">
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Location / Area (Smart
                        Suggest)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="map-pin" class="h-5 w-5 text-slate-400"></i>
                        </div>
                        <input type="text" id="districtInput" list="locationSuggestions"
                            class="search-input w-full pl-12 pr-4 py-3.5 rounded-2xl border border-slate-200 text-slate-700 bg-slate-50 focus:bg-white font-medium placeholder:text-slate-300"
                            placeholder="Type to search e.g. Uttara, Dhaka...">
                        <datalist id="locationSuggestions">
                            <!-- Populated via JS -->
                        </datalist>
                    </div>
                </div>

                <!-- 3. Action (2/12) -->
                <div class="w-full lg:w-2/12">
                    <button onclick="filterDonors()"
                        class="w-full bg-red-600 text-white font-black py-4 rounded-2xl hover:bg-red-700 transition shadow-lg shadow-red-500/30 flex justify-center items-center group">
                        <i data-lucide="filter" class="w-5 h-5 mr-2 group-hover:rotate-12 transition"></i>
                        SEARCH
                    </button>
                </div>
            </div>

            <div class="mt-4 flex justify-center gap-6 text-xs text-slate-400 font-medium">
                <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> Eligible to
                    Donate</span>
                <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-slate-500 mr-2"></span> Ineligible
                    (Recent)</span>
            </div>
        </div>
    </div>

    <!-- RESULTS GRID -->
    <div class="flex-grow max-w-7xl mx-auto px-4 py-12 w-full">

        <div class="flex justify-between items-end mb-6 border-b border-slate-200 pb-2">
            <h2 class="text-2xl font-bold text-slate-800 flex items-center">
                <span id="resultCount"
                    class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-lg mr-3 font-mono">0</span>
                Donors Found
            </h2>
            <div class="text-sm text-slate-500 hidden md:block">
                <i data-lucide="info" class="w-4 h-4 inline mr-1"></i> Verify before confirming
            </div>
        </div>

        <!-- Grid Container -->
        <div id="donorsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Cards will be injected here by JS -->
        </div>

        <!-- Initial State / Empty State -->
        <div id="initialState" class="text-center py-20">
            <div
                class="bg-slate-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i data-lucide="search" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700">Ready to Search</h3>
            <p class="text-slate-500">Select blood group and area to find donors.</p>
        </div>

        <!-- No Result Empty State -->
        <div id="emptyState" class="hidden text-center py-20">
            <div class="bg-red-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 text-red-300">
                <i data-lucide="search-x" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700">No donors found</h3>
            <p class="text-slate-500">Try expanding your search criteria.</p>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-8 mt-auto border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex justify-center items-center gap-2 mb-2">
                <i data-lucide="phone-call" class="w-4 h-4 text-red-500"></i>
                <span class="text-slate-200 font-bold">Emergency Hotline: 01711-000000</span>
            </div>
            <p class="text-sm text-slate-500">&copy; 2024 Red Rescue | SSC Batch '94 Association.</p>
        </div>
    </footer>

    <!-- JAVASCRIPT LOGIC -->
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

        // 1. Live Data
        let donorsDB = [];

        async function loadDonors() {
            try {
                const response = await fetch('../../api/user/find_donors.php');
                const data = await response.json();
                donorsDB = data;
                renderDonors(donorsDB);
            } catch (error) {
                console.error("Error loading donors:", error);
            }
        }

        // 2. Helper: Calculate Eligibility (3 months / 90 days gap)
        function checkEligibility(lastDate) {
            const today = new Date();
            const donated = new Date(lastDate);
            const diffTime = Math.abs(today - donated);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            return {
                isEligible: diffDays >= 90,
                daysAgo: diffDays
            };
        }

        // 3. Render Function
        function renderDonors(data) {
            const grid = document.getElementById('donorsGrid');
            const countLabel = document.getElementById('resultCount');
            const emptyState = document.getElementById('emptyState');

            grid.innerHTML = '';
            countLabel.innerText = data.length;

            if (data.length === 0) {
                grid.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            } else {
                grid.classList.remove('hidden');
                emptyState.classList.add('hidden');
            }

            data.forEach(donor => {
                const status = checkEligibility(donor.last_donated);

                // Card HTML
                const card = document.createElement('div');
                card.className = `bg-white rounded-2xl p-5 shadow-sm border border-slate-100 donor-card flex flex-col relative overflow-hidden group ${status.isEligible ? 'hover:border-green-200' : 'hover:border-slate-200 opacity-80'}`;

                // Status Badge
                const badgeClass = status.isEligible
                    ? 'bg-green-100 text-green-700 border-green-200'
                    : 'bg-slate-100 text-slate-500 border-slate-200';

                const badgeIcon = status.isEligible ? 'check-circle' : 'clock';
                const badgeText = status.isEligible ? 'Eligible' : `Wait ${90 - status.daysAgo}d`;

                card.innerHTML = `
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-red-50 text-red-600 font-bold text-xl w-14 h-14 rounded-full flex items-center justify-center border border-red-100 shadow-sm">
                            ${donor.blood}
                        </div>
                        <span class="flex items-center text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full border ${badgeClass}">
                            <i data-lucide="${badgeIcon}" class="w-3 h-3 mr-1"></i> ${badgeText}
                        </span>
                    </div>
                    
                    <h3 class="font-bold text-lg text-slate-800 leading-tight mb-1">${donor.name}</h3>
                    <div class="text-sm text-slate-500 mb-4 flex items-center">
                        <i data-lucide="map-pin" class="w-3 h-3 mr-1"></i> ${donor.area}, ${donor.district}
                    </div>
                    
                    <div class="w-full bg-slate-50 rounded-lg p-3 mb-4 border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase font-bold">Last Donation</p>
                        <p class="text-sm font-medium text-slate-700">${new Date(donor.last_donated).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}</p>
                    </div>

                    <div class="flex gap-2 mt-auto">
                        <button onclick="alert('Calling ${donor.phone}')" class="flex-1 ${status.isEligible ? 'bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-500/30' : 'bg-slate-200 text-slate-500 cursor-not-allowed'} py-2.5 rounded-lg font-bold transition flex items-center justify-center">
                            <i data-lucide="phone" class="w-4 h-4 mr-2"></i> Call
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });

            lucide.createIcons();
        }

        // 4. Filter Logic
        async function filterDonors() {
            const bloodVal = document.getElementById('bloodInput').value;
            const districtVal = document.getElementById('districtInput').value;
            const nameVal = ""; // Name search removed as per updated request

            const grid = document.getElementById('donorsGrid');
            const countLabel = document.getElementById('resultCount');
            const initialState = document.getElementById('initialState');

            // Show subtle loader in count
            countLabel.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>';
            initialState.classList.add('hidden');
            lucide.createIcons();

            try {
                const response = await fetch(`../../api/user/find_donors.php?blood_group=${bloodVal}&district=${districtVal}&name=${nameVal}`);
                const data = await response.json();
                renderDonors(data);
            } catch (error) {
                console.error("Filter error:", error);
                grid.innerHTML = '<div class="col-span-full py-20 text-red-500 font-bold">Failed to load result. Please try again.</div>';
            }
        }

        // Fetch Location Suggestions
        async function loadSuggestions() {
            try {
                const response = await fetch('../../api/user/get_locations.php');
                const locations = await response.json();
                const datalist = document.getElementById('locationSuggestions');
                datalist.innerHTML = locations.map(loc => `<option value="${loc}">`).join('');
            } catch (e) { console.error("Could not load suggestions"); }
        }

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

        // Auth UI Logic
        function setAuthUi(isLoggedIn, user) {
            const loginLink = document.getElementById('login-link');
            const profileDropdown = document.getElementById('profile-dropdown');
            const logoutBtn = document.getElementById('logout-btn-nav');
            const loginMobile = document.getElementById('login-link-mobile');
            const profileMobile = document.getElementById('profile-link-mobile');

            if (isLoggedIn) {
                if (loginLink) loginLink.classList.add('hidden');
                if (profileDropdown) profileDropdown.classList.remove('hidden');
                if (logoutBtn) logoutBtn.classList.remove('hidden');
                if (loginMobile) loginMobile.classList.add('hidden');
                if (profileMobile) profileMobile.classList.remove('hidden');

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
            } else {
                if (loginLink) loginLink.classList.remove('hidden');
                if (profileDropdown) profileDropdown.classList.add('hidden');
                if (logoutBtn) logoutBtn.classList.add('hidden');
                if (loginMobile) loginMobile.classList.remove('hidden');
                if (profileMobile) profileMobile.classList.add('hidden');
            }
        }

        async function loadSession() {
            try {
                const response = await fetch('../../api/auth/session.php', { credentials: 'same-origin' });
                const payload = await response.json();
                const data = payload?.data;
                setAuthUi(!!data?.logged_in, data);
                lucide.createIcons();
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

        // Initial Render
        document.addEventListener('DOMContentLoaded', () => {
            loadSuggestions(); // Only load locations for suggestions
            loadSession();
        });

    </script>
</body>

</html>