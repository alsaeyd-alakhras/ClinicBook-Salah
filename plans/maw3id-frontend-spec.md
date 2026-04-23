# موعد — مواصفات صفحة التسجيل (الفرونت)
**واجهة المريض — مواصفات تقنية وتصميمية كاملة**

---

## ملاحظة للتنفيذ
عند تنفيذ هذه الصفحة، استخدم `frontend-design skill` للحصول على أفضل نتيجة تصميمية.
الصفحة تُبنى بـ **HTML + CSS خالص + jQuery** — بدون Tailwind، بدون frameworks.

---

## 1. متطلبات الصفحة العامة

- **للجوال أساساً:** العرض الأمثل 360–430px
- **عند فتحها على لاب:** تبقى بعرض جوال (max-width: 480px) تلقائياً
- **لغة الصفحة:** العربية كاملاً (dir="rtl")
- **خفيفة جداً:** لا صور كبيرة، لا animations ثقيلة، تحميل سريع على نت ضعيف
- **صفحة واحدة فقط:** كل شيء في `/` بدون reload، كل تحديث بـ AJAX
- **فونت عربي:** Noto Kufi Arabic أو Cairo — من Google Fonts (stylesheet link فقط، وزن خفيف)

---

## 2. هيكل الصفحة (من الأعلى للأسفل)

```
┌─────────────────────────────────┐
│         Header (شعار + اسم)     │
├─────────────────────────────────┤
│         بوكس اليوم              │
├─────────────────────────────────┤
│    فورم التسجيل / رسالة الإغلاق │
├─────────────────────────────────┤
│     حالاتك المسجلة              │
└─────────────────────────────────┘
```

---

## 3. المكونات — تفصيل

### 3.1 Header
```
- شعار العيادة (img — صغير 60x60px max)
- اسم العيادة: "عيادة العيون - د.عمر أبو عمارة"
- خط للفصل تحته
```

---

### 3.2 بوكس اليوم
**يظهر دائماً حتى لو الحجز مغلق**

```
┌────────────────────────────────┐
│  📅  الحجز ليوم                 │
│      الأحد  27 / 4 / 2026      │
│   ● 23 حالة متبقية من أصل 65   │
└────────────────────────────────┘
```

- إذا `remaining` = 0 → "امتلأت الحالات ليوم [كذا]"
- يتحدث كل 12 ثانية بـ AJAX polling بدون reload
- لو تغيرت البيانات يتحدث البوكس بـ animation بسيطة (fade)

---

### 3.3 الفورم (يظهر عندما الحجز مفتوح)

**الحقول:**
```
1. الاسم الكامل       → text, required, maxlength=100
2. رقم الهوية        → text, required, pattern=[0-9]{9,10}
3. رقم الجوال        → tel, required, pattern=07[0-9]{8}
4. العمر             → number, required, min=1, max=120
```

**زر الإرسال:** "سجّل الآن ←"

**تسلسل UX عند الإرسال:**
```
1. Validation على الـ frontend أولاً (inline errors تحت كل حقل)
2. الزر يتعطل + يظهر spinner صغير
3. AJAX POST إلى /booking
4. النجاح:
   - رسالة خضراء في الأعلى (success toast)
   - الفورم يفرغ
   - قائمة "حالاتك المسجلة" تتحدث
   - بوكس العدد يتحدث
5. الخطأ:
   - رسالة حمراء (inline أو toast) بحسب نوع الخطأ
   - الزر يرجع للعمل
```

**رسالة النجاح (alert واضح مش مخفي):**
```
✅ تم تسجيل حجزك ليوم الأحد 27/4/2026
يرجى الحضور من الساعة 8 صباحاً لاستلام دور الفحص.
ملاحظة: لا يوجد أرقام أدوار، الفحص حسب ترتيب الحضور.
```

---

### 3.4 رسالة الإغلاق (تحل محل الفورم عندما الحجز مغلق)

```
┌────────────────────────────────────────┐
│                                        │
│   🔒  الحجز مغلق حالياً               │
│                                        │
│   يفتح يوم الإثنين الساعة 3 مساءً     │
│   للحجز ليوم الأربعاء                 │
│                                        │
└────────────────────────────────────────┘
```

- النص يأتي ديناميكياً من الـ API (`closed_message`)
- لو اليوم مغلق من الأدمن بشكل مخصص يظهر نص الإغلاق المخصص

---

### 3.5 قسم "حالاتك المسجلة"

يظهر فقط لو في حالات مسجلة من هذا الجهاز (من localStorage).

```
── حالاتك المسجلة ─────────────────────
│ 1. محمد أحمد                [اعتذار] │
│ 2. سارة علي                 [اعتذار] │
─────────────────────────────────────────
```

- يُظهر: الاسم + زر اعتذار (أحمر)
- عند الضغط على "اعتذار":
  - يظهر confirm بسيط: "هل تريد إلغاء حجز [الاسم]؟"
  - عند التأكيد: AJAX DELETE → يُحذف فوراً من القائمة
  - بوكس العدد يتحدث
- لو الإلغاء من خارج الوقت المسموح (بعد 7 صباحاً) يظهر رسالة: "لا يمكن إلغاء الحجز بعد انطلاق العيادة."

---

## 4. إدارة localStorage

