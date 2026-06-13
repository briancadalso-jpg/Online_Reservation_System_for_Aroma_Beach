<?php
$pageTitle = 'Receipt';
require_once __DIR__ . '/includes/header.php';

if (str_contains(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''), '/src/receipt.php')) {
    $resId = isset($_GET['res_id']) ? (int)$_GET['res_id'] : 0;
    $target = route_for_section('receipt.php');
    if ($resId > 0) $target .= '?res_id=' . $resId;
    header('Location: ' . $target);
    exit();
}

$reservationId = isset($_GET['res_id']) ? (int)$_GET['res_id'] : 0;
$reservationModel = new Reservation();
$reservation = $reservationModel->getById($reservationId);

if (!$reservation) {
    http_response_code(404);
    echo '<section class="card container mx-auto py-12 px-4"><h1>Receipt not found</h1><p class="muted">The selected reservation does not exist.</p></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit();
}

if (is_logged_in() && !is_admin() && (int) ($reservation['admin_id'] ?? 0) !== ($_SESSION['admin_id'] ?? 0)) {
    header('Location: ' . route_for_section('reservations.php'));
    exit();
}
?>
<style>
    @page {
        size: A4 portrait;
        margin: 15mm;
    }

    @media print {
        nav,
        footer,
        #mobile-menu,
        #route-loader,
        #app-modal {
            display: none !important;
        }

        html,
        body,
        main {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            width: auto !important;
        }

        main {
            min-height: auto !important;
        }

        section.print\:bg-white {
            background: #fff !important;
            padding: 0 !important;
        }
    }
</style>
<section class="bg-stone-50 py-12 print:bg-white">
    <div class="max-w-4xl mx-auto px-4 print:max-w-none print:px-0">
        <div class="flex items-center justify-between mb-6 print:hidden">
            <a href="reservations.php" class="inline-flex items-center px-5 py-3 rounded-xl bg-white border border-gray-300 text-gray-800 font-semibold hover:bg-gray-50 transition-colors">
                Back to Reservations
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-5 py-3 rounded-xl bg-green-700 text-white font-semibold hover:bg-green-800 transition-colors">
                Print Receipt
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden print:shadow-none print:border-0">
            <div class="bg-green-800 px-8 py-10 text-white">
                <div class="flex items-center gap-5 mb-8">
                    <img src="<?php echo e(frontend_asset('aromaresortlogo.png')); ?>" alt="Aroma Beach Resort" class="h-14 w-auto print:h-12 drop-shadow-lg">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight leading-none">Aroma Beach Resort</h1>
                        <p class="text-green-100 text-base mt-1">Reservation Receipt</p>
                    </div>
                </div>

                <div class="flex flex-col gap-2 text-sm md:flex-row md:items-center md:justify-between w-full border-t border-green-700/50 pt-6">
                    <p>Receipt for reservation #<?php echo (int) $reservation['res_id']; ?></p>
                    <p>Status: <span class="font-bold"><?php echo ucfirst(e($reservation['status'])); ?></span></p>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-12 border-b border-gray-100 pb-8 mb-8">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Guest Details</h2>
                        <div class="space-y-2 text-gray-600">
                            <p class="font-semibold text-gray-900"><?php echo e(trim($reservation['guest_fname'] . ' ' . $reservation['guest_lname'])); ?></p>
                            <p><?php echo e($reservation['guest_email']); ?></p>
                            <p><?php echo e($reservation['guest_phone']); ?></p>
                            <p><?php echo e($reservation['guest_address']); ?></p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Stay Details</h2>
                        <div class="space-y-2 text-gray-600">
                            <p><span class="font-semibold text-gray-900">Cottage:</span> <?php echo e($reservation['cottage_name']); ?></p>
                            <p><span class="font-semibold text-gray-900">Type:</span> <?php echo e($reservation['cottage_type']); ?></p>
                            <p><span class="font-semibold text-gray-900">Start:</span> <?php echo e(date('M d, Y h:i A', strtotime($reservation['start_datetime']))); ?></p>
                            <p><span class="font-semibold text-gray-900">End:</span> <?php echo e(date('M d, Y h:i A', strtotime($reservation['end_datetime']))); ?></p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 border border-gray-200 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">Payment Summary</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between text-gray-700">
                            <span>Cottage price</span>
                            <strong><?php echo e(format_currency((float) $reservation['base_amount'])); ?></strong>
                        </div>
                        <div class="flex items-center justify-between text-gray-700">
                            <span>VAT (12%)</span>
                            <strong><?php echo e(format_currency((float) $reservation['vat_amount'])); ?></strong>
                        </div>
                        <div class="flex items-center justify-between text-gray-700">
                            <span>Processing fee</span>
                            <strong><?php echo e(format_currency((float) $reservation['processing_fee'])); ?></strong>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 text-lg font-extrabold text-gray-900">
                            <span>Total amount due</span>
                            <strong><?php echo e(format_currency((float) $reservation['total_amount'])); ?></strong>
                        </div>
                        <div class="flex items-center justify-between text-gray-700">
                            <span>Amount paid</span>
                            <strong><?php echo e(format_currency((float) $reservation['amount_paid'])); ?></strong>
                        </div>
                        <div class="flex items-center justify-between text-gray-700">
                            <span>Balance due</span>
                            <strong><?php echo e(format_currency(max(0, (float) $reservation['total_amount'] - (float) $reservation['amount_paid']))); ?></strong>
                        </div>
                        <div class="flex items-center justify-between text-gray-700">
                            <span>Change</span>
                            <strong><?php echo e(format_currency((float) ($reservation['pay_change'] ?? max(0, (float) $reservation['amount_paid'] - (float) $reservation['total_amount'])))); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
