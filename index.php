<?php
require_once __DIR__ . '/controller/system_core.php';

if (is_logged_in()) {
    header('Location: ' . role_home_url());
    exit();
}?>
<?php render_header('Welcome'); ?>
<style>
    .hero-bg {
        background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?php echo e(frontend_asset('campuyo2.png')); ?>');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-color: #064e3b; /* Dark green fallback matching the theme */
    }
</style>

    <!-- Hero Section -->
    <section class="hero-bg text-white py-32 px-4 sm:px-6 lg:px-8 min-h-screen flex items-center justify-center">
        <div class="max-w-screen-xl mx-auto text-center">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-7xl font-bold mb-8 bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent leading-tight">
                    Aroma Beach Resort
                </h1>
                <p class="text-xl md:text-2xl mb-12 text-gray-200 max-w-2xl mx-auto leading-relaxed">
                    Beachfront stays in Campuyo, Manjuyod.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center max-w-2xl mx-auto">
                    <a href="<?php echo e(route_for_section('cottages.php')); ?>" class="flex-1 w-full px-8 py-4 bg-green-700 text-white font-bold text-lg rounded-2xl hover:bg-green-800 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 text-center">
                        Browse Cottages & Book
                    </a>
                    <a href="<?php echo e(route_for_section('login.php')); ?>?type=admin" class="flex-1 w-full px-8 py-4 bg-gray-900 text-white font-bold text-lg rounded-2xl hover:bg-gray-800 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 text-center">
                        Admin Portal
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Preview -->
    <section class="py-24 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-screen-xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Why Choose Aroma?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Modern stays with easy booking.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-8 rounded-3xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="w-20 h-20 bg-green-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Beachfront Cottages</h3>
                    <p class="text-gray-600">Private seaside cottages with modern amenities.</p>
                </div>
                <div class="text-center p-8 rounded-3xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="w-20 h-20 bg-blue-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Easy Booking</h3>
                    <p class="text-gray-600">Simple online booking with real-time availability.</p>
                </div>
                <div class="text-center p-8 rounded-3xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="w-20 h-20 bg-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Guest Portal</h3>
                    <p class="text-gray-600">Manage your bookings and receipts online.</p>
                </div>
            </div>
        </div>
    </section>

<?php render_footer(); ?>
