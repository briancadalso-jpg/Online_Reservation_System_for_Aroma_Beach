<?php
$pageTitle = 'Sign Up';
include __DIR__ . '/includes/header.php';
redirect_if_logged_in();
?>
<section class="auth-layout" style="display: block; max-width: 600px; margin: 0 auto;">
    <section class="auth-panel bg-white/80 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-white/40">
        <div class="hero-badge inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider mb-4">Admin Registration</div>
        <h1 style="margin:0 0 8px; font-size:34px;">Create an admin account</h1>
        <p class="muted" style="margin:0 0 24px;">All created accounts are administrator accounts. Guests can reserve cottages without signing up.</p>

        <div id="signupNotice" class="notice"></div>
        <form id="signupForm" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="block">
                    <span class="block mb-2 text-sm font-semibold text-gray-700">First name</span>
                    <input type="text" name="admin_fname" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 transition-all" required>
                </label>
                <label class="block">
                    <span class="block mb-2 text-sm font-semibold text-gray-700">Last name</span>
                    <input type="text" name="admin_lname" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 transition-all" required>
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="block">
                    <span class="block mb-2 text-sm font-semibold text-gray-700">Middle name</span>
                    <input type="text" name="admin_mname" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 transition-all">
                </label>
                <label class="block">
                    <span class="block mb-2 text-sm font-semibold text-gray-700">Phone number</span>
                    <input type="text" name="admin_phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 transition-all" required>
                </label>
            </div>
            <label class="block">
                <span class="block mb-2 text-sm font-semibold text-gray-700">Address</span>
                <textarea name="admin_address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 transition-all" placeholder="Complete address" required></textarea>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="block">
                    <span class="block mb-2 text-sm font-semibold text-gray-700">Email address</span>
                    <input type="email" name="admin_email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 transition-all" required>
                </label>
                <label class="block">
                    <span class="block mb-2 text-sm font-semibold text-gray-700">Password</span>
                    <input type="password" name="admin_pass" minlength="6" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 transition-all" required>
                </label>
            </div>
            <button type="submit" class="w-full mt-4 text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-bold rounded-xl text-sm px-5 py-3.5 text-center transition-all">Create Account</button>
        </form>

        <p class="muted" style="margin:18px 0 0;">Already registered? <a href="<?php echo e(route_for_section('login.php')); ?>" style="color:var(--sea); font-weight:700;">Sign in now</a>.</p>
    </section>

</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
