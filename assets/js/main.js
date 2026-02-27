/**
 * SSC Batch '94 - Shared JavaScript Utilities
 */

// 1. Toast Notification System
function showToast(message, type = 'success') {
    // Remove existing toast if any
    const existingToast = document.querySelector('.ssc-toast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = `ssc-toast fixed top-24 right-4 md:right-8 z-[9999] flex items-center gap-3 bg-white p-4 rounded-2xl shadow-2xl border-l-4 min-w-[300px] max-w-md transform transition-all duration-300 translate-x-full opacity-0 ${
        type === 'success' ? 'border-emerald-500' : 
        type === 'error' ? 'border-red-500' : 'border-amber-500'
    }`;

    const icon = type === 'success' ? 'check-circle' : 
                 type === 'error' ? 'alert-circle' : 'info';
    const iconColor = type === 'success' ? 'text-emerald-500' : 
                      type === 'error' ? 'text-red-500' : 'text-amber-500';

    toast.innerHTML = `
        <div class="flex-shrink-0 ${iconColor}">
            <i data-lucide="${icon}" class="w-6 h-6"></i>
        </div>
        <div class="flex-grow">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">${type === 'success' ? 'Success' : type === 'error' ? 'Error' : 'Notice'}</p>
            <p class="text-sm font-medium text-slate-700">${message}</p>
        </div>
        <button class="text-slate-300 hover:text-slate-500 transition" onclick="this.parentElement.remove()">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    `;

    document.body.appendChild(toast);
    
    // Initialize Lucide icons for the new element
    if (window.lucide) {
        window.lucide.createIcons();
    }

    // Trigger animate in
    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
    });

    // Auto remove
    const timer = setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 5000);

    // Pause timer on hover
    toast.addEventListener('mouseenter', () => clearTimeout(timer));
}

// Override default alert (optional, but let's be explicit and replace calls)
// window.alert = function(msg) { showToast(msg, 'notice'); };

// 2. Navigation Helper
function navigateTo(url) {
    // Add nice fade or just go
    window.location.href = url;
}
