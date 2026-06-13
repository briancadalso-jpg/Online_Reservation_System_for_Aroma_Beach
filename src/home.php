<?php
$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>
<section
    class="relative isolate min-h-[calc(100vh-72px)] overflow-hidden bg-emerald-950"
    style="background-image:linear-gradient(90deg, rgba(6,78,59,.86), rgba(6,78,59,.48), rgba(15,23,42,.18)), url('<?php echo e(frontend_asset('campuyo1.jpg')); ?>'); background-size:cover; background-position:center;"
>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-emerald-950/70 to-transparent"></div>

    <div class="relative mx-auto flex min-h-[calc(100vh-72px)] max-w-screen-xl flex-col justify-center px-4 py-16 text-white">
        <?php
        $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $isAdminPortal = str_contains($scriptPath, '/public/admin/');
        $isGuestPortal = str_contains($scriptPath, '/public/users/');

        if ($isAdminPortal) require_admin();

        $isAdminPortal = $isAdminPortal && is_admin();
        ?>
        <div class="max-w-3xl">
            <img
                src="<?php echo e(frontend_asset('aromaresortlogo.png')); ?>"
                alt="Aroma Beach Resort logo"
                class="mb-6 h-20 w-auto drop-shadow-lg"
            >
            <p class="mb-4 text-sm font-bold uppercase tracking-[0.28em] text-emerald-100">
                <?php echo $isAdminPortal ? 'Admin Portal' : ($isGuestPortal ? 'Guest Portal' : 'Reservation System'); ?>
            </p>
            <h1 class="text-5xl font-extrabold leading-tight tracking-tight drop-shadow-lg md:text-7xl">
                Aroma Beach Resort
            </h1>
            <p class="mt-5 max-w-2xl text-xl font-medium leading-8 text-emerald-50 drop-shadow md:text-2xl">
                Relax, reserve, and reconnect by the shore.
            </p>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/85 md:text-lg">
                <?php if ($isAdminPortal): ?>
                    Manage cottages, reservations, approvals, payments, and receipts from one connected resort dashboard.
                <?php elseif ($isGuestPortal): ?>
                    Browse cottages and book your next beachside stay with a clear reservation and receipt experience.
                <?php else: ?>
                    Welcome to the Aroma Beach Resort management and reservation system. Please select your destination to continue.
                <?php endif; ?>
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                <?php if ($isAdminPortal): ?>
                    <a href="<?php echo e(route_for_section('reservations.php')); ?>" class="inline-flex items-center justify-center rounded-lg bg-white px-6 py-3 text-sm font-bold text-emerald-950 shadow-lg hover:bg-emerald-50">
                        Manage Reservations
                    </a>
                    <a href="<?php echo e(route_for_section('cottages.php')); ?>" class="inline-flex items-center justify-center rounded-lg border border-white/70 px-6 py-3 text-sm font-bold text-white hover:bg-white/10">
                        Manage Cottages
                    </a>
                <?php elseif ($isGuestPortal): ?>
                    <a href="<?php echo e(route_for_section('cottages.php')); ?>" class="inline-flex items-center justify-center rounded-lg bg-white px-6 py-3 text-sm font-bold text-emerald-950 shadow-lg hover:bg-emerald-50">
                        View Cottages
                    </a>
                    <a href="<?php echo e(route_for_section('reservations.php')); ?>" class="inline-flex items-center justify-center rounded-lg border border-white/70 px-6 py-3 text-sm font-bold text-white hover:bg-white/10">
                        Make Reservation
                    </a>
                    <a href="<?php echo e(route_url('/')); ?>" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-6 py-3 text-sm font-bold text-white/70 hover:bg-white/10 transition-colors">
                        Back to Portal Selection
                    </a>
                <?php else: ?>
                    <div class="grid sm:grid-cols-2 gap-4 w-full max-w-lg">
                        <a href="<?php echo e(route_for_section('login.php')); ?>" class="group flex flex-col items-center justify-center rounded-2xl bg-white p-6 text-center shadow-xl transition-all hover:bg-emerald-50">
                            <span class="mb-2 text-xs font-bold uppercase tracking-wider text-emerald-800 opacity-60">Staff Access</span>
                            <span class="text-xl font-extrabold text-emerald-950">Admin Portal</span>
                            <p class="mt-2 text-sm text-emerald-900/60 font-medium leading-relaxed">Manage bookings, cottages, and resort operations.</p>
                        </a>
                        <a href="<?php echo e(route_for_section('home.php', 'users')); ?>" class="group flex flex-col items-center justify-center rounded-2xl border border-white/30 bg-white/10 p-6 text-center backdrop-blur-md transition-all hover:bg-white/20">
                            <span class="mb-2 text-xs font-bold uppercase tracking-wider text-emerald-100 opacity-60">Visitor Access</span>
                            <span class="text-xl font-extrabold text-white">Guest Portal</span>
                            <p class="mt-2 text-sm text-white/70 font-medium leading-relaxed">Explore our cottages and reserve your beach stay.</p>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
