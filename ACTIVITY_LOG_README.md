# نظام تتبع الأنشطة - Activity Log System (عربي/إنجليزي)

## نظرة عامة

تم تنفيذ نظام تتبع شامل للأنشطة باستخدام مكتبة `spatie/laravel-activitylog` مع دعم كامل للغتين العربية والإنجليزية لتسجيل جميع أنشطة المستخدمين في النظام.

## المميزات المنفذة

### 1. دعم متعدد اللغات

-   **واجهة عربية/إنجليزية**: جميع النصوص والرسائل تدعم اللغتين
-   **ملفات ترجمة منظمة**: `lang/ar/activity.php` و `lang/en/activity.php`
-   **تبديل تلقائي**: حسب إعدادات اللغة في النظام

### 1. تسجيل تلقائي للأنشطة

-   **تسجيل العمليات على النماذج**: إنشاء، تحديث، حذف للـ Models (User, Booking, Customer)
-   **تسجيل تسجيل الدخول والخروج**: مع تفاصيل IP والـ User Agent
-   **تسجيل محاولات تسجيل الدخول الفاشلة**
-   **تسجيل أنشطة التصفح المهمة**: عبر Middleware مخصص

### 2. واجهة إدارة احترافية

-   **صفحة عرض السجلات**: مع جدول منظم وتصميم احترافي
-   **نظام فلترة متقدم**:
    -   فلترة حسب المستخدم
    -   فلترة حسب نوع النشاط
    -   فلترة حسب نوع النموذج
    -   فلترة حسب التاريخ
    -   البحث في الوصف
-   **صفحة تفاصيل النشاط**: عرض تفصيلي لكل نشاط
-   **حذف مجمع**: إمكانية حذف عدة سجلات مرة واحدة
-   **تصدير CSV**: تصدير السجلات مع الفلاتر المطبقة

### 3. أدوات إدارية

-   **Command لتنظيف السجلات القديمة**: `php artisan activity-log:clean`
-   **مهمة مجدولة**: تنظيف تلقائي للسجلات الأقدم من 90 يوم
-   **Widget في الـ Dashboard**: عرض آخر 5 أنشطة

### 4. نظام صلاحيات

-   `view activity log`: عرض السجلات
-   `delete activity log`: حذف السجلات
-   `export activity log`: تصدير السجلات

### الملفات المضافة/المعدلة

### Language Files

-   `lang/ar/activity.php` - الترجمة العربية
-   `lang/en/activity.php` - الترجمة الإنجليزية

### Controllers

-   `app/Http/Controllers/Admin/ActivityLogController.php`

### Models & Traits

-   `app/Traits/LogsActivity.php`
-   تم تعديل: `app/Models/User.php`, `app/Models/Booking.php`, `app/Models/Customer.php`

### Middleware

-   `app/Http/Middleware/LogUserActivity.php`

### Views

-   `resources/views/admin/pages/activity-log/index.blade.php`
-   `resources/views/admin/pages/activity-log/show.blade.php`
-   `resources/views/admin/components/recent-activities.blade.php`

### Commands

-   `app/Console/Commands/CleanOldActivityLogs.php`

### Routes

-   تم إضافة routes في `routes/web.php`

### Configuration

-   `config/activitylog.php`
-   تم تعديل `bootstrap/app.php`

### Database

-   Migration: `create_activity_log_table`
-   تم تحديث `RolePermissionSeeder.php`

## كيفية الاستخدام

### 1. عرض السجلات

```
/activity-log
```

### 2. تنظيف السجلات القديمة يدوياً

```bash
php artisan activity-log:clean --days=30
```

### 3. إضافة تسجيل مخصص في الكود

```php
activity()
    ->causedBy(auth()->user())
    ->withProperties(['key' => 'value'])
    ->log('وصف النشاط');
```

### 4. إضافة تسجيل لـ Model جديد

```php
use App\Traits\LogsActivity;

class YourModel extends Model
{
    use LogsActivity;

    // باقي الكود...
}
```

## الإعدادات

### تخصيص مدة الاحتفاظ بالسجلات

في `config/activitylog.php`:

```php
'delete_records_older_than_days' => 90,
```

### تعطيل/تفعيل التسجيل

في `.env`:

```
ACTIVITY_LOGGER_ENABLED=true
```

## الأمان والأداء

-   **تنظيف تلقائي**: السجلات الأقدم من 90 يوم يتم حذفها تلقائياً
-   **فهرسة قاعدة البيانات**: جداول محسنة للاستعلامات السريعة
-   **صلاحيات محكمة**: فقط المستخدمين المخولين يمكنهم الوصول
-   **تسجيل انتقائي**: تجنب تسجيل الأنشطة غير المهمة

## التخصيص

### إضافة أنواع أنشطة جديدة

في `LogUserActivity` middleware:

```php
$patterns = [
    'your.route.name' => "وصف النشاط بواسطة {$userName}",
];
```

### تخصيص ألوان الأنشطة

في الـ views:

```php
$eventColors = [
    'your_event' => 'primary',
];
```

## الصيانة

-   **مراقبة حجم الجدول**: تأكد من تشغيل التنظيف التلقائي
-   **مراجعة الصلاحيات**: تأكد من إعطاء الصلاحيات للمستخدمين المناسبين
-   **النسخ الاحتياطي**: ضع في اعتبارك نسخ احتياطية للسجلات المهمة قبل الحذف

## الدعم الفني

للمساعدة أو الاستفسارات حول نظام تتبع الأنشطة، يرجى مراجعة:

-   [Spatie Activity Log Documentation](https://spatie.be/docs/laravel-activitylog)
-   الكود المصدري في المجلدات المذكورة أعلاه
