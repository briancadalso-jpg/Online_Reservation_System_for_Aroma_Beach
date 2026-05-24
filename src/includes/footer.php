<?php ?>
</main>

<?php if (in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'signup.php'], true)): ?>
</div>
<?php endif; ?>

<div id="route-loader" class="fixed inset-0 z-[100] hidden items-center justify-center bg-emerald-950/60 backdrop-blur-sm transition-all duration-300">
    <div class="flex flex-col items-center">
        <div class="h-14 w-14 animate-spin rounded-full border-4 border-emerald-200 border-t-white"></div>
        <p id="route-loader-text" class="mt-6 font-bold text-white tracking-[0.2em] uppercase text-xs">Processing</p>
    </div>
</div>

<div id="app-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div id="app-modal-backdrop" class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-sm overflow-hidden rounded-[2rem] bg-white shadow-2xl transition-all duration-200 border border-white/20">
        <div class="p-8">
            <h3 id="app-modal-title" class="text-xl font-extrabold text-gray-900 tracking-tight">Notice</h3>
            <p id="app-modal-message" class="mt-3 text-sm text-gray-500 leading-relaxed"></p>
            <div class="mt-8 flex justify-end gap-3">
                <button id="app-modal-cancel" type="button" class="rounded-xl px-5 py-2.5 text-sm font-bold text-gray-500 hover:bg-gray-100 transition-colors">Cancel</button>
                <button id="app-modal-confirm" type="button" class="rounded-xl bg-green-700 px-6 py-2.5 text-sm font-bold text-white hover:bg-green-800 shadow-md transition-all">OK</button>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white border-t border-gray-100 pt-20 pb-10">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
            <div class="md:col-span-4">
                <img src="<?= e(frontend_asset('aromaresortlogo.png')) ?>" alt="Aroma Beach Resort" class="h-12 w-auto mb-8 opacity-90">
                <p class="text-gray-500 text-sm leading-8 max-w-sm">
                    Escape to our native cottages designed for comfort and authentic island living in Campuyo, Manjuyod. Your gateway to the Sandbar.
                </p>
            </div>

            <div class="md:col-span-3">
                <h4 class="text-gray-900 font-bold text-sm uppercase tracking-widest mb-8">Navigation</h4>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="<?= route_for_section('home.php') ?>" class="text-gray-500 hover:text-green-700 transition-colors">Resort Home</a></li>
                    <li><a href="<?= route_for_section('cottages.php') ?>" class="text-gray-500 hover:text-green-700 transition-colors">Explore Cottages</a></li>
                    <li><a href="<?= route_for_section('reservations.php') ?>" class="text-gray-500 hover:text-green-700 transition-colors">Reservations</a></li>
                </ul>
            </div>

            <div class="md:col-span-3">
                <h4 class="text-gray-900 font-bold text-sm uppercase tracking-widest mb-8">Get in Touch</h4>
                <ul class="space-y-5 text-sm text-gray-500">
                    <li class="flex items-start gap-4">
                        <span class="text-green-700 font-bold uppercase text-[10px] tracking-tighter mt-1">Location</span>
                        <span class="leading-relaxed">Campuyo, Manjuyod, Negros Oriental, Philippines</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="text-green-700 font-bold uppercase text-[10px] tracking-tighter">Phone</span>
                        <span>+63 912 345 6789</span>
                    </li>
                </ul>
            </div>

            <div class="md:col-span-2">
                <h4 class="text-gray-900 font-bold text-sm uppercase tracking-widest mb-8">Portal</h4>
                <div class="flex flex-col gap-4">
                    <?php if (is_logged_in()): ?>
                        <a href="<?= route_for_section('logout.php') ?>" data-logout-link="true" class="text-xs font-bold text-red-600 hover:underline">Staff Sign Out</a>
                    <?php else: ?>
                        <a href="<?= route_for_section('login.php') ?>" class="inline-flex items-center justify-center rounded-xl bg-gray-50 px-4 py-3 text-xs font-bold text-gray-900 hover:bg-gray-100 transition-all border border-gray-100">Staff Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <p class="text-gray-400 text-[11px] font-medium tracking-wide">&copy; <?= date('Y') ?> Aroma Beach Resort Reservation System. Built for excellence.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
<script src="<?= frontend_asset('js/aroma.js') ?>"></script>
</body>
</html>
