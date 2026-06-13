
<?php
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
if (str_contains($scriptPath, '/src/reservations.php')) {
    $cottageId = isset($_GET['cottage']) ? (int)$_GET['cottage'] : 0;
    $target = route_for_section('reservations.php');
    if ($cottageId > 0) $target .= '?cottage=' . $cottageId;
    header('Location: ' . $target);
    exit();
}

if (str_contains($scriptPath, '/public/admin/')) require_admin();

$cottageModel = new Cottage();
$reservationModel = new Reservation();

$cottages = $cottageModel->getAll();
$selectedCottage = null;

if (!is_admin() && isset($_GET['cottage'])) {
    $selectedCottage = $cottageModel->getById((int) $_GET['cottage']);
}

$reservations = is_admin() ? $reservationModel->getAllWithDetails() : [];
?>
<section class="bg-stone-50 min-h-screen py-12">

    <div class="container mx-auto px-4">
        <?php if (!is_admin()): ?>
            <div class="mb-8 grid gap-6 lg:grid-cols-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Create Reservation</h2>
                    <p class="text-gray-500 mb-6">Pick your cottage and stay schedule. Guest details are collected in a pop-up before submission.</p>
                    <div id="reservationNotice" class="hidden mb-4 rounded-xl px-4 py-3 text-sm"></div>
                    <form id="reservationForm" class="space-y-4">
                <label>
                    Cottage
                    <select name="cot_id" id="cottageSelect" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        <option value="">Select a cottage</option>
                        <?php foreach ($cottages as $cottage): ?>
                            <option
                                value="<?php echo (int) $cottage['cot_id']; ?>"
                                data-cot-price="<?php echo e((string) $cottage['cot_price']); ?>"
                                data-cot-capacity="<?php echo (int) $cottage['cot_capacity']; ?>"
                                <?php echo ($selectedCottage && (int) $selectedCottage['cot_id'] === (int) $cottage['cot_id']) ? 'selected' : ''; ?>
                            >
                                <?php echo e($cottage['cot_name']); ?> | <?php echo e($cottage['cottage_type']); ?> | <?php echo format_currency((float)$cottage['cot_price']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div class="grid gap-4 md:grid-cols-2">
                    <label>
                        Start date and time
                        <input type="datetime-local" name="start_datetime" id="startDatetime" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                    </label>
                    <label>
                        End date and time
                        <input type="datetime-local" name="end_datetime" id="endDatetime" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label>
                        Number of guests
                        <input type="number" name="guests" id="guestCount" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                    </label>
                    <label>
                        Notes
                        <input type="text" name="notes" placeholder="Special requests or reminders" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                    </label>
                </div>
                <section class="bg-green-50 rounded-2xl p-5">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Estimated Charges</h3>
                    <div class="space-y-3 text-sm text-gray-700" id="estimateBox" data-processing-fee="<?php echo e((string) PROCESSING_FEE); ?>">
                        <div class="flex items-center justify-between"><span>Cottage amount</span><strong>PHP 0.00</strong></div>
                        <div class="flex items-center justify-between"><span>VAT (12%)</span><strong>PHP 0.00</strong></div>
                        <div class="flex items-center justify-between"><span>Processing fee</span><strong><?php echo e(format_currency(PROCESSING_FEE)); ?></strong></div>
                        <div class="flex items-center justify-between text-base font-bold text-gray-900 border-t border-green-200 pt-3"><span>Total</span><strong><?php echo e(format_currency(PROCESSING_FEE)); ?></strong></div>
                    </div>
                </section>
                <button type="submit" class="w-full text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-3 text-center transition-all">Submit Reservation</button>
            </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Reservation Guide</h2>
                    <div class="space-y-3 text-gray-600">
                        <p>Select a cottage and set both start and end date/time.</p>
                        <p>If the stay length is 1 day or less, the admin page will show a live countdown until the reservation ends.</p>
                        <p>Once approved, you can open and print the receipt from your reservation list below.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (is_admin()): ?>
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Reservation List</h1>
                <p class="text-green-700 font-medium">Manage and monitor guest cottage bookings</p>
            </div>
            <div class="mt-4 md:mt-0 text-sm text-gray-500 italic">Showing <?php echo count($reservations); ?> reservation(s)</div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <?php if (!$reservations): ?>
                <div class="p-8 text-gray-500">No reservations found yet.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold">Guest / Reservation</th>
                                <th scope="col" class="px-6 py-4 font-bold">Cottage</th>
                                <th scope="col" class="px-6 py-4 font-bold">Date</th>
                                <th scope="col" class="px-6 py-4 font-bold">Status</th>
                                <th scope="col" class="px-6 py-4 font-bold">Payment</th>
                                <th scope="col" class="px-6 py-4 font-bold text-right"><?php echo is_admin() ? 'Actions' : 'Receipt'; ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($reservations as $reservation): ?>
                                <?php $shortStay = reservation_has_short_stay($reservation); ?>
                                <?php
                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                if ($reservation['status'] === 'approved') {
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                } elseif (in_array($reservation['status'], ['cancelled', 'expired'], true)) {
                                    $statusClass = 'bg-red-100 text-red-800';
                                }
                                ?>
                                <tr class="hover:bg-green-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <?php if (is_admin()): ?>
                                            <div class="font-medium text-gray-900 whitespace-nowrap"><?php echo e(trim($reservation['guest_fname'] . ' ' . $reservation['guest_lname'])); ?></div>
                                            <div class="text-xs text-gray-500">#<?php echo (int) $reservation['res_id']; ?> | <?php echo e($reservation['email']); ?></div>
                                        <?php else: ?>
                                            <div class="font-medium text-gray-900 whitespace-nowrap">#<?php echo (int) $reservation['res_id']; ?></div>
                                            <div class="text-xs text-gray-500">Guests: <?php echo (int) $reservation['guests']; ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900"><?php echo e($reservation['cottage_name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($reservation['cottage_type']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-900"><?php echo e(date('M d, Y', strtotime($reservation['start_datetime']))); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e(date('h:i A', strtotime($reservation['start_datetime']))); ?> to <?php echo e(date('M d, Y h:i A', strtotime($reservation['end_datetime']))); ?></div>
                                        <?php if ($shortStay): ?>
                                            <div class="text-xs text-green-700 mt-1">Countdown: <span class="countdown" data-end-datetime="<?php echo e(str_replace(' ', 'T', $reservation['end_datetime'])); ?>">--:--:--</span></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="<?php echo e($statusClass); ?> text-xs font-semibold px-2.5 py-0.5 rounded-full"><?php echo ucfirst(e($reservation['status'])); ?></span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-700">
                                        <?php echo e(format_currency((float) $reservation['total_amount'])); ?>
                                        <div class="text-xs font-normal text-gray-500">Paid: <?php echo e(format_currency((float) $reservation['amount_paid'])); ?></div>
                                        <div class="text-xs font-normal text-gray-500">Change: <?php echo e(format_currency((float) ($reservation['pay_change'] ?? max(0, (float) $reservation['amount_paid'] - (float) $reservation['total_amount'])))); ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <?php if (is_admin()): ?>
                                            <div class="flex flex-col gap-3 items-end">
                                                <form class="reservation-status-form space-y-2" data-res-id="<?php echo (int) $reservation['res_id']; ?>">
                                                    <select name="status" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 p-2">
                                                        <?php foreach (['pending', 'approved', 'cancelled', 'expired'] as $status): ?>
                                                            <option value="<?php echo $status; ?>" <?php echo $reservation['status'] === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <input type="number" step="0.01" min="0" name="amount_paid" value="<?php echo e((string) $reservation['amount_paid']); ?>" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 p-2">
                                                    <div class="flex justify-end gap-2">
                                                        <button type="submit" class="font-medium text-green-700 hover:underline">Save</button>
                                                        <span class="text-gray-300">|</span>
                                                        <button type="button" class="font-medium text-red-600 hover:underline reservation-delete-btn" data-res-id="<?php echo (int) $reservation['res_id']; ?>">Delete</button>
                                                    </div>
                                                </form>

                                                <a href="<?php echo e(route_for_section('receipt.php')); ?>?res_id=<?php echo (int) $reservation['res_id']; ?>" target="_blank" class="font-medium text-green-700 hover:underline">
                                                    Open Receipt
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <a href="<?php echo e(route_for_section('receipt.php')); ?>?res_id=<?php echo (int) $reservation['res_id']; ?>" target="_blank" class="font-medium text-green-700 hover:underline">Open Receipt</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <span class="text-xs text-gray-500 italic">Showing <?php echo count($reservations); ?> of <?php echo count($reservations); ?> reservations</span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
