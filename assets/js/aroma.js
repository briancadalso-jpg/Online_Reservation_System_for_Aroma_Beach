function showNotice(id, type, message) {
    const notice = document.getElementById(id);
    if (!notice) return;
    notice.className = 'mb-4 rounded-xl px-4 py-3 text-sm';
    if (type === 'success') {
        notice.classList.add('bg-green-100', 'text-green-900', 'border', 'border-green-200');
    } else {
        notice.classList.add('bg-red-100', 'text-red-900', 'border', 'border-red-200');
    }
    notice.textContent = message;
}

const appModal = document.getElementById('app-modal');
const appModalTitle = document.getElementById('app-modal-title');
const appModalMessage = document.getElementById('app-modal-message');
const appModalConfirm = document.getElementById('app-modal-confirm');
const appModalCancel = document.getElementById('app-modal-cancel');
const appModalBackdrop = document.getElementById('app-modal-backdrop');
const routeLoader = document.getElementById('route-loader');
const routeLoaderText = document.getElementById('route-loader-text');
const authApiUrl = document.body?.dataset?.authApiUrl || 'controller/auth.php';
const cottagesApiUrl = document.body?.dataset?.cottagesApiUrl || 'controller/cottages.php';
const reservationsApiUrl = document.body?.dataset?.reservationsApiUrl || 'controller/reservations.php';
const adminHomeUrl = document.body?.dataset?.adminHomeUrl || '/aroma_resortsystem/public/admin/home.php';

function resolveRedirectUrl(url, fallback) {
    const candidate = url ? String(url).trim() : '';
    if (!candidate || candidate === 'undefined' || candidate === 'null' || candidate.includes('undefined')) {
        return fallback;
    }
    return candidate;
}

let appModalResolver = null;
let appModalClosing = false;
let loaderRequestCount = 0;
let pageLeaving = false;

function markPageReady() {
    document.body.classList.add('page-ready');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', markPageReady, { once: true });
} else {
    markPageReady();
}

function startPageTransition(callback, delay = 240) {
    if (pageLeaving) return;
    pageLeaving = true;
    document.body.classList.add('page-leaving');
    window.setTimeout(() => {
        callback();
    }, delay);
}

function closeAppModal(result = false) {
    if (!appModal || appModalClosing) return;
    appModalClosing = true;
    appModal.classList.remove('app-modal-open');
    appModal.classList.add('app-modal-closing');
    window.setTimeout(() => {
        appModal.classList.add('hidden');
        appModal.classList.remove('flex', 'app-modal-closing');
        appModalClosing = false;
        if (appModalResolver) {
            appModalResolver(result);
            appModalResolver = null;
        }
    }, 180);
}

function openAppModal({
    title = 'Notice',
    message = '',
    confirmText = 'OK',
    cancelText = 'Cancel',
    showCancel = false,
    tone = 'success'
} = {}) {
    if (!appModal || !appModalTitle || !appModalMessage || !appModalConfirm || !appModalCancel) {
        return Promise.resolve(true);
    }

    appModalTitle.textContent = title;
    appModalMessage.textContent = message;
    appModalConfirm.textContent = confirmText;
    appModalCancel.textContent = cancelText;
    appModalCancel.classList.toggle('hidden', !showCancel);

    appModalConfirm.className = 'inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white';
    if (tone === 'error') {
        appModalConfirm.classList.add('bg-red-600', 'hover:bg-red-700');
    } else {
        appModalConfirm.classList.add('bg-green-700', 'hover:bg-green-800');
    }

    appModal.classList.remove('hidden');
    appModal.classList.remove('app-modal-closing');
    appModal.classList.add('flex', 'app-modal-open');

    return new Promise((resolve) => {
        appModalResolver = resolve;
    });
}

function showRouteLoader(message = 'Loading') {
    if (!routeLoader) return;
    if (routeLoaderText) {
        routeLoaderText.textContent = message;
    }
    routeLoader.classList.remove('hidden');
    routeLoader.classList.add('flex');
}

function hideRouteLoader() {
    if (!routeLoader) return;
    routeLoader.classList.add('hidden');
    routeLoader.classList.remove('flex');
}

function beginOperationLoader(message = 'Processing') {
    loaderRequestCount += 1;
    showRouteLoader(message);
}

