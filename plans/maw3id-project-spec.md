# موعد — نظام حجز عيادة د. عمر أبو عمارة
**مشروع Laravel — مواصفات كاملة**

---

## 1. نظرة عامة

نظام حجز بسيط لعيادة عيون، يعمل على يومي عيادة أسبوعياً قابلَين للتعديل. المرضى يحجزون عبر واجهة عامة على الجوال بدون تسجيل دخول، والأدمن يتابع الحجوزات من داشبورد موجود مسبقاً.

**الـ Stack:**
- Backend: Laravel (PHP)
- Frontend: HTML + CSS + jQuery (AJAX)
- DB: MySQL
- داشبورد الأدمن: موجود مسبقاً (Laravel Breeze أو مشابه)

---

## 2. قواعد العمل الأساسية

### أيام العيادة
- يحددها الأدمن من صفحة الإعدادات (أيام الأسبوع: 0=أحد ... 6=سبت)
- حالياً: **الأحد (0) + الأربعاء (3)**
- لكل يوم عيادة: عدد حالات (افتراضي 65، قابل للتخصيص)
- يمكن إغلاق يوم بعينه بدون حذفه من القائمة

### نافذة الحجز
- تفتح: **الساعة 15:00 (3 مساءً)** من اليوم السابق ليوم العيادة الأول التالي
- تسكر بأحد شرطين:
  1. امتلاء عدد الحالات
  2. يوم العيادة نفسه الساعة **07:00 صباحاً**
- اليومان مستقلان تماماً، لا تحويل تلقائي بينهما

**مثال يومين ورا بعض:**
```
يوم الأحد (عيادة) → نافذته تفتح السبت 15:00
يوم الإثنين (عيادة) → نافذته تفتح الأحد 15:00
```
إذن بعد انتهاء يوم الأحد الساعة 07:00، نافذة الإثنين تكون مفتوحة فعلاً من الأحد 15:00.

### قواعد التسجيل
| القاعدة | التفصيل |
|---|---|
| رقم الجوال | حد أقصى حالتان لنفس اليوم |
| رقم الهوية | مرة واحدة فقط لنفس اليوم (حالة واحدة للمريض) |
| الحقول الإجبارية | الاسم الكامل + رقم الهوية + رقم الجوال + العمر |
| الاعتذار | حذف نهائي من DB + المكان يتاح فوراً |

### حماية الجهاز (3 طبقات)
1. **رقم الجوال** — الأقوى، من DB
2. **localStorage** — fingerprint بسيط يُخزَّن في المتصفح
3. **IP** — كطبقة ثالثة داعمة فقط

رسالة إذا تجاوز الجهاز الحد:
> "لقد سجلت الحد المسموح به من الحالات من هذا الجهاز. إذا كنت تريد تسجيل حالة إضافية، يرجى المحاولة من جهاز آخر."

### Race Condition
- عند الحجز: `DB::transaction` مع `SELECT ... FOR UPDATE` على جدول `clinic_days`
- آخر شخص يصل بعد الامتلاء يحصل على رسالة "امتلأت الحالات للتو" والفورم يُقفَل

---

## 3. قاعدة البيانات — Schema الكامل

### جدول `clinic_settings` (إعدادات العيادة)
```sql
id              bigint PK
key             varchar(100) UNIQUE   -- مفتاح الإعداد
value           text                  -- قيمة الإعداد (JSON أو نص)
created_at, updated_at
```

**السجلات الأساسية:**
```
clinic_name       → "عيادة العيون - د.عمر أبو عمارة"
clinic_logo       → "logo.png"
default_capacity  → 65
booking_open_hour → 15        (الساعة 3 مساءً)
booking_close_hour→ 7         (الساعة 7 صباحاً يوم العيادة)
clinic_days       → [0, 3]    (JSON: الأحد=0، الأربعاء=3)
```

---

### جدول `clinic_day_configs` (تخصيص أيام معينة)
```sql
id              bigint PK
day_of_week     tinyint(0-6)          -- 0=أحد ... 6=سبت
specific_date   date NULL             -- تاريخ محدد (nullable)
capacity        int                   -- عدد حالات مختلف لهذا اليوم
is_closed       boolean DEFAULT 0     -- إغلاق اليوم كلياً
close_message   varchar(255) NULL     -- رسالة الإغلاق للمريض
created_at, updated_at
```

