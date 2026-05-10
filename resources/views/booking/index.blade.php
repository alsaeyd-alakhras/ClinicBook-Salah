<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>موعد - تسجيل حجز المريض</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    @include('booking.styles')
</head>
<body>
    <main class="booking-shell">
        <header class="clinic-head card">
            <div class="clinic-logo-wrap">
                <img id="clinicLogo" src="{{ $clinicLogo ? asset('storage/' . $clinicLogo) : asset('imgs/logo-brand.png') }}" alt="شعار العيادة" class="clinic-logo">
            </div>
            <div>
                <h1 id="clinicName">{{ $clinicName }}</h1>
                <p>تسجيل الحجز الإلكتروني</p>
            </div>
        </header>

        <section class="card day-box" id="statusCard">
            <h2>الحجز ليوم</h2>
            <div id="bookingDate" class="booking-date">-</div>
        </section>

        <section id="messageBox" class="message-box" style="display:none;"></section>

        <section id="bookingFormCard" class="card">
            <h3>بيانات التسجيل</h3>
            <form id="bookingForm" novalidate>
                <div class="field-group">
                    <label for="patient_name">الاسم الكامل</label>
                    <input type="text" id="patient_name" name="patient_name" maxlength="100" required>
                    <small class="field-error" data-field="patient_name"></small>
                </div>

                <div class="field-group">
                    <label for="national_id">رقم الهوية</label>
                    <input type="text" id="national_id" name="national_id" inputmode="numeric" maxlength="10" required>
                    <small class="field-error" data-field="national_id"></small>
                </div>

                <div class="field-group">
                    <label for="phone">رقم الجوال</label>
                    <input type="tel" id="phone" name="phone" inputmode="tel" maxlength="10" required>
                    <small class="field-error" data-field="phone"></small>
                </div>

                <div class="field-group">
                    <label for="age">العمر</label>
                    <input type="number" id="age" name="age" min="1" max="120" required>
                    <small class="field-error" data-field="age"></small>
                </div>

                <div class="field-group">
                    <label>نوع الكشفية</label>
                    <div class="visit-type-options" id="visitTypeOptions">
                        <label class="visit-type-option" data-type="strabismus">
                            <input type="radio" name="visit_type" value="strabismus" required>
                            <span>حول</span>
                        </label>
                        <label class="visit-type-option" data-type="other">
                            <input type="radio" name="visit_type" value="other" required>
                            <span>أخرى</span>
                        </label>
                    </div>
                    <small class="field-error" data-field="visit_type"></small>
                </div>

                <div id="visitTypeNotice" class="visit-type-notice" style="display:none;"></div>

                <button type="submit" id="submitBtn" class="btn-primary">
                    <span class="btn-label">سجل الآن ←</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </form>
        </section>

        <section id="closedCard" class="card closed-box" style="display:none;">
            <h3 id="closedTitle">🔒 الحجز مغلق حالياً</h3>
            <p id="closedMessage">يرجى المحاولة لاحقاً.</p>
            <div id="nextOpenWrap" class="next-open-wrap" style="display:none;">
                <span class="next-open-label">أقرب موعد متاح</span>
                <strong id="nextOpenText">-</strong>
            </div>
        </section>

        <section id="myBookingsCard" class="card" style="display:none;">
            <h3>حالاتك المسجلة</h3>
            <div id="myBookingsList" class="my-bookings-list"></div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @include('booking.scripts')
</body>
</html>