```javascript
// مفتاح التخزين
const STORAGE_KEY = 'maw3id_bookings';

// الهيكل المخزَّن
{
  "bookings": [
    {
      "id": 42,              // ID من الـ DB
      "booking_date": "2026-04-27",
      "patient_name": "محمد أحمد",
      "created_at": "2026-04-26T10:30:00"
    }
  ],
  "fingerprint": "abc123xyz"  // device fingerprint
}
```

**قواعد:**
- عند كل تحميل: يُرسَل الـ fingerprint مع polling request (للتحقق من الجانب السيرفر)
- الحجوزات المخزنة: يُراجَعها مع كل polling (لو حُذف من السيرفر نحذفه من localStorage)
- الـ fingerprint: يُولَّد مرة واحدة عند أول تحميل ويبقى ثابتاً

**Device Fingerprint البسيط:**
```javascript
function generateFingerprint() {
    const data = [
        navigator.userAgent,
        navigator.language,
        screen.width + 'x' + screen.height,
        new Date().getTimezoneOffset()
    ].join('|');
    // hash بسيط
    return btoa(data).substring(0, 32);
}
```

---

## 5. AJAX Calls — تفصيل

### 5.1 Polling (كل 12 ثانية)
```javascript
GET /booking/status
Headers: X-Device-Fingerprint: {fingerprint}

Response:
{
  "booking_date": "2026-04-27",
  "booking_date_ar": "الأحد 27 / 4 / 2026",
  "is_open": true,
  "remaining": 23,
  "total": 65,
  "closed_message": null,
  "my_bookings": [   // الحجوزات المرتبطة بهذا الـ fingerprint من السيرفر
    { "id": 42, "patient_name": "محمد أحمد" }
  ]
}
```

### 5.2 إرسال الحجز
```javascript
POST /booking
{
  "patient_name": "...",
  "national_id": "...",
  "phone": "...",
  "age": 30,
  "fingerprint": "abc123xyz",
  "_token": "{csrf}"
}

Success: { "success": true, "booking": { "id": 42, "patient_name": "..." } }
Error:   { "success": false, "message": "...", "field": "national_id" }
```

### 5.3 إلغاء الحجز
```javascript
DELETE /booking/{id}
Headers: X-Device-Fingerprint: {fingerprint}

Success: { "success": true }
Error:   { "success": false, "message": "لا يمكن الإلغاء..." }
```

---

## 6. CSS — توجيهات التصميم

```css
/* متغيرات الألوان (قابلة للتعديل) */
--color-primary:    #1a6b9a;   /* أزرق طبي */
--color-primary-light: #e8f4fd;
--color-success:    #2d8a4e;
--color-danger:     #c0392b;
--color-warning:    #f39c12;
--color-text:       #2c3e50;
--color-muted:      #7f8c8d;
--color-border:     #dee2e6;
--color-bg:         #f8f9fa;
--color-card:       #ffffff;

/* Layout الرئيسي */
body → background: var(--color-bg), direction: rtl
.container → max-width: 480px, margin: 0 auto, min-height: 100vh

/* الكارد العام */
.card → background: white, border-radius: 12px, 
        box-shadow: 0 2px 8px rgba(0,0,0,0.08), margin-bottom: 16px, padding: 20px

/* الفورم */
.form-group → margin-bottom: 16px
.form-control → width: 100%, padding: 12px, border-radius: 8px,
                font-size: 16px (مهم للجوال — يمنع zoom تلقائي iOS)
.btn-primary → background: var(--color-primary), width: 100%, 
               padding: 14px, font-size: 18px, border-radius: 8px

/* رسالة الإغلاق */
.closed-box → background: #fff3cd, border: 2px solid var(--color-warning),
              border-radius: 12px, text-align: center, padding: 32px

/* قائمة الحالات */
.booking-item → display: flex, justify-content: space-between,
                padding: 12px 0, border-bottom: 1px solid var(--color-border)
.btn-cancel → color: var(--color-danger), background: transparent, 
              border: 1px solid var(--color-danger), padding: 6px 14px
```

---

## 7. قواعد مهمة للجوال

```
font-size: 16px على input → يمنع iOS auto-zoom (مهم جداً)
touch-action: manipulation → يسرّع الضغط على الأزرار
padding أزرار >= 48px height → للضغط بالإبهام راحة
لا hover effects → تبدل بـ active states
```

---

## 8. Blade View — الهيكل المقترح

```
resources/views/
├── booking/
│   └── index.blade.php          ← الصفحة الوحيدة
public/
├── css/
│   └── booking.css              ← كل الـ styles
├── js/
│   └── booking.js               ← كل الـ AJAX + logic
```

**index.blade.php يحتوي:**
- `<link>` لـ Google Fonts (Noto Kufi Arabic weight 400, 600 فقط)
- `<link>` لـ booking.css
- الـ HTML الهيكلي
- `<script>` لـ jQuery (CDN — slim version)
- `<script>` لـ booking.js
- `@csrf` meta tag في الـ head

---

## 9. تسلسل تنفيذ الصفحة

```
1. إنشاء booking.css بمتغيرات الألوان والـ layout
2. بناء الـ HTML الثابت (header + بوكس اليوم + فورم + قسم الحالات)
3. ربط الـ polling (كل 12 ثانية) وتحديث البوكس
4. منطق إظهار/إخفاء الفورم vs رسالة الإغلاق
5. إرسال الفورم بـ AJAX + validation
6. إدارة localStorage (حفظ + عرض + حذف)
7. زر الاعتذار + تأكيد الحذف
8. اختبار على جوال حقيقي (iOS + Android)
```
