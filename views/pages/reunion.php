<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grand Reunion 2024 - SSC Batch '94 Alumni</title>
    <meta name="description"
        content="Register for the SSC Batch 1994 Grand Reunion. Join us for a day of nostalgia, cultural programs, and connecting with lifelong friends.">
    <meta name="keywords"
        content="SSC 94 Grand Reunion, Batch 94 30 Years, Alumni Celebration Dhaka, SSC 94 Registration">
    <link rel="canonical" href="https://ssc94.com/views/pages/reunion.php">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://ssc94.com/views/pages/reunion.php">
    <meta property="og:title" content="Grand Reunion 2024 - SSC Batch '94 Alumni">
    <meta property="og:description"
        content="30 Years of Brotherhood. Secure your spot for the biggest alumni gathering of the year.">
    <meta property="og:image" content="https://ssc94.com/assets/images/og-reunion.jpg">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Icons (Lucide) -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Shared Utilities -->
    <script src="../../assets/js/main.js"></script>

    <!-- Ticket Generation (QRCode) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Righteous&family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap"
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

        .script-font {
            font-family: 'Great Vibes', cursive;
        }

        /* Ticket Design */
        .ticket-edge {
            background-image: radial-gradient(circle at 10px 10px, transparent 10px, #0f172a 10px);
            background-size: 20px 40px;
            background-position: 0 10px;
        }

        /* Polaroid Effect */
        .polaroid {
            background: white;
            padding: 10px 10px 30px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: rotate(-2deg);
            transition: transform 0.3s ease;
        }

        .polaroid:hover {
            transform: rotate(0deg) scale(1.05);
            z-index: 10;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .polaroid:nth-child(even) {
            transform: rotate(2deg);
        }

        .polaroid:nth-child(even):hover {
            transform: rotate(0deg) scale(1.05);
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
</head>

<body class="antialiased text-slate-800 flex flex-col min-h-screen">

    <!-- PREMIUM MINIMAL NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div
                class="bg-slate-900/80 backdrop-blur-xl border border-white/10 rounded-2xl px-6 h-16 flex items-center justify-between shadow-2xl shadow-slate-900/50">

                <!-- Brand -->
                <div class="flex items-center gap-3 cursor-pointer group"
                    onclick="window.location.href='../../index.html'">
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
                            onclick="window.location.href='../profile.php'">
                            <img id="profile-photo" src="https://i.pravatar.cc/300?u=guest"
                                class="w-8 h-8 rounded-full border-2 border-yellow-400/50">
                            <span id="profile-name" class="text-xs font-bold text-white tracking-wide">Member</span>
                        </div>
                    </div>

                    <div class="w-px h-6 bg-white/10 mx-1 hidden sm:block"></div>

                    <button id="logout-btn-nav"
                        class="hidden w-10 h-10 flex items-center justify-center rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all duration-300 group"
                        title="Logout" onclick="window.location.href='../../api/auth/logout.php'">
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

    <!-- NO REUNION STATE -->
    <div id="no-reunion"
        class="hidden min-h-screen flex flex-col items-center justify-center p-6 text-center bg-slate-50">
        <div class="w-24 h-24 bg-slate-200 rounded-full flex items-center justify-center mb-6">
            <i data-lucide="calendar-off" class="w-12 h-12 text-slate-400"></i>
        </div>
        <h2 class="text-3xl font-bold text-slate-900 brand-font mb-4">No Upcoming Reunion</h2>
        <p class="text-slate-500 max-w-md mx-auto mb-8 italic">We are currently planning the next big gathering. Stay
            tuned for announcements!</p>
        <a href="index.html"
            class="bg-slate-900 text-white px-8 py-3 rounded-full font-bold hover:bg-slate-800 transition shadow-xl">Back
            to Home</a>
    </div>

    <!-- REUNION CONTENT -->
    <div id="reunion-content" class="hidden">
        <!-- HERO SECTION -->
        <div class="relative py-20 overflow-hidden">
            <div class="absolute inset-0 hidden">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-slate-900 hidden"></div>
            </div>

            <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
                <p class="text-yellow-400 script-font text-4xl mb-2">Let's Create Memories</p>
                <h1 id="hero-title"
                    class="text-5xl md:text-7xl font-bold text-white mb-6 brand-font drop-shadow-2xl tracking-wide">
                    GRAND REUNION <br /><span id="hero-year"
                        class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">2024</span>
                </h1>
                <p id="hero-details" class="text-slate-300 text-lg md:text-xl mb-10 max-w-2xl mx-auto">
                    30 Years later, the bell rings again. Join us for a day of laughter, nostalgia, and endless adda.
                </p>
                <div class="flex justify-center gap-4 text-white">
                    <div
                        class="bg-white/10 backdrop-blur border border-white/20 px-6 py-3 rounded-lg flex flex-col items-center">
                        <span id="date-day" class="text-2xl font-bold brand-font">15</span>
                        <span id="date-month" class="text-xs uppercase tracking-widest text-slate-400">DEC</span>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur border border-white/20 px-6 py-3 rounded-lg flex flex-col items-center">
                        <span id="date-time" class="text-2xl font-bold brand-font">09</span>
                        <span class="text-xs uppercase tracking-widest text-slate-400">START</span>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur border border-white/20 px-6 py-3 rounded-lg flex flex-col items-center">
                        <span id="date-weekday" class="text-2xl font-bold brand-font">FRI</span>
                        <span class="text-xs uppercase tracking-widest text-slate-400">DAY</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- EVENT DETAILS TICKET -->
        <div class="max-w-5xl mx-auto px-4 -mt-16 relative z-20 mb-20">
            <div
                class="bg-slate-900 text-white rounded-3xl shadow-2xl flex flex-col md:flex-row overflow-hidden border border-slate-700">
                <!-- Left: Info -->
                <div class="p-8 md:p-12 flex-1 relative">
                    <div class="absolute top-0 right-0 p-6 opacity-5">
                        <i data-lucide="ticket" class="w-48 h-48"></i>
                    </div>
                    <h3
                        class="text-2xl font-bold text-yellow-400 mb-6 uppercase tracking-widest border-b border-slate-700 pb-2">
                        Event Details</h3>

                    <ul class="space-y-6">
                        <li class="flex items-start">
                            <div class="bg-slate-800 p-3 rounded-lg mr-4 text-yellow-400">
                                <i data-lucide="map-pin" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">The Venue</h4>
                                <p id="venue-name" class="text-slate-400">Green View Resort</p>
                                <p id="venue-details" class="text-slate-500 text-sm">Gazipur, Dhaka</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-slate-800 p-3 rounded-lg mr-4 text-yellow-400">
                                <i data-lucide="utensils" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">The Feast</h4>
                                <p id="food-text" class="text-slate-400">Breakfast, Grand Buffet Lunch & Evening Snacks
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-slate-800 p-3 rounded-lg mr-4 text-yellow-400">
                                <i data-lucide="music" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Activities</h4>
                                <p id="activities-text" class="text-slate-400">Live Band, Raffle Draw & Cultural Program
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-slate-800 p-3 rounded-lg mr-4 text-yellow-400">
                                <i data-lucide="calendar-check" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Registration Deadline</h4>
                                <p id="deadline-text" class="text-slate-500 text-sm">Dec 10, 2024</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Right: Pricing (Perforated Look) -->
                <div
                    class="bg-yellow-500 text-slate-900 p-8 md:w-1/3 flex flex-col justify-center items-center relative">
                    <!-- Perforation Circles -->
                    <div class="absolute left-0 top-0 bottom-0 flex flex-col justify-between py-2 -ml-3">
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                        <div class="w-6 h-6 rounded-full bg-[#f8fafc] mb-2"></div>
                    </div>

                    <div class="text-center w-full">
                        <span
                            class="inline-block bg-slate-900 text-white text-xs font-bold px-3 py-1 rounded-full mb-4">REGISTRATION
                            FEE</span>

                        <div class="mb-6">
                            <h4 id="price-alumnus" class="text-4xl font-bold brand-font">৳ 0</h4>
                            <p class="text-sm font-bold opacity-80 uppercase">Per Alumnus</p>
                        </div>

                        <div class="mb-8 border-t border-slate-900/10 pt-4">
                            <h4 id="price-guest" class="text-2xl font-bold brand-font">৳ 0</h4>
                            <p class="text-sm font-bold opacity-80 uppercase">Spouse / Guest</p>
                        </div>

                        <div id="registration-action">
                            <button onclick="openRegistration()"
                                class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition shadow-xl transform hover:-translate-y-1">
                                Register Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FLASHBACK SECTION -->
    <div class="max-w-7xl mx-auto px-4 mb-20">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-white mb-2 brand-font">Flashback</h2>
            <p class="text-slate-500">Moments from our Silver Jubilee 2019</p>
        </div>

        <div class="flex flex-wrap justify-center gap-8">
            <div class="polaroid w-64">
                <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
                    class="w-full h-64 object-cover grayscale hover:grayscale-0 transition duration-500"
                    alt="2019 Group">
                <div class="text-center font-handwriting mt-3 font-bold text-slate-600">The Gang</div>
            </div>
            <div class="polaroid w-64">
                <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
                    class="w-full h-64 object-cover grayscale hover:grayscale-0 transition duration-500" alt="2019 Fun">
                <div class="text-center font-handwriting mt-3 font-bold text-slate-600">Late Night Adda</div>
            </div>
            <div class="polaroid w-64">
                <img src="https://images.unsplash.com/photo-1517457373958-b7bdd4587205?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
                    class="w-full h-64 object-cover grayscale hover:grayscale-0 transition duration-500"
                    alt="2019 Food">
                <div class="text-center font-handwriting mt-3 font-bold text-slate-600">Cultural Night</div>
            </div>
        </div>
    </div>

    <!-- REGISTRATION MODAL -->
    <div id="regModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/90 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl relative overflow-hidden">
            <div class="bg-yellow-500 p-4 flex justify-between items-center">
                <h3 class="text-slate-900 font-bold text-lg uppercase tracking-wider">Reunion Registration</h3>
                <button onclick="closeRegistration()" class="text-slate-800 hover:text-white transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="p-6 max-h-[80vh] overflow-y-auto">
                <form id="registrationForm" onsubmit="proceedToPay(event)" class="space-y-5">

                    <!-- ── GUEST 1: Logged-in Member ── -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div class="bg-slate-800 px-4 py-2 flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-yellow-400"></i>
                            <span class="text-xs font-bold text-white uppercase tracking-wider">Guest 1 — You
                                (Member)</span>
                        </div>
                        <div class="p-4 space-y-3 bg-slate-50">
                            <!-- Name (read-only) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Name</label>
                                    <input type="text" id="memberName" name="full_name"
                                        class="w-full border border-slate-200 p-2 rounded-lg bg-white text-slate-800 font-medium text-sm"
                                        placeholder="Loading..." readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mobile</label>
                                    <input type="tel" id="memberMobile" name="mobile"
                                        class="w-full border border-slate-200 p-2 rounded-lg bg-white text-slate-800 font-medium text-sm"
                                        placeholder="Loading..." readonly>
                                </div>
                            </div>
                            <!-- Gender -->
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Gender</label>
                                <div class="flex gap-2">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="gender" value="male" class="peer sr-only" checked>
                                        <div
                                            class="border rounded-md py-2 text-center text-sm font-bold peer-checked:bg-slate-900 peer-checked:text-white hover:bg-slate-100 transition">
                                            Male</div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="gender" value="female" class="peer sr-only">
                                        <div
                                            class="border rounded-md py-2 text-center text-sm font-bold peer-checked:bg-slate-900 peer-checked:text-white hover:bg-slate-100 transition">
                                            Female</div>
                                    </label>
                                </div>
                            </div>
                            <!-- T-Shirt -->
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">T-Shirt
                                    Size</label>
                                <div class="flex gap-2">
                                    <?php foreach (['S', 'M', 'L', 'XL', 'XXL'] as $size): ?>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="tshirt_size" value="<?= $size ?>" class="peer sr-only"
                                                <?= $size === 'XL' ? 'checked' : '' ?>>
                                            <div
                                                class="border rounded-md py-2 text-center text-xs font-bold peer-checked:bg-slate-900 peer-checked:text-white hover:bg-slate-100 transition">
                                                <?= $size ?>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── ADDITIONAL GUESTS ── -->
                    <div id="guestRows" class="space-y-3"></div>

                    <!-- Add Guest Controls -->
                    <div
                        class="flex items-center justify-between p-3 border border-dashed border-slate-300 rounded-xl bg-slate-50">
                        <div>
                            <p class="text-sm font-bold text-slate-700">Add Spouse / Guest</p>
                            <p class="text-xs text-slate-400">Up to 3 additional guests</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="changeGuestCount(-1)"
                                class="w-8 h-8 rounded-lg border border-slate-300 flex items-center justify-center hover:bg-slate-200 transition">
                                <i data-lucide="minus" class="w-4 h-4"></i>
                            </button>
                            <span id="guestCountDisplay"
                                class="text-lg font-bold text-slate-800 w-5 text-center">0</span>
                            <button type="button" onclick="changeGuestCount(1)"
                                class="w-8 h-8 rounded-lg border border-slate-300 flex items-center justify-center hover:bg-slate-200 transition">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <!-- hidden input for guest_count to keep API compat -->
                    <input type="hidden" id="guestCount" name="guest_count" value="0">

                    <!-- Total -->
                    <div class="bg-slate-100 p-4 rounded-lg flex justify-between items-center border border-slate-200">
                        <div>
                            <span class="font-bold text-slate-700 block">Total Payable</span>
                            <span class="text-xs text-slate-400" id="totalBreakdown">Member only</span>
                        </div>
                        <span class="font-bold text-2xl text-slate-900">৳ <span id="totalAmount">0</span></span>
                    </div>

                    <button type="submit"
                        class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 shadow-lg transition">
                        Proceed to Payment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-8 mt-auto border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm">&copy; 2026 SSC Batch '94 Association</p>
        </div>
    </footer>

    <script>
        // Global variables for pricing
        let BASE_FEE = 0;
        let GUEST_FEE = 0;

        async function initReunionPage() {
            try {
                const response = await fetch('../../api/reunion/get_active.php');
                const result = await response.json();

                if (result.success && result.data) {
                    const r = result.data.reunion;
                    const reg = result.data.user_registration;

                    document.getElementById('reunion-content').classList.remove('hidden');

                    // Update Title/Year
                    const titleParts = r.title.split(' ');
                    const year = titleParts[titleParts.length - 1];
                    const mainTitle = titleParts.slice(0, -1).join(' ');
                    document.getElementById('hero-title').innerHTML = `${mainTitle} <br /><span id="hero-year" class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">${year}</span>`;

                    // Parse Date
                    const dateObj = new Date(r.reunion_date);
                    document.getElementById('date-day').innerText = dateObj.getDate();
                    document.getElementById('date-month').innerText = dateObj.toLocaleString('default', { month: 'short' }).toUpperCase();
                    document.getElementById('date-weekday').innerText = dateObj.toLocaleString('default', { weekday: 'short' }).toUpperCase();
                    document.getElementById('date-time').innerText = r.reunion_time || '09';

                    // Update Venue & Details
                    document.getElementById('venue-name').innerText = r.venue;
                    document.getElementById('venue-details').innerText = r.venue_details;
                    document.getElementById('food-text').innerText = r.food_menu;
                    document.getElementById('activities-text').innerText = r.activities;
                    document.getElementById('deadline-text').innerText = r.registration_deadline ? `Deadline: ${new Date(r.registration_deadline).toLocaleDateString()}` : 'No specific deadline';

                    // Update Pricing
                    BASE_FEE = parseFloat(r.cost_alumnus);
                    GUEST_FEE = parseFloat(r.cost_guest);
                    document.getElementById('price-alumnus').innerText = `৳ ${BASE_FEE.toLocaleString()}`;
                    document.getElementById('price-guest').innerText = `৳ ${GUEST_FEE.toLocaleString()}`;

                    // Handle Existing Registration
                    if (reg) {
                        const actionDiv = document.getElementById('registration-action');
                        actionDiv.innerHTML = `
                            <div class="bg-slate-900 text-white p-4 rounded-xl shadow-xl text-center space-y-2 border border-yellow-500/30">
                                <div class="flex items-center justify-center gap-2 text-yellow-400 font-bold uppercase tracking-wider text-xs">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    Registration Confirmed
                                </div>
                                <div class="text-xl font-bold brand-font">TIK: ${reg.ticket_number}</div>
                                <button onclick="showTicket('${reg.ticket_number}', '${reg.qr_code_data}')" class="w-full bg-yellow-500 text-slate-900 py-2 rounded-lg font-bold text-sm hover:bg-yellow-400 transition transform hover:-translate-y-1">
                                    View Digital Ticket
                                </button>
                            </div>
                        `;
                        lucide.createIcons();
                    }

                    // Sync Modal
                    calculateTotal();

                } else {
                    document.getElementById('no-reunion').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error initializing reunion page:', error);
                document.getElementById('no-reunion').classList.remove('hidden');
            }
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

        let _isLoggedIn = false;
        let _userData = null;

        // Auth UI Logic
        function setAuthUi(isLoggedIn, user) {
            const loginLink = document.getElementById('login-link');
            const profileDropdown = document.getElementById('profile-dropdown');
            const logoutBtn = document.getElementById('logout-btn-nav');
            const loginMobile = document.getElementById('login-link-mobile');
            const profileMobile = document.getElementById('profile-link-mobile');

            if (isLoggedIn) {
                _isLoggedIn = true;
                _userData = user;
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

                // Update Reunion button only if not already registered
                const regAction = document.getElementById('registration-action');
                if (regAction && !regAction.innerText.includes('Registration Confirmed')) {
                    const regBtn = regAction.querySelector('button');
                    if (regBtn) {
                        regBtn.innerHTML = 'Register Now';
                        regBtn.classList.remove('bg-slate-700');
                        regBtn.classList.add('bg-slate-900');
                    }
                }
            } else {
                _isLoggedIn = false;
                _userData = null;
                if (loginLink) loginLink.classList.remove('hidden');
                if (profileDropdown) profileDropdown.classList.add('hidden');
                if (logoutBtn) logoutBtn.classList.add('hidden');
                if (loginMobile) loginMobile.classList.remove('hidden');
                if (profileMobile) profileMobile.classList.add('hidden');

                // Update Reunion button for public
                const regAction = document.getElementById('registration-action');
                if (regAction && !regAction.innerText.includes('Registration Confirmed')) {
                    const regBtn = regAction.querySelector('button');
                    if (regBtn) {
                        regBtn.innerHTML = '<i data-lucide="lock" class="w-4 h-4 inline mr-2 opacity-50"></i> Login to Register';
                        regBtn.classList.remove('bg-slate-900');
                        regBtn.classList.add('bg-slate-700');
                        lucide.createIcons();
                    }
                }
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

        // Mobile Menu Toggle
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

        // Show ticket for already registered users
        function showTicket(ticketNumber, qrData) {
            const modal = document.getElementById('regModal');
            const form = document.getElementById('registrationForm');

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            form.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="check-circle" class="w-12 h-12"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Registration Confirmed</h3>
                    <p class="text-slate-500 mb-6">You are already registered for this reunion.</p>
                    
                    <div class="bg-slate-100 p-6 rounded-2xl border border-dashed border-slate-300 mb-6">
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">Your Ticket Number</p>
                        <p class="text-3xl font-bold text-slate-900 brand-font mb-4">${ticketNumber}</p>
                        
                        <div id="qrcode" class="flex justify-center bg-white p-4 rounded-xl shadow-inner mx-auto" style="width: 160px; height: 160px;"></div>
                        <p class="text-[10px] text-slate-400 mt-2 italic font-medium">Scan this at the venue entrance</p>
                    </div>

                    <button onclick="closeRegistration()" class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition">
                        Close
                    </button>
                </div>
            `;
            lucide.createIcons();

            // Generate QR Code with Verification URL
            const verifyUrl = `${window.location.origin}${window.location.pathname.replace('views/pages/reunion.php', '')}verify_ticket.php?t=${encodeURIComponent(ticketNumber)}`;

            new QRCode(document.getElementById("qrcode"), {
                text: verifyUrl,
                width: 128,
                height: 128,
                colorDark: "#0f172a",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', () => {
            initReunionPage();
            lucide.createIcons();

            // ── Handle redirect back from payment gateway ──────────────────
            const urlParams = new URLSearchParams(window.location.search);
            const payStatus = urlParams.get('payment');
            const ticket = urlParams.get('ticket');
            const errMsg = urlParams.get('msg');

            if (payStatus === 'success' && ticket) {
                // Clean URL without page reload
                history.replaceState({}, '', window.location.pathname);
                // Wait briefly for initReunionPage data, then show ticket
                setTimeout(() => showTicket(ticket, null), 800);

            } else if (payStatus === 'failed') {
                history.replaceState({}, '', window.location.pathname);
                // Show payment failed banner
                const banner = document.createElement('div');
                banner.className = 'fixed top-20 left-1/2 -translate-x-1/2 z-50 bg-red-600 text-white px-6 py-3 rounded-xl shadow-xl text-sm font-bold flex items-center gap-2';
                banner.innerHTML = '<i data-lucide="alert-circle" class="w-4 h-4"></i> Payment failed. ' + (errMsg ? decodeURIComponent(errMsg) : 'Please try again.');
                document.body.appendChild(banner);
                lucide.createIcons();
                setTimeout(() => banner.remove(), 6000);

            } else if (payStatus === 'cancelled') {
                history.replaceState({}, '', window.location.pathname);
                const banner = document.createElement('div');
                banner.className = 'fixed top-20 left-1/2 -translate-x-1/2 z-50 bg-amber-500 text-white px-6 py-3 rounded-xl shadow-xl text-sm font-bold flex items-center gap-2';
                banner.innerHTML = '<i data-lucide="x-circle" class="w-4 h-4"></i> Payment was cancelled. You can try registering again.';
                document.body.appendChild(banner);
                lucide.createIcons();
                setTimeout(() => banner.remove(), 6000);
            }
        });


        // ─── Modal Logic ───────────────────────────────────────────────
        const modal = document.getElementById('regModal');

        let _guestCount = 0;
        const MAX_GUESTS = 3;
        const SIZES = ['S', 'M', 'L', 'XL', 'XXL'];

        function openRegistration() {
            if (!_isLoggedIn) {
                // If not logged in, show a message or redirect
                const confirmLogin = confirm("You must be logged in as a member to register for the Grand Reunion. Would you like to log in now?");
                if (confirmLogin) {
                    window.location.href = '../auth/login.html?redirect=pages/reunion.php';
                }
                return;
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Pre-fill member info from session
            if (_userData) {
                const n = document.getElementById('memberName');
                const m = document.getElementById('memberMobile');
                if (n) n.value = _userData.name || '';
                if (m) m.value = _userData.mobile || '';
            } else {
                fetch('../../api/auth/session.php', { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(payload => {
                        const d = payload?.data;
                        if (d) {
                            const n = document.getElementById('memberName');
                            const m = document.getElementById('memberMobile');
                            if (n) n.value = d.name || '';
                            if (m) m.value = d.mobile || '';
                        }
                    }).catch(() => { });
            }
            calculateTotal();
        }

        function closeRegistration() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function buildSizeOptions(prefix, idx) {
            return SIZES.map(s => `
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="${prefix}_tshirt_${idx}" value="${s}" class="peer sr-only" ${s === 'M' ? 'checked' : ''}>
                    <div class="border rounded-md py-1.5 text-center text-xs font-bold peer-checked:bg-slate-900 peer-checked:text-white hover:bg-slate-100 transition">${s}</div>
                </label>`).join('');
        }

        function addGuestRow(idx) {
            const container = document.getElementById('guestRows');
            const div = document.createElement('div');
            div.id = `guestRow_${idx}`;
            div.className = 'border border-slate-200 rounded-xl overflow-hidden';
            div.innerHTML = `
                <div class="bg-slate-700 px-4 py-2 flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4 text-yellow-400"></i>
                    <span class="text-xs font-bold text-white uppercase tracking-wider">Guest ${idx + 1} — Spouse / Guest</span>
                </div>
                <div class="p-4 space-y-3 bg-slate-50">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Name</label>
                        <input type="text" id="guestName_${idx}" placeholder="Guest's full name"
                            class="w-full border border-slate-200 p-2 rounded-lg bg-white text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Gender</label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="guest_gender_${idx}" value="male" class="peer sr-only" checked>
                                <div class="border rounded-md py-2 text-center text-sm font-bold peer-checked:bg-slate-900 peer-checked:text-white hover:bg-slate-100 transition">Male</div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="guest_gender_${idx}" value="female" class="peer sr-only">
                                <div class="border rounded-md py-2 text-center text-sm font-bold peer-checked:bg-slate-900 peer-checked:text-white hover:bg-slate-100 transition">Female</div>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">T-Shirt Size</label>
                        <div class="flex gap-2">${buildSizeOptions('g', idx)}</div>
                    </div>
                </div>`;
            container.appendChild(div);
            lucide.createIcons();
        }

        function removeGuestRow(idx) {
            const el = document.getElementById(`guestRow_${idx}`);
            if (el) el.remove();
        }

        function changeGuestCount(delta) {
            const next = _guestCount + delta;
            if (next < 0 || next > MAX_GUESTS) return;

            if (delta > 0) {
                addGuestRow(_guestCount);   // zero-indexed label inside
            } else {
                removeGuestRow(_guestCount - 1);
            }
            _guestCount = next;
            document.getElementById('guestCountDisplay').textContent = _guestCount;
            document.getElementById('guestCount').value = _guestCount;
            calculateTotal();
        }

        function calculateTotal() {
            const total = BASE_FEE + (_guestCount * GUEST_FEE);
            document.getElementById('totalAmount').innerText = total.toLocaleString();
            const breakdown = _guestCount === 0
                ? 'Member only'
                : `Member + ${_guestCount} guest${_guestCount > 1 ? 's' : ''}`;
            const bd = document.getElementById('totalBreakdown');
            if (bd) bd.textContent = breakdown;
        }

        async function proceedToPay(e) {
            e.preventDefault();
            const form = document.getElementById('registrationForm');
            const btn = form.querySelector('button[type="submit"]');

            // Collect member gender
            const memberGender = form.querySelector('input[name="gender"]:checked')?.value || 'male';

            // Collect per-guest info from dynamic rows
            const guestsData = [];
            for (let i = 0; i < _guestCount; i++) {
                const name = document.getElementById(`guestName_${i}`)?.value?.trim() || '';
                const gender = form.querySelector(`input[name="guest_gender_${i}"]:checked`)?.value || 'male';
                const tshirt = form.querySelector(`input[name="g_tshirt_${i}"]:checked`)?.value || 'M';
                guestsData.push({ name, gender, tshirt });
            }

            const formData = new FormData(form);
            formData.set('gender', memberGender);
            formData.set('guests_data', JSON.stringify(guestsData));

            btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin inline mr-2"></i> Processing...';
            btn.disabled = true;
            lucide.createIcons();

            try {
                // ── Single step: send all form data to initiate_payment.php ───
                // No DB row is created here. The registration is only saved after
                // the payment gateway confirms the transaction successfully.
                btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin inline mr-2"></i> Opening Payment...';
                lucide.createIcons();

                const payResponse = await fetch('../../api/reunion/initiate_payment.php', {
                    method: 'POST',
                    body: formData      // contains all fields: name, mobile, tshirt, gender, guest_count, guests_data
                });
                const payResult = await payResponse.json();

                if (!payResult.success) {
                    showToast(payResult.message || 'Failed to initiate payment. Please try again.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = 'Proceed to Payment';
                    return;
                }

                // ── Redirect to Rupantorpay checkout ───────────────────────────
                // payment_success.php will INSERT the registration row upon confirmation
                window.location.href = payResult.data.paymentURL;

            } catch (error) {
                console.error('Payment initiation error:', error);
                showToast('Connection error. Please try again.', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Proceed to Payment';
            }

        }


        // Show ticket for already registered users
        function showTicket(ticketNumber, qrData) {
            const modal = document.getElementById('regModal');
            const form = document.getElementById('registrationForm');

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            form.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="check-circle" class="w-12 h-12"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Registration Confirmed</h3>
                    <p class="text-slate-500 mb-6">You are already registered for this reunion.</p>
                    
                    <div class="bg-slate-100 p-6 rounded-2xl border border-dashed border-slate-300 mb-6">
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">Your Ticket Number</p>
                        <p class="text-3xl font-bold text-slate-900 brand-font mb-4">${ticketNumber}</p>
                        
                        <div id="qrcode" class="flex justify-center bg-white p-4 rounded-xl shadow-inner mx-auto" style="width: 160px; height: 160px;"></div>
                        <p class="text-[10px] text-slate-400 mt-2 italic font-medium">Scan this at the venue entrance</p>
                    </div>

                    <button onclick="window.location.reload()" class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition">
                        Done
                    </button>
                </div>
            `;
            lucide.createIcons();

            // Generate QR Code with Verification URL
            const verifyUrl = `${window.location.origin}${window.location.pathname.replace('views/pages/reunion.php', '')}verify_ticket.php?t=${encodeURIComponent(ticketNumber)}`;

            new QRCode(document.getElementById("qrcode"), {
                text: verifyUrl,
                width: 128,
                height: 128,
                colorDark: "#0f172a",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }




        // Close modal on outside click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeRegistration();
        });

        loadSession();
    </script>
</body>

</html>