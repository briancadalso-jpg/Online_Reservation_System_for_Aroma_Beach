<?php
$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
redirect_if_logged_in();
?>
<section class="auth-layout" style="display: block; max-width: 600px; margin: 0 auto;">
    <section class="auth-panel bg-white/80 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-white/40">
        <div class="hero-badge inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider mb-4">Unified Portal</div>
        <h1 style="margin:0 0 8px; font-size:34px;">Welcome back</h1>
        <p class="muted" style="margin:0 0 24px;">Use the same connected system for guest bookings and administrator management.</p>

        <div id="loginNotice" class="notice"></div>
        <form id="loginForm" class="stack">
            <label class="block mb-4">
                <span class="block mb-2 text-sm font-semibold text-gray-700">Email address</span>
                <input type="email" name="admin_email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 transition-all" placeholder="name@example.com" required>
            </label>
            <label class="block mb-6">
                <span class="block mb-2 text-sm font-semibold text-gray-700">Password</span>
                <div class="relative">
                    <input id="loginPassword" type="password" name="admin_pass" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 transition-all" placeholder="Enter your password" required>
                    <button id="loginPasswordToggle" type="button" class="absolute inset-y-0 right-0 px-4 text-xs font-bold text-emerald-700 hover:text-emerald-800" aria-controls="loginPassword" aria-pressed="false">
                        Show
                    </button>
                </div>
            </label>
            <div class="auth-actions flex flex-col gap-3">
                <button type="submit" class="w-full text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-bold rounded-xl text-sm px-5 py-3.5 text-center transition-all">Sign In</button>
                <button type="reset" class="w-full text-gray-500 bg-white/50 hover:bg-gray-100 rounded-xl border border-gray-200 text-sm font-bold px-5 py-3.5 transition-all">Cancel</button>
            </div>
        </form>

        <p class="muted" style="margin:18px 0 0;">Need an admin account? <a href="<?php echo e(route_for_section('signup.php')); ?>" style="color:var(--sea); font-weight:700;">Create Account</a></p>
    </section>

</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
