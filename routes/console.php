<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
use Illuminate\Support\Facades\File;

Artisan::command('security:check', function () {
    $this->info('بدء فحص الأمان...');

    // 1. APP_DEBUG
    $debug = config('app.debug');
    $this->line('> APP_DEBUG: ' . ($debug ? 'ON (خطر!)' : 'OFF (جيد)'));

    // 2. APP_ENV
    $env = config('app.env');
    $this->line('> APP_ENV: ' . $env);

    // 3. .env Permissions
    $envPath = base_path('.env');
    if (File::exists($envPath)) {
        $permissions = substr(sprintf('%o', fileperms($envPath)), -4);
        $this->line("> .env Permissions: $permissions" . ($permissions > '0640' ? ' (يفضل تقليل الصلاحيات)' : ''));
    } else {
        $this->warn('> ملف .env غير موجود!');
    }

    // 4. فحص HTTPS
    if (config('app.url') && str_starts_with(config('app.url'), 'https://')) {
        $this->line('> الاتصال عبر HTTPS: مفعل');
    } else {
        $this->warn('> الاتصال عبر HTTPS: غير مفعل أو app.url لا يبدأ بـ https://');
    }

    // 5. CSRF Middleware
    $csrfEnabled = in_array(\App\Http\Middleware\VerifyCsrfToken::class, app(\Illuminate\Contracts\Http\Kernel::class)->getMiddlewareGroups()['web']);
    $this->line('> CSRF Protection: ' . ($csrfEnabled ? 'مفعل' : 'غير مفعل!'));

    // 6. auth Middleware
    $authMiddleware = app('router')->getMiddleware();
    $this->line('> Middleware auth: ' . (isset($authMiddleware['auth']) ? 'موجود' : 'غير موجود!'));

    // 7. إصدار Laravel
    $version = app()->version();
    $this->line("> Laravel Version: $version");

    // 8. صلاحيات مجلد storage
    $storagePath = storage_path();
    $writable = File::isWritable($storagePath);
    $this->line('> مجلد storage قابل للكتابة: ' . ($writable ? 'نعم' : 'لا (تحقّق من الصلاحيات)'));

    $this->info('تم الفحص!');
});