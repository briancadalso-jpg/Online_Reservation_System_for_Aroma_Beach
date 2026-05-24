<?php
if (str_contains(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''), '/src/cottages.php')) {
    header('Location: ' . route_for_section('cottages.php'));
    exit();
}

if (str_contains(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''), '/public/admin/')) {
    require_admin();
}

$cottageModel = new Cottage();
$cottages = $cottageModel->getAll();
?>
<section class="relative bg-green-900 py-20">
  <div class="absolute inset-0 opacity-40 bg-cover bg-center" style="background-image: url('<?php echo e(frontend_asset('campuyo1.jpg')); ?>');"></div>
  <div class="container mx-auto px-5 relative text-center">
    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4"><?php echo is_admin() ? 'Cottage Management' : 'Explore Aroma Beach'; ?></h1>
    <p class="text-green-100 max-w-2xl mx-auto text-lg">
        <?php echo is_admin() ? 'Add cottage details and publish them into the live reservation flow.' : 'Take a look at our native cottages designed for comfort and authentic island living in Campuyo, Manjuyod.'; ?>
    </p>
  </div>
</section>

<?php if (is_admin()): ?>
    <!-- Admin-only utilities could go here -->
<?php endif; ?>


<section class="text-gray-600 body-font bg-stone-50 py-16">
  <div class="container px-5 mx-auto">
    <?php if (is_admin()): ?>
      <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h2 class="text-3xl font-bold text-gray-900">Cottage List</h2>
          <p class="text-sm text-gray-500">Add, update, or remove published cottages from one place.</p>
        </div>
        <button id="openCottageModal" type="button" class="inline-flex items-center justify-center rounded-xl bg-green-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-green-800">
          Add Cottage
        </button>
      </div>
    <?php endif; ?>
    <div class="flex flex-wrap -m-4">
      <?php if (!$cottages): ?>
        <div class="p-4 w-full">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">No cottages yet</h2>
                <p class="text-gray-500">Add the first cottage to start receiving reservations.</p>
            </div>
        </div>
      <?php else: ?>
<?php foreach ($cottages as $cottage): ?>
          <?php $imagePath = !empty($cottage['image_path']) ? route_url($cottage['image_path']) : frontend_asset('campuyo1.jpg'); ?>
          <div class="p-4 md:w-1/3 w-full">
            <div class="h-full bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100 group">
              <div class="relative overflow-hidden">
                <img class="lg:h-64 md:h-48 w-full object-cover object-center group-hover:scale-105 transition-transform duration-700" src="<?php echo e($imagePath); ?>" alt="<?php echo e($cottage['cot_name']); ?>">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                   <p class="text-white text-sm italic"><?php echo e($cottage['description']); ?></p>
                </div>
              </div>
              <div class="p-8">
                <div class="flex justify-between items-start mb-2">
                    <h2 class="tracking-widest text-xs title-font font-bold text-green-700 uppercase"><?php echo e($cottage['cottage_type']); ?></h2>
                    <span class="text-gray-900 font-bold text-lg"><?php echo e(format_currency((float) $cottage['cot_price'])); ?> <span class="text-gray-400 text-xs font-normal">/day</span></span>
                </div>
                <h1 class="title-font text-2xl font-bold text-gray-900 mb-4"><?php echo e($cottage['cot_name']); ?></h1>
                <p class="leading-relaxed mb-6 text-gray-500"><?php echo e($cottage['description']); ?></p>
                <hr class="mb-6 border-gray-100">
                <div class="flex items-center justify-between text-gray-400 text-sm">
                   <div class="flex flex-wrap gap-4">
                      <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg><?php echo (int) $cottage['cot_capacity']; ?> Pax</span>
                      <?php if (!is_admin()): ?>
                          <a href="<?php echo e(route_for_section('reservations.php')); ?>?cottage=<?php echo (int) $cottage['cot_id']; ?>" class="font-semibold text-green-700 hover:underline">Reserve Now</a>
                      <?php else: ?>
                          <div class="flex items-center gap-3">
                              <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 00-1 1v1H8a1 1 0 000 2h1v1a1 1 0 102 0V6h1a1 1 0 100-2h-1V3a1 1 0 00-1-1zM5 8a3 3 0 00-3 3v4a3 3 0 003 3h10a3 3 0 003-3v-4a3 3 0 00-3-3H5z"></path></svg>Published</span>
                              <button
                                  type="button"
                                  class="font-semibold text-green-700 hover:underline cottage-edit-btn"
                                  data-cot-id="<?php echo (int) $cottage['cot_id']; ?>"
                                  data-cot-name="<?php echo e($cottage['cot_name']); ?>"
                                  data-cottage-type="<?php echo e($cottage['cottage_type']); ?>"
                                  data-cot-price="<?php echo e((string) $cottage['cot_price']); ?>"
                                  data-cot-capacity="<?php echo (int) $cottage['cot_capacity']; ?>"
                                  data-description="<?php echo e($cottage['description']); ?>"
                                  data-image-path="<?php echo e((string) ($cottage['image_path'] ?? '')); ?>"
                              >
                                  Edit
                              </button>
                              <button type="button" class="font-semibold text-red-600 hover:underline cottage-delete-btn" data-cot-id="<?php echo (int) $cottage['cot_id']; ?>">
                                  Delete
                              </button>
                          </div>
                      <?php endif; ?>
                   </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>