function endOperationLoader() {
    loaderRequestCount = Math.max(0, loaderRequestCount - 1);
    if (loaderRequestCount === 0) {
        hideRouteLoader();
    }
}

function navigateWithLoader(url, message = 'Loading', delay = 500) {
    const targetUrl = String(url || '').trim();

    if (!targetUrl || targetUrl === 'undefined' || targetUrl.endsWith('/undefined')) {
        console.error('Navigation aborted: Target URL is undefined.');
        return;
    }
    
    showRouteLoader(message);
    startPageTransition(() => {
        window.setTimeout(() => {
            window.location.href = targetUrl;
        }, delay);
    }, 180);
}

function reloadWithLoader(message = 'Loading', delay = 500) {
    showRouteLoader(message);
    startPageTransition(() => {
        window.setTimeout(() => {
            window.location.reload();
        }, delay);
    }, 180);
}

function showErrorModal(message, title = 'Error') {
    if (window.Swal) {
        return Swal.fire({
            icon: 'error',
            title,
            text: message
        });
    }

    if (!appModal) {
        alert(message);
        return Promise.resolve(true);
    }

    return openAppModal({
        title,
        message,
        confirmText: 'OK',
        showCancel: false,
        tone: 'error'
    });
}

function showSuccessModal(message, title = 'Success') {
    if (window.Swal) {
        return Swal.fire({
            icon: 'success',
            title,
            text: message
        });
    }

    if (!appModal) {
        return Promise.resolve(true);
    }

    return openAppModal({
        title,
        message,
        confirmText: 'OK',
        showCancel: false,
        tone: 'success'
    });
}

function showConfirmModal(message, title = 'Please Confirm') {
    if (window.Swal) {
        return Swal.fire({
            icon: 'question',
            title,
            text: message,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#15803d'
        }).then((result) => result.isConfirmed);
    }

    if (!appModal) {
        return Promise.resolve(confirm(message));
    }

    return openAppModal({
        title,
        message,
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        showCancel: true,
        tone: 'success'
    });
}

if (appModalConfirm) {
    appModalConfirm.addEventListener('click', () => closeAppModal(true));
}

if (appModalCancel) {
    appModalCancel.addEventListener('click', () => closeAppModal(false));
}

if (appModalBackdrop) {
    appModalBackdrop.addEventListener('click', () => closeAppModal(false));
}

async function postForm(url, formData, options = {}) {
    const { loaderMessage = 'Processing', showLoader = true } = options;
    if (showLoader) {
        beginOperationLoader(loaderMessage);
    }
    try {
        if (window.jQuery) {
            return await $.ajax({
                url,
                method: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false
            });
        }

        const response = await fetch(url, { method: 'POST', body: formData });
        let data;
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            data = await response.json();
        } else {
            throw new Error('Server returned an invalid response format.');
        }
        if (!response.ok || !data.success) throw new Error(data.message || 'Request failed.');
        return data;
    } catch (error) {
        const message = error.responseJSON?.message || error.message || 'Request failed.';
        throw new Error(message);
    } finally {
        if (showLoader) {
            endOperationLoader();
        }
    }
}

const loginForm = document.getElementById('loginForm');
const loginPassword = document.getElementById('loginPassword');
const loginPasswordToggle = document.getElementById('loginPasswordToggle');
const adminPortalButton = document.getElementById('adminPortalButton');
const adminPortalLoginForm = document.getElementById('adminPortalLoginForm');

if (loginPassword && loginPasswordToggle) {
    loginPasswordToggle.addEventListener('click', () => {
        const showingPassword = loginPassword.type === 'text';
        loginPassword.type = showingPassword ? 'password' : 'text';
        loginPasswordToggle.textContent = showingPassword ? 'Show' : 'Hide';
        loginPasswordToggle.setAttribute('aria-pressed', showingPassword ? 'false' : 'true');
    });
}

if (loginForm) {
    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        try {
            const data = await postForm(`${authApiUrl}?action=login`, new FormData(loginForm), { loaderMessage: 'Signing in' });
            showNotice('loginNotice', 'success', 'Login successful. Redirecting...');
            await showSuccessModal('Login successful. Redirecting to the admin dashboard.', 'Signed In');
            navigateWithLoader(resolveRedirectUrl(data?.redirect_url, adminHomeUrl), 'Signing in');
        } catch (error) {
            showNotice('loginNotice', 'error', error.message);
            await showErrorModal(error.message, 'Login Failed');
        }
    });
}

