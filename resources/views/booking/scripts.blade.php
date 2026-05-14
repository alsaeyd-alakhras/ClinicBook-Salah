<script>
    (function () {
        const STORAGE_KEY = 'maw3id_bookings';
        const POLL_INTERVAL = 12000;

        const $statusCard = $('#statusCard');
        const $bookingDate = $('#bookingDate');
        const $formCard = $('#bookingFormCard');
        const $closedCard = $('#closedCard');
        const $closedMessage = $('#closedMessage');
        const $closedTitle = $('#closedTitle');
        const $nextOpenWrap = $('#nextOpenWrap');
        const $nextOpenText = $('#nextOpenText');
        const $myBookingsCard = $('#myBookingsCard');
        const $myBookingsList = $('#myBookingsList');
        const $messageBox = $('#messageBox');
        const $submitBtn = $('#submitBtn');
        const $bookingForm = $('#bookingForm');
        const $visitTypeNotice = $('#visitTypeNotice');
        const $toggleDatePicker = $('#toggleDatePicker');
        const $datePickerPanel = $('#datePickerPanel');
        const $availableDatesList = $('#availableDatesList');
        const $bookingDateInput = $('#booking_date');

        const state = {
            fingerprint: null,
            legacyFingerprint: null,
            myBookings: [],
            latestStatus: null,
            availableDates: [],
            selectedBookingDate: null,
        };

        function getStorage() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) {
                    return { bookings: [], fingerprint: null };
                }
                const parsed = JSON.parse(raw);
                return {
                    bookings: Array.isArray(parsed.bookings) ? parsed.bookings : [],
                    fingerprint: parsed.fingerprint || null,
                    legacyFingerprint: parsed.legacyFingerprint || null,
                };
            } catch (e) {
                return { bookings: [], fingerprint: null, legacyFingerprint: null };
            }
        }

        function setStorage(payload) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
        }

        function generateFingerprint() {
            if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                return `cb_${window.crypto.randomUUID()}`;
            }

            return `cb_${Date.now().toString(36)}_${Math.random().toString(36).slice(2, 12)}`;
        }

        function isModernFingerprint(fingerprint) {
            return typeof fingerprint === 'string' && fingerprint.indexOf('cb_') === 0;
        }

        function ensureFingerprint() {
            const storage = getStorage();
            if (storage.fingerprint && !isModernFingerprint(storage.fingerprint)) {
                storage.legacyFingerprint = storage.fingerprint;
                storage.fingerprint = generateFingerprint();
                setStorage(storage);
            } else if (!storage.fingerprint) {
                storage.fingerprint = generateFingerprint();
                setStorage(storage);
            }
            state.fingerprint = storage.fingerprint;
            state.legacyFingerprint = storage.legacyFingerprint || null;
            state.myBookings = storage.bookings;
        }

        function syncBookings(serverBookings) {
            const storage = getStorage();
            storage.fingerprint = state.fingerprint;
            storage.legacyFingerprint = state.legacyFingerprint;
            storage.bookings = serverBookings;
            setStorage(storage);
            state.myBookings = serverBookings;
            renderMyBookings();
        }

        function localBookingIds() {
            return getStorage().bookings
                .map((booking) => Number(booking.id))
                .filter((id) => Number.isInteger(id) && id > 0)
                .join(',');
        }

        function showMessage(type, message) {
            $messageBox
                .removeClass('success error')
                .addClass(type)
                .html(message)
                .stop(true, true)
                .fadeIn(150);
        }

        function clearFieldErrors() {
            $('.field-error').text('');
        }

        function setFieldError(field, message) {
            $(`.field-error[data-field="${field}"]`).text(message);
        }

        function validateForm() {
            clearFieldErrors();
            let valid = true;

            const patientName = $('#patient_name').val().trim();
            const nationalId = $('#national_id').val().trim();
            const phone = $('#phone').val().trim();
            const age = Number($('#age').val());
            const visitType = $('input[name="visit_type"]:checked').val();
            const bookingDate = $bookingDateInput.val();

            if (!bookingDate) {
                setFieldError('booking_date', 'يرجى اختيار تاريخ الحجز.');
                valid = false;
            }

            if (!patientName || patientName.length > 100) {
                setFieldError('patient_name', 'الاسم مطلوب وبحد أقصى 100 حرف.');
                valid = false;
            }

            if (!/^\d{9,10}$/.test(nationalId)) {
                setFieldError('national_id', 'رقم الهوية يجب أن يكون من 9 إلى 10 أرقام.');
                valid = false;
            }

            if (!/^05\d{8}$/.test(phone)) {
                setFieldError('phone', 'رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.');
                valid = false;
            }

            if (!Number.isInteger(age) || age < 1 || age > 120) {
                setFieldError('age', 'العمر يجب أن يكون بين 1 و 120.');
                valid = false;
            }

            if (!visitType) {
                setFieldError('visit_type', 'يرجى اختيار نوع الكشفية.');
                valid = false;
            }

            return valid;
        }

        function setSubmitting(isSubmitting) {
            if (isSubmitting) {
                $submitBtn.prop('disabled', true).addClass('loading');
            } else {
                $submitBtn.prop('disabled', false).removeClass('loading');
            }
        }

        function renderStatus(data, options = {}) {
            state.latestStatus = data;
            state.availableDates = Array.isArray(data.available_dates) ? data.available_dates : [];

            const selectedStillAvailable = state.availableDates.some((date) => date.date === state.selectedBookingDate);
            if (!selectedStillAvailable) {
                state.selectedBookingDate = data.booking_date || (state.availableDates[0] ? state.availableDates[0].date : null);
            }

            const selectedDate = getSelectedDate();
            $bookingDateInput.val(state.selectedBookingDate || '');

            $bookingDate.text(selectedDate ? selectedDate.date_ar : (data.booking_date_ar || '-'));

            const isPickerOpen = $datePickerPanel.is(':visible');
            if (options.forceDateList || !isPickerOpen) {
                renderAvailableDates();
            }

            if (!isPickerOpen && options.animate !== false) {
                $statusCard.addClass('fade-update');
                setTimeout(() => $statusCard.removeClass('fade-update'), 420);
            }

            const myBookingsCount = Array.isArray(data.my_bookings)
                ? data.my_bookings.filter((booking) => booking.booking_date === state.selectedBookingDate).length
                : 0;
            const reachedLimit = myBookingsCount >= 2;

            if (selectedDate && reachedLimit) {
                $formCard.hide();
                $closedCard.show();
                $closedTitle.text('⚠️ تنبيه الحد الأعلى للحالات');
                $closedMessage.text('لقد وصلت إلى الحد الأعلى المسموح به من الحالات لهذا اليوم. لا يمكنك تسجيل حالات إضافية.');
                $nextOpenWrap.hide();
                renderVisitTypes(selectedDate.visit_types || {});
                return;
            }

            if (selectedDate) {
                $formCard.show();
                $closedCard.hide();
                $nextOpenWrap.hide();
                renderVisitTypes(selectedDate.visit_types || {});
            } else {
                $formCard.hide();
                $closedCard.show();
                $closedTitle.text('🔒 الحجز مغلق حالياً');
                renderVisitTypes(data.visit_types || {});
                $closedMessage.text(data.closed_message || 'الحجز مغلق حالياً.');

                if (data.next_opening_ar) {
                    $nextOpenText.text(data.next_opening_ar);
                    $nextOpenWrap.show();
                } else {
                    $nextOpenText.text('-');
                    $nextOpenWrap.hide();
                }
            }
        }

        function getSelectedDate() {
            return state.availableDates.find((date) => date.date === state.selectedBookingDate) || null;
        }

        function renderAvailableDates() {
            if (!state.availableDates.length) {
                $toggleDatePicker.prop('disabled', true).addClass('disabled');
                $availableDatesList.html('<div class="date-picker-empty">لا توجد أيام متاحة حالياً.</div>');
                return;
            }

            $toggleDatePicker.prop('disabled', false).removeClass('disabled');

            const html = state.availableDates.map((date) => {
                const isActive = date.date === state.selectedBookingDate;
                const dateParts = String(date.date_ar || '').split(' ');
                const dayName = dateParts.shift() || date.date_ar || '-';
                const dateText = dateParts.join(' ');

                return `
                    <button type="button" class="date-choice ${isActive ? 'active' : ''}" data-date="${date.date}" aria-pressed="${isActive ? 'true' : 'false'}">
                        <span>
                            <span class="date-choice-main">
                                <strong>${dayName}</strong>
                                <span class="date-choice-date">${dateText}</span>
                            </span>
                            <small>${isActive ? 'هذا موعدك الحالي، تقدر تغيّره من هنا.' : 'اختيار سريع وواضح لهذا اليوم.'}</small>
                        </span>
                    </button>
                `;
            }).join('');

            $availableDatesList.html(html);
        }

        function selectBookingDate(date) {
            state.selectedBookingDate = date;
            $bookingDateInput.val(date);
            closeDatePicker();
            renderStatus(state.latestStatus, { forceDateList: true, animate: false });
        }

        function openDatePicker() {
            $datePickerPanel.fadeIn(120);
            $toggleDatePicker.attr('aria-expanded', 'true');
            $('body').addClass('date-picker-open');
        }

        function closeDatePicker() {
            $datePickerPanel.fadeOut(120);
            $toggleDatePicker.attr('aria-expanded', 'false');
            $('body').removeClass('date-picker-open');
        }

        function renderVisitTypes(visitTypes) {
            const notices = [];

            ['strabismus', 'other'].forEach((type) => {
                const info = visitTypes[type] || {};
                const $input = $(`input[name="visit_type"][value="${type}"]`);
                const $option = $(`.visit-type-option[data-type="${type}"]`);
                const disabled = Boolean(info.is_closed) || Number(info.remaining || 0) <= 0;

                $input.prop('disabled', disabled);
                $option.toggleClass('disabled', disabled);

                if (disabled && $input.is(':checked')) {
                    $input.prop('checked', false);
                }

                if (info.is_closed && info.closed_message) {
                    notices.push(info.closed_message);
                }
            });

            if (notices.length) {
                $visitTypeNotice.html(notices.join('<br>')).show();
            } else {
                $visitTypeNotice.hide().empty();
            }
        }

        function renderMyBookings() {
            if (!state.myBookings.length) {
                $myBookingsCard.hide();
                $myBookingsList.empty();
                return;
            }

            $myBookingsCard.show();
            const html = state.myBookings.map((b, index) => {
                return `
                    <div class="my-booking-item">
                        <div>${index + 1}. ${b.patient_name} <small>(${b.visit_type_label || 'أخرى'} - ${b.booking_date_ar || b.booking_date || '-'})</small></div>
                        <button class="cancel-btn" data-id="${b.id}" data-name="${b.patient_name}">اعتذار</button>
                    </div>
                `;
            }).join('');

            $myBookingsList.html(html);
        }

        function fetchStatus() {
            return $.ajax({
                url: '/booking/status',
                method: 'GET',
                headers: {
                    'X-Device-Fingerprint': state.fingerprint,
                    'X-Legacy-Device-Fingerprint': state.legacyFingerprint || '',
                    'X-Local-Booking-Ids': localBookingIds()
                }
            }).done(function (response) {
                renderStatus(response);
                syncBookings(response.my_bookings || []);
            }).fail(function () {
                showMessage('error', 'تعذر تحديث حالة الحجز حالياً. يرجى المحاولة بعد قليل.');
            });
        }

        function submitBooking(e) {
            e.preventDefault();
            if (!validateForm()) {
                return;
            }

            setSubmitting(true);
            clearFieldErrors();

            $.ajax({
                url: '/booking',
                method: 'POST',
                data: {
                    patient_name: $('#patient_name').val().trim(),
                    national_id: $('#national_id').val().trim(),
                    phone: $('#phone').val().trim(),
                    age: $('#age').val(),
                    visit_type: $('input[name="visit_type"]:checked').val(),
                    booking_date: $bookingDateInput.val(),
                    fingerprint: state.fingerprint,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function (response) {
                showMessage('success', `✅ ${response.message}`);
                $bookingForm[0].reset();
                fetchStatus();
            }).fail(function (xhr) {
                const response = xhr.responseJSON || {};
                if (response.errors) {
                    Object.keys(response.errors).forEach((field) => {
                        setFieldError(field, response.errors[field][0]);
                    });
                    return;
                }

                if (response.field) {
                    setFieldError(response.field, response.message || 'قيمة غير صحيحة.');
                }

                showMessage('error', response.message || 'تعذر تنفيذ عملية الحجز.');
                fetchStatus();
            }).always(function () {
                setSubmitting(false);
            });
        }

        function cancelBooking(id, name) {
            if (!window.confirm(`هل تريد إلغاء حجز ${name}؟`)) {
                return;
            }

            $.ajax({
                url: `/booking/${id}`,
                method: 'DELETE',
                headers: {
                    'X-Device-Fingerprint': state.fingerprint,
                    'X-Legacy-Device-Fingerprint': state.legacyFingerprint || '',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function (response) {
                showMessage('success', response.message || 'تم إلغاء الحجز بنجاح.');
                fetchStatus();
            }).fail(function (xhr) {
                const response = xhr.responseJSON || {};
                showMessage('error', response.message || 'تعذر إلغاء الحجز.');
            });
        }

        function startPolling() {
            fetchStatus();
            setInterval(fetchStatus, POLL_INTERVAL);
        }

        $(document).on('submit', '#bookingForm', submitBooking);
        $(document).on('click', '#toggleDatePicker', function () {
            if (!state.availableDates.length) {
                return;
            }

            if ($datePickerPanel.is(':visible')) {
                closeDatePicker();
            } else {
                openDatePicker();
            }
        });
        $(document).on('click', '[data-close-date-picker]', closeDatePicker);
        $(document).on('keydown', function (event) {
            if (event.key === 'Escape' && $datePickerPanel.is(':visible')) {
                closeDatePicker();
            }
        });
        $(document).on('click', '.date-choice', function () {
            selectBookingDate($(this).data('date'));
        });
        $(document).on('click', '.cancel-btn', function () {
            cancelBooking($(this).data('id'), $(this).data('name'));
        });

        ensureFingerprint();
        renderMyBookings();
        startPolling();
    })();
</script>
