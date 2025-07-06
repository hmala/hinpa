@extends('layouts.master')

@section('css')
<link href="{{ asset('assets/css/no-permissions.css') }}" rel="stylesheet">
<style>
    /* إخفاء القائمة الجانبية */
    .sidebar, .main-sidebar, .sidemenu-open, .slide-menu, .app-sidebar, .main-menu {
        display: none !important;
        visibility: hidden !important;
    }
    
    .main-content, .app-content {
        margin-left: 0 !important;
        width: 100% !important;
    }
    
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: 'Cairo', sans-serif;
    }
    
    .no-permissions-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        text-align: center;
        padding: 2rem;
    }
    
    .card-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        max-width: 700px;
        width: 100%;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .no-permissions-icon {
        font-size: 6rem;
        color: #ff6b6b;
        margin-bottom: 2rem;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .no-permissions-title {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1rem;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }
    
    .welcome-message {
        font-size: 1.5rem;
        color: #27ae60;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
    
    .no-permissions-message {
        font-size: 1.3rem;
        color: #34495e;
        margin-bottom: 2rem;
        line-height: 1.8;
        text-align: right;
        direction: rtl;
    }
    
    .contact-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2.5rem;
        margin: 2rem 0;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    .contact-info h4 {
        color: white;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .contact-item {
        margin-bottom: 1rem;
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        text-align: right;
        direction: rtl;
    }
    
    .contact-item i {
        margin-left: 10px;
        color: #f39c12;
    }
    
    .info-alert {
        background: rgba(52, 152, 219, 0.1);
        border: 2px solid #3498db;
        border-radius: 10px;
        padding: 1.5rem;
        margin: 2rem 0;
        color: #2980b9;
        font-size: 1.1rem;
        text-align: right;
        direction: rtl;
    }
    
    .info-alert i {
        color: #3498db;
        font-size: 1.5rem;
        margin-left: 10px;
    }
    
    .logout-btn {
        margin-top: 2rem;
    }
    
    .logout-btn button {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        border: none;
        padding: 15px 30px;
        font-size: 1.2rem;
        border-radius: 25px;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
    }
    
    .logout-btn button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
    }
    
    .status-indicator {
        display: inline-block;
        padding: 8px 16px;
        background: #e74c3c;
        color: white;
        border-radius: 20px;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('page-header')
    <!-- تم إزالة breadcrumb لجعل الصفحة أكثر نظافة -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="no-permissions-container">
        <div class="card-container">
            <!-- رمز التحذير -->
            <div class="no-permissions-icon">
                <i class="fas fa-user-lock"></i>
            </div>
            
            <!-- حالة الحساب -->
            <div class="status-indicator">
                <i class="fas fa-exclamation-circle"></i>
                حساب غير مفعل
            </div>
            
            <!-- الترحيب -->
            <div class="welcome-message">
                أهلاً وسهلاً {{ Auth::user()->name }}
            </div>
            
            <!-- العنوان الرئيسي -->
            <h1 class="no-permissions-title">
                تم إنشاء حسابك بنجاح
            </h1>
            
            <!-- الرسالة الأساسية -->
            <div class="no-permissions-message">
                <p><strong>حالة الحساب:</strong> في انتظار التفعيل من قبل الإدارة</p>
                <p>تم تسجيل حسابك بنجاح في نظام وزارة الصحة، ولكن يحتاج حسابك إلى موافقة وتفعيل من قبل مسؤولي النظام قبل أن تتمكن من الوصول إلى الخدمات.</p>
                <p>يرجى التواصل مع الجهات المسؤولة لتفعيل حسابك وإعطائك الصلاحيات المناسبة.</p>
            </div>
            
            <!-- معلومات الاتصال -->
            <div class="contact-info">
                <h4><i class="fas fa-headset"></i> للحصول على الصلاحيات، يرجى التواصل مع:</h4>
                
                <div class="contact-item">
                    <i class="fas fa-building"></i>
                    <strong>الجهة:</strong> وزارة الصحة - دائرة التخطيط وتنمية الموارد
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-users-cog"></i>
                    <strong>القسم:</strong> قسم تقنية المعلومات - إدارة النظام
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <strong>البريد الإلكتروني:</strong> it-support@moh.gov.iq
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <strong>الهاتف:</strong> 0780-XXX-XXXX
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <strong>العنوان:</strong> بغداد - الباب المعظم - مجمع وزارة الصحة
                </div>
            </div>
            
            <!-- معلومات إضافية -->
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <strong>ملاحظة مهمة:</strong> 
                سيتم إشعارك عبر البريد الإلكتروني المسجل ({{ Auth::user()->email }}) عند تفعيل حسابك ومنحك الصلاحيات المطلوبة.
                <br><br>
                <strong>معلومات حسابك:</strong>
                <br>• المؤسسة: {{ Auth::user()->fck->Fckname ?? 'غير محدد' }}
                <br>• دائرة الصحة: 
                @php
                    try {
                        if(Auth::user()->moh) {
                            echo Auth::user()->moh->mohname ?? Auth::user()->moh->Mohname ?? 'غير محدد';
                        } else {
                            $mohInfo = \App\Models\mohs::where('mohcode', Auth::user()->mohcode)->first();
                            echo $mohInfo ? ($mohInfo->mohname ?? $mohInfo->Mohname ?? 'غير محدد') : 'رقم: ' . Auth::user()->mohcode;
                        }
                    } catch (\Exception $e) {
                        echo 'رقم: ' . Auth::user()->mohcode;
                    }
                @endphp
                <br>• تاريخ التسجيل: {{ Auth::user()->created_at->format('Y-m-d H:i') }}
            </div>
            
            <!-- زر تسجيل الخروج -->
            <div class="logout-btn">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // إخفاء القائمة الجانبية فوراً
    $('.sidebar, .main-sidebar, .sidemenu-open, .slide-menu, .app-sidebar, .main-menu').hide();
    $('body').removeClass('sidemenu-open').addClass('sidebar-closed');
    
    // إخفاء أي أزرار قوائم
    $('.sidemenu-toggle, .navbar-toggler, .menu-toggle, .sidebar-toggle').hide();
    
    // التأكد من أن المحتوى يأخذ العرض الكامل
    $('.main-content, .app-content, .main-container').css({
        'margin-left': '0',
        'width': '100%',
        'max-width': '100%'
    });
    
    // إضافة تأثيرات بصرية جميلة
    $('.card-container').hide().fadeIn(1200);
    
    // تأثير النبضة للأيقونة
    setInterval(function() {
        $('.no-permissions-icon i').addClass('fa-beat');
        setTimeout(function() {
            $('.no-permissions-icon i').removeClass('fa-beat');
        }, 1000);
    }, 3000);
    
    // تحديث تلقائي للصفحة كل 10 دقائق للتحقق من تفعيل الصلاحيات
    setTimeout(function() {
        window.location.reload();
    }, 600000); // 10 دقائق
    
    // رسالة ترحيب
    setTimeout(function() {
        toastr.info('مرحباً بك! سيتم تفعيل حسابك قريباً.', 'نظام وزارة الصحة', {
            positionClass: 'toast-top-center',
            timeOut: 5000,
            rtl: true
        });
    }, 1500);
});
</script>
@endsection