if (adminPortalButton && adminPortalLoginForm) {
    adminPortalButton.addEventListener('click', () => {
        adminPortalLoginForm.classList.remove('hidden');
        adminPortalLoginForm.querySelector('input[name="admin_email"]')?.focus();
    });

    adminPortalLoginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        try {
            const data = await postForm(`${authApiUrl}?action=login`, new FormData(adminPortalLoginForm), { loaderMessage: 'Signing in' });
            showNotice('adminPortalNotice', 'success', 'Login successful. Redirecting...');
            await showSuccessModal('Login successful. Redirecting to the admin dashboard.', 'Signed In');
            navigateWithLoader(resolveRedirectUrl(data?.redirect_url, adminHomeUrl), 'Opening admin portal', 500);
        } catch (error) {
            showNotice('adminPortalNotice', 'error', error.message);
            await showErrorModal(error.message, 'Admin Login Failed');
        }
    });
}

const signupForm = document.getElementById('signupForm');
if (signupForm) {
    signupForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const confirmed = await showConfirmModal('Create this account?', 'Create Account');
        if (!confirmed) return;
        try {
            const data = await postForm(`${authApiUrl}?action=signup`, new FormData(signupForm), { loaderMessage: 'Creating account' });
            showNotice('signupNotice', 'success', data.message);
            signupForm.reset();
            await showSuccessModal(data.message || 'Account created successfully.', 'Account Created');
            navigateWithLoader(document.body?.dataset?.loginUrl || 'login.php', 'Redirecting');
        } catch (error) {
            showNotice('signupNotice', 'error', error.message);
            await showErrorModal(error.message, 'Unable to Create Account');
        }
    });
}

const openCottageModalButton = document.getElementById('openCottageModal');

function escapeHtml(value = '') {
    return String(value).replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[character]);
}

function getCottageInputValue(popup, name) {
    return popup.querySelector(`[name="${name}"]`)?.value.trim() || '';
}

