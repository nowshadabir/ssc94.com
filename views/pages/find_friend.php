<?php
/**
 * Find Friend - Active User Search
 * SSC Batch '94
 */

define('PROJECT_ROOT', dirname(dirname(dirname(__FILE__))));
$configPath = PROJECT_ROOT . '/config/config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    exit('Server configuration error.');
}

require_once $configPath;

$district = sanitize($_GET['district'] ?? '');
$upozilla = sanitize($_GET['upozilla'] ?? '');
$school = sanitize($_GET['school'] ?? '');
$name = sanitize($_GET['name'] ?? '');

$hasSearch = $district !== '' || $upozilla !== '' || $school !== '' || $name !== '';
$results = [];

if ($hasSearch) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        $where = ["u.status = 'active'"];
        $params = [];

        if ($district !== '') {
            $where[] = 'si.zilla LIKE ?';
            $params[] = '%' . $district . '%';
        }
        if ($upozilla !== '') {
            $where[] = 'si.union_upozilla LIKE ?';
            $params[] = '%' . $upozilla . '%';
        }
        if ($school !== '') {
            $where[] = 'si.school_name LIKE ?';
            $params[] = '%' . $school . '%';
        }
        if ($name !== '') {
            $where[] = 'u.full_name LIKE ?';
            $params[] = '%' . $name . '%';
        }

        $sql = "
            SELECT
                u.user_id,
                u.full_name,
                u.mobile,
                u.profile_photo,
                MAX(pr.job_business) as job_business,
                MAX(pr.current_location) as current_location,
                MAX(si.school_name) as school_name,
                MAX(si.zilla) as zilla,
                MAX(si.union_upozilla) as union_upozilla
            FROM users u
            LEFT JOIN user_present_info pr ON u.user_id = pr.user_id
            LEFT JOIN user_school_info si ON u.user_id = si.user_id
            WHERE " . implode(' AND ', $where) . "
            GROUP BY u.user_id
            ORDER BY u.full_name ASC
            LIMIT 200
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        logError('Find friend search error: ' . $e->getMessage());
        $results = [];
    }
}

