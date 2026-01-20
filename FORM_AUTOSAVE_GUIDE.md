# دليل حل مشاكل ضعف الإنترنت وفقدان البيانات

## المشاكل التي يحلها هذا النظام

### 1. مشكلة القوائم المنسدلة عند ضعف الإنترنت
- القوائم المنسدلة (Dropdowns) التي تُحمّل عبر AJAX لا تظهر عند ضعف الإنترنت
- عدم وجود مؤشرات تحميل واضحة للمستخدم
- عدم وجود آلية لإعادة المحاولة تلقائياً

### 2. مشكلة فقدان البيانات عند Refresh
- عند عمل Refresh للصفحة، البيانات المُدخلة تختفي
- المستخدم يضطر لإدخال كل البيانات من جديد

## الحلول المطبقة

### ✅ الحل الأول: تحسين تحميل القوائم المنسدلة

#### المميزات:
1. **مؤشر تحميل واضح**: يظهر "جاري التحميل..." أثناء تحميل البيانات
2. **إعادة المحاولة التلقائية**: يعيد المحاولة 3 مرات تلقائياً عند الفشل
3. **التخزين المؤقت (Cache)**: يحفظ البيانات في localStorage للاستخدام عند ضعف الإنترنت
4. **رسائل واضحة**: يعرض رسائل للمستخدم عن حالة التحميل

#### طريقة الاستخدام:

بدلاً من استخدام AJAX التقليدي:
```javascript
// الطريقة القديمة
$.ajax({
    url: "{{ URL::to('section') }}/" + SectionId,
    type: "GET",
    dataType: "json",
    success: function(data) {
        $('select[name="product"]').empty();
        $.each(data, function(key, value) {
            $('select[name="product"]').append('<option value="' + value + '">' + value + '</option>');
        });
    }
});
```

استخدم الدالة المحسّنة:
```javascript
// الطريقة الجديدة المحسّنة
loadSelectDataWithRetry(
    "{{ URL::to('section') }}/" + SectionId,
    'select[name="product"]',
    {
        method: 'GET',
        defaultText: 'اختر المنتج',
        retries: 3,
        timeout: 10000
    }
);
```

### ✅ الحل الثاني: الحفظ التلقائي للبيانات

#### المميزات:
1. **حفظ تلقائي**: يحفظ البيانات كل ثانيتين في localStorage
2. **استعادة تلقائية**: عند فتح الصفحة، يستعيد البيانات المحفوظة
3. **زر مسح البيانات**: يمكن للمستخدم مسح البيانات المحفوظة يدوياً
4. **حذف تلقائي**: يحذف البيانات بعد إرسال النموذج بنجاح

#### طريقة الاستخدام:

في أي صفحة تحتوي على نموذج، أضف هذا الكود في قسم `@section('js')`:

```javascript
<script>
$(document).ready(function() {
    // تفعيل الحفظ التلقائي للنموذج
    FormAutoSave.init('form', {
        excludeFields: ['password', 'password_confirmation', '_token'],
        showRestoreMessage: true
    });
});
</script>
```

### ✅ الحل الثالث: مراقبة الاتصال بالإنترنت

#### المميزات:
1. **تحذير فوري**: يظهر تحذير عند فقدان الاتصال بالإنترنت
2. **إشعار بالاستعادة**: يعلم المستخدم عند عودة الاتصال

هذا يعمل تلقائياً بدون أي إعدادات إضافية.

## أمثلة تطبيقية

### مثال 1: تحديث صفحة add_salat.blade.php

في نهاية الملف، في قسم `@section('js')`:

```javascript
@section('js')
<!-- الملفات الأخرى... -->

<script>
$(document).ready(function() {
    // تفعيل الحفظ التلقائي
    FormAutoSave.init('form', {
        showRestoreMessage: true
    });

    // تحسين تحميل القوائم المنسدلة
    $('select[name="Section"]').on('change', function() {
        var SectionId = $(this).val();
        if (SectionId) {
            loadSelectDataWithRetry(
                "{{ URL::to('section') }}/" + SectionId,
                'select[name="product"]',
                {
                    method: 'GET',
                    defaultText: 'اختر المنتج',
                    retries: 3,
                    timeout: 10000
                }
            );
        }
    });
});
</script>
@endsection
```