async function openCottageSwal(cottage = {}) {
    if (!window.Swal) {
        await showErrorModal('SweetAlert is required to open the cottage form.', 'Unable to Open Form');
        return;
    }

    const isUpdate = cottage.action === 'update';
    const title = isUpdate ? 'Update Cottage' : 'Add New Cottage';
    const imageHelp = isUpdate && cottage.imagePath
        ? 'Leave blank to keep the current photo.'
        : 'Upload a cottage image.';

    const result = await Swal.fire({
        title,
        width: 720,
        html: `
            <div class="swal-cottage-form">
                <label>
                    <span>Cottage Name</span>
                    <input name="cot_name" class="swal2-input" value="${escapeHtml(cottage.name)}">
                </label>
                <div class="swal-cottage-grid">
                    <label>
                        <span>Cottage Type</span>
                        <input name="cottage_type" class="swal2-input" value="${escapeHtml(cottage.type)}">
                    </label>
                    <label>
                        <span>Capacity</span>
                        <input name="cot_capacity" class="swal2-input" type="number" min="1" value="${escapeHtml(cottage.capacity)}">
                    </label>
                </div>
                <div class="swal-cottage-grid">
                    <label>
                        <span>Price per Day</span>
                        <input name="cot_price" class="swal2-input" type="number" step="0.01" min="0" value="${escapeHtml(cottage.price)}">
                    </label>
                    <label>
                        <span>Image</span>
                        <input name="image" class="swal2-file" type="file" accept="image/*">
                        <small>${escapeHtml(imageHelp)}</small>
                    </label>
                </div>
                <label>
                    <span>Description</span>
                    <textarea name="description" class="swal2-textarea">${escapeHtml(cottage.description)}</textarea>
                </label>
            </div>
            <style>
                .swal-cottage-form{
                    display:grid;
                    gap:14px;
                    text-align:left;
                }
                .swal-cottage-form label{
                    display:grid;
                    gap:6px;
                    margin:0;
                    color:#111827;
                    font-size:13px;
                    font-weight:700;
                }
                .swal-cottage-form .swal2-input,
                .swal-cottage-form .swal2-textarea,
                .swal-cottage-form .swal2-file{
                    box-sizing:border-box;
                    width:100%;
                    margin:0;
                    border:1px solid #d1d5db;
                    border-radius:10px;
                    box-shadow:none;
                    font-size:14px;
                }
                .swal-cottage-form .swal2-input{
                    height:44px;
                    padding:0 12px;
                }
                .swal-cottage-form .swal2-textarea{
                    min-height:100px;
                    padding:12px;
                    resize:vertical;
                }
                .swal-cottage-form .swal2-file{
                    padding:9px 12px;
                }
                .swal-cottage-grid{
                    display:grid;
                    gap:12px;
                    grid-template-columns:repeat(2,minmax(0,1fr));
                }
                .swal-cottage-form small{
                    color:#6b7280;
                    font-size:12px;
                    font-weight:500;
                }
                @media (max-width:640px){
                    .swal-cottage-grid{
                        grid-template-columns:1fr;
                    }
                }
            </style>
        `,
        showCancelButton: true,
        confirmButtonText: isUpdate ? 'Update Cottage' : 'Save Cottage',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#15803d',
        focusConfirm: false,
        preConfirm: () => {
            const popup = Swal.getPopup();
            const name = getCottageInputValue(popup, 'cot_name');
            const type = getCottageInputValue(popup, 'cottage_type');
            const capacity = getCottageInputValue(popup, 'cot_capacity');
            const price = getCottageInputValue(popup, 'cot_price');
            const description = getCottageInputValue(popup, 'description');
            const image = popup.querySelector('[name="image"]')?.files?.[0] || null;

            if (!name || !type || !capacity || !price || !description) {
                Swal.showValidationMessage('Please provide complete cottage details.');
                return false;
            }

            if (Number(capacity) <= 0 || Number(price) <= 0) {
                Swal.showValidationMessage('Capacity and price must be greater than zero.');
                return false;
            }

            if (!isUpdate && !image) {
                Swal.showValidationMessage('Please upload a cottage image.');
                return false;
            }

            const formData = new FormData();
            formData.append('action', isUpdate ? 'update' : 'create');
            formData.append('cot_id', cottage.id || '');
            formData.append('cot_name', name);
            formData.append('cottage_type', type);
            formData.append('cot_capacity', capacity);
            formData.append('cot_price', price);
            formData.append('description', description);
            if (image) formData.append('image', image);

            return formData;
        }
    });

    if (!result.isConfirmed || !result.value) return;

    if (!isUpdate) {
        const shouldAdd = await showConfirmModal('Add this cottage to the reservation system?', 'Add Cottage');
        if (!shouldAdd) return;
    }

    try {
        const data = await postForm(cottagesApiUrl, result.value, {
            loaderMessage: isUpdate ? 'Updating cottage' : 'Saving cottage'
        });
        await showSuccessModal(
            data.message || (isUpdate ? 'Cottage updated successfully.' : 'Cottage added successfully.'),
            isUpdate ? 'Cottage Updated' : 'Cottage Saved'
        );
        setTimeout(() => reloadWithLoader('Refreshing'), 800);
    } catch (error) {
        await showErrorModal(error.message, isUpdate ? 'Unable to Update Cottage' : 'Unable to Save Cottage');
    }
}

document.querySelectorAll('.cottage-edit-btn').forEach((button) => {
    button.addEventListener('click', () => {
        openCottageSwal({
            action: 'update',
            id: button.dataset.cotId || '',
            name: button.dataset.cotName || '',
            type: button.dataset.cottageType || '',
            price: button.dataset.cotPrice || '',
            capacity: button.dataset.cotCapacity || '',
            description: button.dataset.description || '',
            imagePath: button.dataset.imagePath || ''
        });
    });
});

if (openCottageModalButton) {
    openCottageModalButton.addEventListener('click', () => {
        openCottageSwal({ action: 'create' });
    });
}

document.querySelectorAll('.cottage-delete-btn').forEach((button) => {
    button.addEventListener('click', async () => {
        const confirmed = await showConfirmModal('Delete this cottage? Existing reservations tied to it will also be removed.', 'Delete Cottage');
        if (!confirmed) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('cot_id', button.dataset.cotId || '');

        try {
            await postForm(cottagesApiUrl, formData, { loaderMessage: 'Deleting cottage' });
            await showSuccessModal('Cottage deleted successfully.', 'Cottage Deleted');
            reloadWithLoader('Refreshing');
        } catch (error) {
            await showErrorModal(error.message, 'Unable to Delete Cottage');
        }
    });
});