**ملاحظة:** إذا `specific_date` محددة → تطبق على ذلك اليوم بالتحديد. إذا null → تطبق على كل مواعيد ذلك اليوم من الأسبوع.

---

### جدول `bookings` (الحجوزات)
```sql
id                  bigint PK
booking_date        date              -- تاريخ موعد العيادة
patient_name        varchar(100)
national_id         varchar(20)
phone               varchar(20)
age                 tinyint
device_fingerprint  varchar(255) NULL -- localStorage fingerprint
ip_address          varchar(45) NULL
serial_number       int               -- رقم تسلسلي داخلي لليوم (1، 2، 3...)
status              enum('pending','ticket_received') DEFAULT 'pending'
created_at, updated_at
```

**Unique Constraints:**
```sql
UNIQUE (booking_date, national_id)        -- هوية مرة واحدة لليوم
INDEX  (booking_date, phone)              -- لفحص حد الجوال سريعاً
INDEX  (booking_date, ip_address)
```

---

## 4. Laravel — Routes & Controllers

### Routes العامة (بدون auth)
```
GET  /                          → BookingController@show
POST /booking                   → BookingController@store
DELETE /booking/{id}            → BookingController@cancel   (بشرط: من نفس الجهاز)
GET  /booking/status            → BookingController@status   (AJAX polling - كل 12 ثانية)
```

### Routes الأدمن (مع auth middleware)
```
GET  /admin/bookings            → Admin\BookingController@index
POST /admin/bookings/{id}/confirm → Admin\BookingController@confirm
GET  /admin/bookings/export     → Admin\BookingController@export   (?type=pdf|excel&from=&to=)
GET  /admin/settings            → Admin\SettingsController@index
POST /admin/settings            → Admin\SettingsController@update
POST /admin/settings/day-config → Admin\SettingsController@updateDayConfig
```

---

## 5. Logic الأساسي — BookingService

```php
class BookingService {

    // يرجع تاريخ يوم العيادة الحالي (التالي المفتوح للحجز)
    public function getActiveBookingDate(): ?Carbon

    // يفحص هل نافذة الحجز مفتوحة الآن
    public function isBookingWindowOpen(Carbon $date): bool

    // يرجع عدد الحالات المتبقية لليوم
    public function getRemainingSlots(Carbon $date): int

    // ينجز الحجز مع transaction + lock
    public function createBooking(array $data): Booking

    // يرجع نص الرسالة المناسبة لو الحجز مغلق
    public function getClosedMessage(): string

    // يفحص حد الجوال والجهاز لليوم
    public function checkDeviceLimit(string $phone, string $fingerprint, string $ip, Carbon $date): bool
}
```

### getActiveBookingDate — المنطق:
```
1. احضر أيام العيادة من الإعدادات [0, 3]
2. لكل يوم في القائمة:
   a. احسب تاريخ أقرب يوم قادم بهذا الرقم
   b. فحص: هل نافذة الحجز له مفتوحة الآن؟
      - الفتح: يوم قبله الساعة 15:00
      - الإقفال: يومه الساعة 07:00
   c. أول يوم يجتاز الفحص → هو التاريخ الفعّال
3. لو ما في يوم مفتوح → إرجاع null + رسالة "ارجع يوم كذا الساعة 3 مساءً"
```

---

## 6. لوحة الأدمن — التفاصيل

**ملاحظة:** الداشبورد موجود مسبقاً مع نظام مستخدمين وصلاحيات. المطلوب إضافة الصفحات التالية فقط.

### 6.1 صفحة الحجوزات `/admin/bookings`

**عرض البيانات:**
- افتراضياً تعرض حجوزات أقرب يوم عيادة قادم
- الجدول: رقم تسلسلي | الاسم | الهوية | الجوال | العمر | وقت الحجز | الحالة | إجراء

**الفلترة (في نفس الصفحة — AJAX):**
- تاريخ من / تاريخ إلى (date picker يعرض فقط أيام العيادة)
- بحث بالاسم (live search)
- زر "بحث" و "إعادة تعيين"