### مثال 2: تحديث صفحة private-wings/create.blade.php

```javascript
<script>
$(document).ready(function() {
    // تفعيل الحفظ التلقائي
    FormAutoSave.init('form', {
        showRestoreMessage: true
    });

    // تحسين تحميل التخصصات
    $('#section_id').change(function() {
        var section_id = $('#section_id').val();
        
        if (section_id) {
            loadSelectDataWithRetry(
                "{{ route('getSpecializations') }}",
                '#fckn',
                {
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        section_id: section_id
                    },
                    defaultText: '--حدد المؤسسة--',
                    valueField: 'sercode',
                    textField: 'sername',
                    retries: 3,
                    onSuccess: function() {
                        $('#doctor_id').prop('disabled', true).val('').trigger('change');
                    },
                    onError: function() {
                        $('#fckn').empty().append('<option value="" disabled selected>--حدد المؤسسة--</option>');
                    }
                }
            );
        }
    });
});
</script>
```

## خيارات الإعدادات

### خيارات loadSelectDataWithRetry:

| الخيار | الافتراضي | الوصف |
|--------|-----------|-------|
| method | 'GET' | نوع طلب AJAX |
| data | {} | البيانات المرسلة |
| loadingText | 'جاري التحميل...' | نص أثناء التحميل |
| defaultText | 'اختر...' | النص الافتراضي |
| valueField | 'id' | حقل القيمة في البيانات |
| textField | 'name' | حقل النص في البيانات |
| retries | 3 | عدد محاولات إعادة التحميل |
| timeout | 10000 | مهلة الانتظار بالميلي ثانية |
| onSuccess | null | دالة تُنفذ عند النجاح |
| onError | null | دالة تُنفذ عند الفشل |

### خيارات FormAutoSave.init:

| الخيار | الافتراضي | الوصف |
|--------|-----------|-------|
| excludeFields | [] | حقول لا نريد حفظها |
| storageKey | null | مفتاح مخصص للتخزين |
| showRestoreMessage | true | إظهار رسالة الاستعادة |
| onRestore | null | دالة تُنفذ عند الاستعادة |
| onSave | null | دالة تُنفذ عند الحفظ |

## الدوال المتاحة

### 1. showSelectLoading(selectElement)
يعرض مؤشر تحميل في القائمة المنسدلة

### 2. hideSelectLoading(selectElement, defaultText)
يخفي مؤشر التحميل ويعيد القائمة للحالة الطبيعية

### 3. loadSelectDataWithRetry(url, targetSelect, options)
تحميل بيانات القائمة المنسدلة مع إعادة المحاولة والتخزين المؤقت

### 4. showNotification(message, type)
عرض إشعار للمستخدم
- الأنواع: 'info', 'success', 'warning', 'error'

### 5. FormAutoSave.init(formSelector, options)
تفعيل الحفظ التلقائي لنموذج معين

### 6. FormAutoSave.clearFormData(storageKey)
حذف البيانات المحفوظة يدوياً

## ملاحظات مهمة

1. **الأمان**: كلمات المرور وtokens لا تُحفظ تلقائياً
2. **الذاكرة**: البيانات تُحذف تلقائياً بعد 24 ساعة
3. **التوافق**: يعمل مع Select2 وجميع أنواع الحقول
4. **الأداء**: لا يؤثر على سرعة الصفحة

## استكشاف الأخطاء

### المشكلة: البيانات لا تُحفظ
**الحل**: تأكد من أن الحقول لها attribute "name"

### المشكلة: القوائم لا تُحمّل
**الحل**: تحقق من console للأخطاء وتأكد من صحة الـ URL

### المشكلة: رسالة "localStorage is full"
**الحل**: امسح بيانات المتصفح أو استخدم excludeFields لتقليل البيانات المحفوظة

## التحديثات المستقبلية المقترحة

- [ ] إضافة تشفير للبيانات الحساسة
- [ ] دعم IndexedDB للبيانات الكبيرة
- [ ] واجهة إدارة للبيانات المحفوظة
- [ ] تصدير/استيراد البيانات

## الدعم

للمشاكل أو الاستفسارات، راجع ملف `form-autosave.js` للمزيد من التفاصيل التقنية.