const cottageSelect = document.getElementById('cottageSelect');
const startDatetime = document.getElementById('startDatetime');
const endDatetime = document.getElementById('endDatetime');
const estimateBox = document.getElementById('estimateBox');
const reservationForm = document.getElementById('reservationForm');
const guestCount = document.getElementById('guestCount');

function updateEstimate() {
    if (!estimateBox || !cottageSelect || !startDatetime || !endDatetime) return;

    const option = cottageSelect.options[cottageSelect.selectedIndex];
    const price = Number(option?.dataset?.cotPrice || 0);
    const capacity = Number(option?.dataset?.cotCapacity || 0);
    if (guestCount && capacity > 0) {
        guestCount.max = String(capacity);
        guestCount.placeholder = `Up to ${capacity} guests`;
    }

    const start = startDatetime.value ? new Date(startDatetime.value) : null;
    const end = endDatetime.value ? new Date(endDatetime.value) : null;
    let days = 1;

    if (start && end && end > start) {
        const diff = end.getTime() - start.getTime();
        days = Math.max(1, Math.ceil(diff / 86400000));
    }

    const base = price * days;
    const vat = Number((base * 0.12).toFixed(2));
    const fee = Number(estimateBox.dataset.processingFee || 50);
    const total = Number((base + vat + fee).toFixed(2));

    estimateBox.innerHTML = `
        <div class="flex items-center justify-between"><span>Cottage amount</span><strong>PHP ${base.toFixed(2)}</strong></div>
        <div class="flex items-center justify-between"><span>VAT (12%)</span><strong>PHP ${vat.toFixed(2)}</strong></div>
        <div class="flex items-center justify-between"><span>Processing fee</span><strong>PHP ${fee.toFixed(2)}</strong></div>
        <div class="flex items-center justify-between text-base font-bold text-gray-900 border-t border-green-200 pt-3"><span>Total</span><strong>PHP ${total.toFixed(2)}</strong></div>
    `;
}

[cottageSelect, startDatetime, endDatetime].forEach((element) => {
    if (element) {
        element.addEventListener('change', updateEstimate);
    }
});

if (reservationForm) {
    updateEstimate();
    reservationForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const guestDetails = await collectGuestReservationDetails();
        if (!guestDetails) return;
        const formData = new FormData(reservationForm);
        formData.append('action', 'create');
        Object.entries(guestDetails).forEach(([key, value]) => {
            formData.append(key, value);
        });

        try {
            const data = await postForm(reservationsApiUrl, formData, { loaderMessage: 'Submitting reservation' });
            showNotice('reservationNotice', 'success', data.message);
            reservationForm.reset();
            updateEstimate();
            const result = await Swal.fire({
                icon: 'success',
                title: 'Reservation Submitted',
                text: data.message || 'Reservation submitted successfully.',
                showCancelButton: Boolean(data.receipt_url),
                confirmButtonText: data.receipt_url ? 'Open Receipt' : 'OK',
                cancelButtonText: 'Close',
                confirmButtonColor: '#15803d'
            });
            if (result.isConfirmed && data.receipt_url) {
                navigateWithLoader(data.receipt_url, 'Opening receipt', 400);
            }
        } catch (error) {
            showNotice('reservationNotice', 'error', error.message);
            const conflict = error.message.toLowerCase().includes('already reserved');
            await showErrorModal(error.message, conflict ? 'Reservation Conflict' : 'Unable to Submit Reservation');
        }
    });
}

