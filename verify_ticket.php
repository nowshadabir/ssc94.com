<?php
/**
 * Public Ticket Verification Page
 * SSC Batch '94
 */
require_once 'config/config.php';
$ticket = $_GET['t'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Ticket | SSC Batch '94</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Inter:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }

        .brand-font {
            font-family: 'Righteous', cursive;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-6">

    <div id="verifyContainer"
        class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
        <!-- Loading State -->
        <div id="loading" class="p-12 text-center">
            <div class="w-16 h-16 border-4 border-slate-100 border-t-indigo-600 rounded-full animate-spin mx-auto mb-4">
            </div>
            <p class="text-slate-500 font-medium">Verifying Ticket...</p>
        </div>

        <!-- Success State (Hidden) -->
        <div id="success" class="hidden">
            <div class="bg-emerald-500 p-8 text-center text-white">
                <div
                    class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i data-lucide="check-circle" class="w-12 h-12 text-white"></i>
                </div>
                <h1 class="text-2xl font-bold uppercase tracking-widest">Verified Ticket</h1>
                <p class="text-emerald-100 text-sm mt-1">Official Entry Pass Confirmed</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="flex items-center gap-4 border-b border-slate-100 pb-6">
                    <img id="userPhoto" src="" class="w-16 h-16 rounded-full border-2 border-slate-100 object-cover">
                    <div>
                        <h3 id="userName" class="text-xl font-bold text-slate-900 leading-tight">Name</h3>
                        <p id="userMobile" class="text-slate-500 text-sm">Mobile</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-4 text-sm">
                    <div>
                        <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Ticket ID</p>
                        <p id="ticketId" class="font-bold text-slate-900 brand-font text-lg">#00-0000</p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">T-Shirt Size</p>
                        <p id="tshirtSize" class="font-bold text-slate-900">N/A</p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Attendance</p>
                        <p id="guests" class="font-bold text-slate-900">Member + 0</p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Payment</p>
                        <span class="text-emerald-600 font-bold flex items-center gap-1">
                            <i data-lucide="shield-check" class="w-3 h-3"></i> Completed
                        </span>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                    <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Event Venue</p>
                    <p id="eventTitle" class="font-bold text-slate-900 text-sm"></p>
                    <p id="eventVenue" class="text-slate-500 text-xs mt-0.5"></p>
                </div>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-100 text-center">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Authorized by SSC Batch '94
                </p>
            </div>
        </div>

        <!-- Error State (Hidden) -->
        <div id="error" class="hidden p-12 text-center">
            <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-12 h-12"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Verification Failed</h2>
            <p id="errorMessage" class="text-slate-500 mb-8">This ticket could not be validated or is not yet confirmed.
            </p>
            <button onclick="window.close()"
                class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition">Close
                Window</button>
        </div>
    </div>

    <script shadow>
        lucide.createIcons();
        
        // Smart Ticket ID extraction (Handles '#' in URL manually typed)
        let ticketId = "<?= $ticket ?>";
        if (!ticketId) {
            const url = window.location.href;
            const match = url.match(/[?&]t=([^&]+)/);
            if (match) {
                ticketId = decodeURIComponent(match[1]);
            }
        }

        async function verify() {
            if (!ticketId) {
                showError("No ticket ID provided in the scan.");
                return;
            }

            try {
                const response = await fetch(`api/reunion/verify.php?ticket=${encodeURIComponent(ticketId)}`);
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('userName').innerText = data.full_name;
                    document.getElementById('userMobile').innerText = data.mobile;
                    document.getElementById('ticketId').innerText = data.ticket_number;
                    document.getElementById('tshirtSize').innerText = data.tshirt_size ? data.tshirt_size.toUpperCase() : 'N/A';

                    // Attendance Logic: 1 Member + N Guests
                    const gCount = parseInt(data.guest_count) || 0;
                    document.getElementById('guests').innerText = gCount > 0 ? `Member + ${gCount} Guest${gCount > 1 ? 's' : ''}` : 'Member Only';

                    // Decode HTML Entities for Venue/Title
                    const tempDiv = document.createElement("div");
                    tempDiv.innerHTML = data.reunion_title;
                    document.getElementById('eventTitle').innerText = tempDiv.innerText;
                    tempDiv.innerHTML = data.venue;
                    document.getElementById('eventVenue').innerText = tempDiv.innerText;

                    // Photo Path Fix
                    const photoPath = data.profile_photo ? `assets/uploads/profiles/${data.profile_photo}` : 'assets/images/default-profile.png';
                    document.getElementById('userPhoto').src = photoPath;

                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('success').classList.remove('hidden');
                } else {
                    showError(result.message);
                }
            } catch (err) {
                showError("Connection error. Please scan again.");
            }
            lucide.createIcons();
        }

        function showError(msg) {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error').classList.remove('hidden');
            document.getElementById('errorMessage').innerText = msg;
        }

        verify();
    </script>
</body>

</html>