**زر "تم استلام التذكرة":**
- يغير `status` من `pending` → `ticket_received`
- مؤشر بصري فقط (لون مختلف للصف)
- لا يؤثر على العدد أو المتاح

**تصدير التقارير:**
- زر PDF + زر Excel بجانب الفلاتر
- يصدّر الحجوزات المفلترة الظاهرة حالياً
- PDF: اسم العيادة + التاريخ + جدول بسيط
- Excel: نفس الأعمدة

**Packages المقترحة:**
```
PDF:   barryvdh/laravel-dompdf
Excel: maatwebsite/laravel-excel
```

---

### 6.2 صفحة الإعدادات `/admin/settings`

**القسم 1 — معلومات العيادة:**
- اسم العيادة
- شعار العيادة (upload)

**القسم 2 — أيام العيادة:**
- Checkboxes (أيام الأسبوع)
- لكل يوم مُختار: حقل "عدد الحالات" (افتراضي يأخذ من `default_capacity`)

**القسم 3 — طاقة استيعابية مخصصة:**
- تاريخ محدد + عدد حالات مختلف (لإضافة استثناء يوم بعينه)
- زر "إغلاق يوم" مع حقل نص رسالة الإغلاق

**القسم 4 — الإعدادات العامة:**
- الطاقة الافتراضية لكل يوم
- ساعة فتح الحجز (افتراضي: 15)
- ساعة إقفال الحجز (افتراضي: 7)

---

## 7. AJAX Polling — status endpoint

**الـ Response:**
```json
{
  "booking_date": "2026-04-27",
  "booking_date_ar": "الأحد 27/4/2026",
  "is_open": true,
  "remaining": 23,
  "total": 65,
  "closed_message": null
}
```

إذا `is_open: false`:
```json
{
  "is_open": false,
  "closed_message": "الحجز مغلق. يفتح يوم الإثنين الساعة 3 مساءً لحجز يوم الأربعاء."
}
```

---

## 8. الأمان — ملخص

| الطبقة | التطبيق |
|---|---|
| CSRF | Laravel CSRF token في كل POST |
| Rate Limiting | `throttle:10,1` على route الحجز (10 طلبات/دقيقة لكل IP) |
| Validation | رقم الهوية숫 숫숫숫숫숫숫숫숫숫 + الجوال숫숫숫숫숫숫숫숫숫 + العمر 1-120 |
| Race Condition | `DB::transaction` + `lockForUpdate()` على فحص العدد |
| Device limit | phone + fingerprint + IP مجتمعين |

---

## 9. رسائل النظام (للمريض)

| الحالة | الرسالة |
|---|---|
| تسجيل ناجح | "تم تسجيل حجزك ليوم [الأحد 27/4/2026]. يرجى الحضور من الساعة 8 صباحاً لاستلام دور الفحص. لا يوجد أرقام أدوار، الفحص حسب ترتيب الحضور." |
| نافذة مغلقة | "الحجز مغلق. يفتح يوم [الإثنين] الساعة 3 مساءً." |
| اليوم مكتمل | "عذراً، امتلأت حالات اليوم. يرجى المتابعة لمعرفة موعد فتح الحجز القادم." |
| حد الجهاز | "سجلت الحد المسموح به من هذا الجهاز. جرب من جهاز آخر." |
| هوية مكررة | "هذه الهوية محجوزة مسبقاً ليوم [كذا]." |
| تأكيد الاعتذار | "تم إلغاء الحجز بنجاح. المكان أصبح متاحاً." |

---

## 10. ترتيب التنفيذ المقترح

```
1. Migration: bookings + clinic_settings + clinic_day_configs
2. BookingService (الـ logic الأساسي)
3. Route /booking + Controller + Validation
4. صفحة التسجيل (الفرونت — مواصفاتها في الملف الثاني)
5. Admin: صفحة الحجوزات + فلترة + تأكيد
6. Admin: التصدير PDF + Excel
7. Admin: صفحة الإعدادات
8. Polling endpoint + ربطه بالفرونت
9. اختبار Race condition + Device limit
```