$resultCount = count($results);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your School Friends - SSC Batch '94 Member Directory</title>
    <meta name="description"
        content="Search the SSC Batch 1994 alumni directory. Find your old school friends by district, school name, or name and reconnect with your batchmates.">
    <meta name="keywords"
        content="Find SSC 94 Friends, Batch 94 Directory, SSC 94 School Search, Alumni Registry Bangladesh">
    <link rel="canonical" href="https://ssc94.com/views/pages/find_friend.php">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://ssc94.com/views/pages/find_friend.php">
    <meta property="og:title" content="Find Your School Friends - SSC Batch '94 Member Directory">
    <meta property="og:description"
        content="Reconnect with your 1994 batchmates. Search our comprehensive directory of alumni.">
    <meta property="og:image" content="https://ssc94.com/assets/images/og-find.jpg">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Icons (Lucide) -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Righteous&family=Inter:wght@300;400;500;600;700&family=Merriweather:ital,wght@1,300&display=swap"
        rel="stylesheet">

    <style>
        /* Custom Theme Configuration */
        :root {
            --primary-navy: #0f172a;
            --accent-gold: #fbbf24;
            --alert-red: #ef4444;
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

        /* Card Hover Effects */
        .friend-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .friend-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Search Input Focus */
        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.3);
            border-color: var(--accent-gold);
        }

        /* Disabled select state */
        select:disabled {
            background-color: #f1f5f9;
            cursor: not-allowed;
            color: #94a3b8;
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
        // Safe navigation helper for demo environments
        function navigateTo(url) {
            try {
                window.location.href = url;
            } catch (e) {
                console.warn("Navigation prevented by environment:", e);
                alert("Navigation to '" + url + "' simulated (Environment restriction).");
            }
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
                <div class="flex items-center gap-3 cursor-pointer group" onclick="navigateTo('../../index.html')">
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
                        class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider text-white bg-white/10 transition-all">Find
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
                            onclick="navigateTo('../profile.php')">
                            <img id="profile-photo" src="https://i.pravatar.cc/300?u=guest"
                                class="w-8 h-8 rounded-full border-2 border-yellow-400/50">
                            <span id="profile-name" class="text-xs font-bold text-white tracking-wide">Member</span>
                        </div>
                    </div>

                    <div class="w-px h-6 bg-white/10 mx-1 hidden sm:block"></div>

                    <button id="logout-btn-nav"
                        class="hidden w-10 h-10 flex items-center justify-center rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all duration-300 group"
                        title="Logout" onclick="navigateTo('../../api/auth/logout.php')">
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

    <div class="relative z-10 max-w-5xl mx-auto px-4 text-center">
        <span
            class="inline-block py-1 px-3 rounded-full bg-slate-800 text-yellow-400 border border-yellow-500/30 text-xs font-bold tracking-[0.2em] mb-4">
            MEMBER DIRECTORY
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 brand-font">
            Find Your <span class="text-yellow-500">School Friends</span>
        </h1>
        <p class="text-slate-400 mb-10 max-w-2xl mx-auto text-lg">
            Enter location details and school name to find your batchmates.
        </p>

        <!-- SEARCH BAR -->
        <form method="get" action=""
            class="bg-white p-5 rounded-2xl shadow-2xl flex flex-col md:flex-row gap-4 items-center transform transition-all">

            <!-- 1. District Input -->
            <div class="relative w-full md:w-3/12">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1 text-left px-1">District</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="map-pin" class="h-4 w-4 text-slate-400"></i>
                    </div>
                    <input type="text" id="districtInput" name="district"
                        value="<?php echo htmlspecialchars($district); ?>"
                        class="search-input w-full pl-9 pr-4 py-3 rounded-xl border border-slate-200 text-slate-800 placeholder-slate-400 focus:border-yellow-400 transition bg-slate-50 font-semibold"
                        placeholder="e.g. Dhaka">
                </div>
            </div>

            <!-- 2. Union/Upozilla Input -->
            <div class="relative w-full md:w-3/12">
                <label
                    class="block text-xs font-bold text-slate-500 uppercase mb-1 text-left px-1">Union/Upozilla</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="map" class="h-4 w-4 text-slate-400"></i>
                    </div>
                    <input type="text" id="upozillaInput" name="upozilla"
                        value="<?php echo htmlspecialchars($upozilla); ?>"
                        class="search-input w-full pl-9 pr-4 py-3 rounded-xl border border-slate-200 text-slate-800 placeholder-slate-400 focus:border-yellow-400 transition bg-slate-50"
                        placeholder="e.g. Dhanmondi">
                </div>
            </div>

            <!-- 3. School Name Input -->
            <div class="relative w-full md:w-4/12">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1 text-left px-1">School
                    Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="school" class="h-4 w-4 text-slate-400"></i>
                    </div>
                    <input type="text" id="schoolInput" name="school" value="<?php echo htmlspecialchars($school); ?>"
                        class="search-input w-full pl-9 pr-4 py-3 rounded-xl border border-slate-200 text-slate-800 placeholder-slate-400 focus:border-yellow-400 transition bg-slate-50"
                        placeholder="e.g. Dhaka Residential">
                </div>
            </div>

            <!-- 4. Friend Name Input (Optional) -->
            <div class="relative w-full md:w-3/12">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1 text-left px-1">Friend Name
                    (Optional)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="user" class="h-4 w-4 text-slate-400"></i>
                    </div>
                    <input type="text" id="nameInput" name="name" value="<?php echo htmlspecialchars($name); ?>"
                        class="search-input w-full pl-9 pr-4 py-3 rounded-xl border border-slate-200 text-slate-800 placeholder-slate-400 focus:border-yellow-400 transition bg-slate-50"
                        placeholder="e.g. Rahim">
                </div>
            </div>

            <!-- 5. Search Button -->
            <div class="w-full md:w-2/12 pt-6 md:pt-5">
                <button type="submit"
                    class="w-full bg-yellow-500 text-slate-900 font-bold py-3 rounded-xl hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/30 flex justify-center items-center h-[50px]">
                    Find
                </button>
            </div>
        </form>
    </div>
    </div>

    <!-- RESULTS GRID -->
    <div class="flex-grow max-w-7xl mx-auto px-4 py-12 w-full min-h-[400px]">

        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold text-white flex items-center">
                <span id="resultCount"
                    class="bg-slate-200 text-white px-2 py-1 rounded-md text-sm mr-3 font-mono"><?php echo $resultCount; ?></span>
                Students Found
            </h2>
        </div>

        <!-- Initial Prompt State -->
        <div id="initialState" class="text-center py-12<?php echo $hasSearch ? ' hidden' : ''; ?>">
            <div
                class="bg-blue-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-500 animate-pulse">
                <i data-lucide="map-pin" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700">Select a Location</h3>
            <p class="text-slate-500">Please choose a district and school to view the student list.</p>
        </div>

        <!-- Empty State (Hidden by default) -->
        <div id="emptyState" class="text-center py-20<?php echo (!$hasSearch || $resultCount > 0) ? ' hidden' : ''; ?>">
            <div
                class="bg-slate-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                <i data-lucide="search-x" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700">No students found</h3>
            <p class="text-slate-500">Try changing the name or school.</p>
        </div>

        <!-- Grid Container -->
        <div id="friendsGrid"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6<?php echo $resultCount > 0 ? '' : ' hidden'; ?>">
            <?php foreach ($results as $friend): ?>
                <?php
                $photo = $friend['profile_photo'] ?? '';
                if ($photo === '') {
                    $photo = 'https://i.pravatar.cc/150?u=' . $friend['user_id'];
                } else {
                    $isHttp = strpos($photo, 'http://') === 0 || strpos($photo, 'https://') === 0;
                    if (!$isHttp) {
                        $photo = '../../assets/uploads/profiles/' . $photo;
                    }
                }
                $role = $friend['job_business'] ?? 'Batchmate';
                $schoolName = $friend['school_name'] ?? 'Not specified';
                $location = trim(($friend['union_upozilla'] ?? '') . ', ' . ($friend['zilla'] ?? ''), ', ');
                ?>
                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 friend-card flex flex-col items-center text-center group">
                    <div class="relative mb-4">
                        <img src="<?php echo htmlspecialchars($photo); ?>"
                            alt="<?php echo htmlspecialchars($friend['full_name']); ?>"
                            class="w-24 h-24 rounded-full object-cover border-4 border-slate-50 group-hover:border-yellow-100 transition-colors">
                    </div>

                    <h3 class="font-bold text-lg text-slate-800 leading-tight mb-1">
                        <?php echo htmlspecialchars($friend['full_name']); ?>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium bg-slate-100 px-2 py-1 rounded-full mb-4">
                        <?php echo htmlspecialchars($role); ?>
                    </p>

                    <div class="w-full text-left space-y-2 mb-4 bg-slate-50 p-3 rounded-lg">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">School</span>
                            <span
                                class="text-xs font-medium text-slate-700 leading-tight"><?php echo htmlspecialchars($schoolName); ?></span>
                        </div>
                        <div class="flex flex-col mt-2">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Location</span>
                            <span
                                class="text-xs font-medium text-slate-700"><?php echo htmlspecialchars($location ?: 'Not specified'); ?></span>
                        </div>
                        <div class="flex flex-col mt-2">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Mobile</span>
                            <span
                                class="text-xs font-medium text-slate-700"><?php echo htmlspecialchars($friend['mobile'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-8 mt-auto border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm">&copy; 2026 SSC Batch '94 Association</p>
        </div>
    </footer>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // Initialize Icons
        lucide.createIcons();

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
            } catch (error) {
                setAuthUi(false, null);
            }
        }

        // Mobile Menu Toggle Logic
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            const iconMenu = document.getElementById('icon-menu');
            const iconClose = document.getElementById('icon-close');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menu.classList.add('animate-fade-in-down');
                iconMenu.classList.add('hidden');
                iconClose.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                menu.classList.add('hidden');
                menu.classList.remove('animate-fade-in-down');
                iconMenu.classList.remove('hidden');
                iconClose.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        document.addEventListener('DOMContentLoaded', loadSession);
    </script>
</body>

</html>