async function collectGuestReservationDetails() {
    if (!window.Swal) {
        return null;
    }

    const result = await Swal.fire({
        title: 'Guest Information',
        html: `
            <div class="swal2-grid">
                <div class="swal2-col">
                    <input id="swal-guest-fname" class="swal2-input" placeholder="First name">
                </div>
                <div class="swal2-col">
                    <input id="swal-guest-lname" class="swal2-input" placeholder="Last name">
                </div>

                <div class="swal2-col">
                    <input id="swal-guest-email" class="swal2-input" type="email" placeholder="Email address">
                </div>
                <div class="swal2-col">
                    <input id="swal-guest-phone" class="swal2-input" placeholder="Phone number">
                </div>

                <div class="swal2-col swal2-span-2">
                    <textarea id="swal-guest-address" class="swal2-textarea" placeholder="Complete address"></textarea>
                </div>
            </div>
            <style>
                .swal2-grid{
                    display:grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 12px;
                    align-items:start;
                }
                .swal2-col{
                    width:100%;
                }
                .swal2-span-2{
                    grid-column: 1 / -1;
                }
                .swal2-grid .swal2-input, .swal2-grid .swal2-textarea {
                    width: 100% !important;
                    margin: 0 !important;
                }
            </style>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Submit Reservation',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#15803d',
        preConfirm: () => {
            const details = {
                guest_fname: document.getElementById('swal-guest-fname')?.value.trim() || '',
                guest_lname: document.getElementById('swal-guest-lname')?.value.trim() || '',
                guest_email: document.getElementById('swal-guest-email')?.value.trim() || '',
                guest_phone: document.getElementById('swal-guest-phone')?.value.trim() || '',
                guest_address: document.getElementById('swal-guest-address')?.value.trim() || ''
            };
            if (Object.values(details).some((value) => value === '')) {
                Swal.showValidationMessage('Please complete all guest information fields.');
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(details.guest_email)) {
                Swal.showValidationMessage('Please provide a valid email address.');
                return false;
            }
            return details;
        }
    });

    return result.isConfirmed ? result.value : null;
}

document.querySelectorAll('.reservation-status-form').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const confirmed = await showConfirmModal('Save changes to this reservation?', 'Update Reservation');
        if (!confirmed) return;
        const formData = new FormData(form);
        formData.append('action', 'update');
        formData.append('res_id', form.dataset.resId || '');

        try {
            await postForm(reservationsApiUrl, formData, { loaderMessage: 'Saving reservation' });
            await showSuccessModal('Reservation updated successfully.', 'Reservation Saved');
            reloadWithLoader('Refreshing');
        } catch (error) {
            await showErrorModal(error.message, 'Unable to Save');
        }
    });
});

document.querySelectorAll('.reservation-delete-btn').forEach((button) => {
    button.addEventListener('click', async () => {
        const confirmed = await showConfirmModal('Delete this reservation?', 'Delete Reservation');
        if (!confirmed) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('res_id', button.dataset.resId || '');

        try {
            await postForm(reservationsApiUrl, formData, { loaderMessage: 'Deleting reservation' });
            await showSuccessModal('Reservation deleted successfully.', 'Reservation Deleted');
            reloadWithLoader('Refreshing');
        } catch (error) {
            await showErrorModal(error.message, 'Unable to Delete');
        }
    });
});

function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach((node) => {
        const endString = node.dataset.endDatetime || '';
        const end = endString ? new Date(endString).getTime() : Number(node.dataset.end || 0);
        const diff = end - Date.now();

        if (!end || Number.isNaN(end) || diff <= 0) {
            node.textContent = 'Expired';
            return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
        const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');
        node.textContent = `${hours}:${minutes}:${seconds}`;
    });
}

if (document.querySelector('.countdown')) {
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
}

document.querySelectorAll('[data-logout-link="true"]').forEach((link) => {
    link.addEventListener('click', async (event) => {
        event.preventDefault();
        const confirmed = await showConfirmModal('Are you sure you want to log out?', 'Log Out');
        if (confirmed) {
            navigateWithLoader(link.href, 'Signing out');
        }
    });
});

document.addEventListener('click', (event) => {
    if (event.defaultPrevented) return;

    const link = event.target.closest('a[href]');
    if (!link) return;

    const href = link.getAttribute('href') || '';
    if (
        href === '' ||
        href === 'undefined' ||
        href.startsWith('#') ||
        href.startsWith('javascript:') ||
        href.startsWith('mailto:') ||
        href.startsWith('tel:') ||
        link.hasAttribute('download') ||
        (link.getAttribute('target') && link.getAttribute('target') !== '_self')
    ) {
        return;
    }

    const destination = new URL(link.href, window.location.href);
    if (destination.origin !== window.location.origin) {
        return;
    }

    if (destination.href === window.location.href) {
        return;
    }

    event.preventDefault();
    startPageTransition(() => {
        window.location.href = destination.href;
    });
});
